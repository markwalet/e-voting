<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\AuditController;
use App\Models\ArchivedResults;
use App\Models\Poll;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Ods;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use UnderflowException;

class PollController extends AdminController
{
    /**
     * List all votes
     * @param Request $request
     * @return void
     */
    public function index()
    {
        // Return
        return \response()->view('admin.polls.list');
    }

    /**
     * Store the new poll
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        // Validate
        $valid = $request->validate([
            'title' => [
                'required',
                'string',
                'min:2',
                'max:250'
            ]
        ]);

        // Make it
        Poll::create($valid);

        // Set
        $this->sendNotice('De nieuwe peiling is aangemaakt.');

        // Return
        return \redirect()
            ->back();
    }

    /**
     * Sends the results as a long sheet
     * @param Poll $poll
     * @return mixed
     * @throws AuthorizationException
     */
    public function download(Poll $poll)
    {
        // Check request
        $this->authorize('download', $poll);

        // Check if the poll has stored data
        if (!$poll->results) {
            throw new NotFoundHttpException(
                'De uitslagen voor deze stemming zijn niet beschikbaar'
            );
        }

        // Determine file name
        $version = App::make(AuditController::class)->getAppVersion();
        $expectedFile = "exports/{$version}/vote-{$poll->id}.ods";

        // Check if the file exists
        if (
            !Storage::exists($expectedFile) &&
            !$this->createSheet($poll, $expectedFile)
        ) {
            throw new NotFoundHttpException(
                'De uitslagen voor deze stemming konden niet omgezet worden in een ODS-bestand'
            );
        }

        return Storage::download($expectedFile, sprintf(
            'Uitslagen stemming %d - %s.ods',
            $poll->id,
            Str::ascii($poll->title, 'nl')
        ));
    }

    /**
     * Creates a sheet for the given poll's data
     */
    private function createSheet(Poll $poll, string $filename): bool
    {
        // Safety check
        if (!$poll->results || !$poll->results instanceof ArchivedResults) {
            throw new UnderflowException('Poll does not contain data');
        }

        // Create the new sheet
        $spreadsheet = new Spreadsheet();

        // Set some metadata
        $spreadsheet->getProperties()
            ->setCreator("Gumbo Millennium e-voting")
            ->setTitle("Uitslagen stemming {$poll->title} van {$poll->ended_at->format('d-m-Y')}")
            ->setSubject("Stemming {$poll->title}")
            ->setKeywords("alv gumbo stemming vote")
            ->setCategory("Uitslagen stemming");

        // Get first sheet
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Uitslagen');

        // Get data
        $results = $poll->results;
        \assert($results instanceof ArchivedResults);

        // Add meta
        $this->writeTable($sheet, 2, 2, [
            [null, 'datum', 'leden'],
            ['aanvang', $poll->started_at, $results->startVotes],
            ['sluiting', $poll->ended_at, $results->endVotes],
        ]);

        // Add votes
        $this->writeTable($sheet, 7, 2, [
            ['optie', 'stemmen'],
            ['Voor', $results->results->favor],
            ['Tegen', $results->results->against],
            ['Onthouding', $results->results->blank],
        ]);

        // Add header
        $this->writeTable($sheet, 1, 7, [
            ['Datum', 'Stem']
        ]);

        // Add data below
        $this->writeTable($sheet, 1, 8, $results->results->votes);

        // Lock the first few lines
        $sheet->freezePaneByColumnAndRow(0, 7);

        // Get a temp handle
        $tempFile = \tempnam(\sys_get_temp_dir(), 'ods-');

        // Write to file
        $writer = new Ods($spreadsheet);
        $writer->save($tempFile);

        // Get temp file object
        $tempFileObject = new File($tempFile);

        // Sync to storage
        $path = Storage::putFileAs(
            \dirname($filename),
            $tempFileObject,
            \basename($filename)
        );

        return $path !== false;
    }

    /**
     * Writes the given data
     * @param Worksheet $sheet
     * @param int $x
     * @param int $y
     * @param array $data
     * @return void
     */
    public function writeTable(Worksheet &$sheet, int $x, int $y, array $data)
    {
        foreach ($data as $rowId => $row) {
            foreach ($row as $cellId => $cell) {
                $sheet->setCellValueByColumnAndRow($x + $cellId, $y + $rowId, $cell);
            }
        }
    }
}
