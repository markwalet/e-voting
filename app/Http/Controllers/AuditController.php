<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;
use ZipArchive;

/**
 * Allows users to audit the application
 */
class AuditController extends Controller
{
    private const STATUS_CMD = <<<'CMD'
    echo "THE SERVER TIME IS"
    date | ts '>'
    echo "\nGIT STATUS"
    git status | ts '>'
    echo "\nLAST THREE LOG ITEMS"
    git log -3 --pretty='commit  %H%nAuthor: %an <::EMAIL:%ae:EMAIL::>%nDate:   %aD%n%n%t' | ts '>'
    CMD;

    private const VERSION_CMD = 'git log -1 --format=\'%H\'';

    /**
     * Ensure rate limits are applied
     * @return void
     */
    public function __construct()
    {
        $this->middleware('throttle:audit-download')->only('download');
    }

    /**
     * Returns long version
     * @return string
     * @throws BindingResolutionException
     * @throws LogicException
     * @throws RuntimeException
     */
    public function getAppVersion(): string
    {
        $version = Cache::get('sys.version');
        $version = null;

        if (empty($version)) {
            // Run command
            $version = $this->runCachedCommand(self::VERSION_CMD);

            // Check if version is valid
            $version ??= 'onbekend';

            // Cache version
            Cache::put('sys.version', $version, Date::now()->addHour());
        }

        // Done
        return $version;
    }

    /**
     * Returns an audit log
     * @return View|Factory
     * @throws BindingResolutionException
     * @throws LogicException
     * @throws RuntimeException
     */
    public function index(Request $request)
    {
        // Run `git status` and `git log`
        $gitStatus = $this->runCachedCommand(self::STATUS_CMD);
        $gitVersion = $this->getAppVersion();

        // Fallbacks
        $gitStatus ??= "Git status kon niet worden opgehaald";

        // Handle emails
        $replace = $request->user() ? '\1' : '[EMAIL REDACTED]';
        $gitStatus = preg_replace('/::EMAIL:(.+?):EMAIL::/', $replace, $gitStatus);

        return response()
            ->view('audit', [
                'version' => substr($gitVersion, 0, 7),
                'status' => $gitStatus,
                'sums' => $this->getFileChecksums()
            ])
            ->setPrivate()
            ->setExpires(Date::now()->addMinutes(15));
    }

    /**
     * Runs a command and caches the response
     * @param string $cmd
     * @param null|DateTimeInterface $expire
     * @return null|string
     */
    private function runCachedCommand(string $cmd, ?DateTimeInterface $expire = null): ?string
    {
        // Determine cache key
        $cacheKey = sprintf('sys-proc.%s', md5($cmd));

        // Check cache
        if (Cache::has($cacheKey) && App::environment('production')) {
            return Cache::get($cacheKey);
        }

        // Prep a process
        $proc = Process::fromShellCommandline($cmd, \base_path());
        $proc->enableOutput();

        // Get output
        $result = '';

        // Run it
        try {
            $proc->run();
        } catch (ProcessTimedOutException | ProcessSignaledException $error) {
            return null;
        }

        // Get the output
        $result = trim($proc->getOutput());

        // Save to cache
        $expire ??= Date::now()->addMinutes(15);
        Cache::put($cacheKey, $result, $expire);

        // Done
        return $result;
    }

    /**
     * Returns all file checksums
     * @return array
     * @throws BindingResolutionException
     * @throws LogicException
     * @throws RuntimeException
     */
    private function getFileChecksums(): array
    {
        // Get files
        $files = $this->runCachedCommand('git ls-files');
        if (!$files) {
            return [];
        }

        // Get checksums
        $cmd = ['shasum', '--tag', ...array_map('escapeshellarg', explode(PHP_EOL, $files))];
        $result = $this->runCachedCommand(implode(' ', $cmd));
        if (!$result) {
            return [];
        }

        // Format checksums
        $sums = explode(PHP_EOL, $result);
        sort($sums);
        $out = [];
        foreach ($sums as $checksum) {
            if (preg_match('/^SHA1 \((.+?)\) = ([a-f0-9]{8,64})$/', $checksum, $matches)) {
                $out[$matches[1]] = $matches[2];
            }
        }

        // Done
        return $out;
    }
    /**
     * Downloads the active source code
     */
    public function download(): BinaryFileResponse
    {
        // Get version
        // Get a list of files
        $files = $this->runCachedCommand('git ls-files');

        // Fail with server error if no file list
        if (empty($files) || Str::startsWith($files, 'Command failed')) {
            throw new ServiceUnavailableHttpException(5, 'Kan niet communiceren met Git');
        }

        // Prep a tempfile
        $zipFile = \storage_path(sprintf("audit-%s.zip", Str::uuid()));
        touch($zipFile);

        // Prep a zipfile
        $zip = new ZipArchive();

        // Add all files in the given dirs
        $files = collect(explode(PHP_EOL, $files));
        foreach ($files->chunk(25) as $fileChunk) {
            // Open the zipfile
            if ($zip->open($zipFile) !== true) {
                @unlink($zipFile);
                throw new ServiceUnavailableHttpException(5, 'Kan zip-bestand niet aanmaken');
            }

            // Add each file
            foreach ($fileChunk as $file) {
                $path = \base_path($file);
                if (\file_exists($path) && is_file($path)) {
                    $zip->addFile($file, realpath($path));
                }
            }

            // Close the zip file after the chunk
            if ($zip->close() !== true) {
                @unlink($zipFile);
                throw new ServiceUnavailableHttpException(5, 'Kan zip-bestand niet sluiten');
            }
        }

        // Check file
        if (!\file_exists($zipFile)) {
            throw new NotFoundHttpException('Kan zip-bestand niet meer vinden');
        }

        // Send file
        return \response()
            ->file($zipFile, ['Content-Type' => 'application/zip'])
            ->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'application-code.zip');
    }
}
