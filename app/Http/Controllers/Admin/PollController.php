<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Models\ArchivedResults;
use App\Models\Poll;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
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
        $expectedFile = "exports/vote-{$poll->id}.odt";

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
            'Uitslagen stemming %d - %s.odt',
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

        // Add prime meta
        $headerData = [
            [],
            [null, null, 'datum', 'leden'],
            [null, 'aanvang', $poll->started_at, $results->startVotes],
            [null, 'sluiting', $poll->ended_at, $results->endVotes],
            [],
            ['ID', 'Datum', 'Stem']
        ];

        // Apply data
        foreach ($headerData as $rowId => $row) {
            foreach ($row as $cellId => $cell) {
                $sheet->setCellValueByColumnAndRow($cellId + 1, $rowId + 1, $cell);
            }
        }

        // Get offset of data
        $offset = count($headerData);

        // Add data below
        $votes = $results->results;
        foreach ($votes->votes as $rowId => $vote) {
            foreach ($vote as $cellId => $cell) {
                $sheet->setCellValueByColumnAndRow($cellId + 1, $offset + $rowId + 1, $cell);
            }
        }

        // Lock the first few lines
        $sheet->freezePaneByColumnAndRow(0, $offset);

        // Get a temp handle
        $tempFile = \tempnam(\sys_get_temp_dir(), 'ods-');

        // Write to file
        $writer = new Xlsx($spreadsheet);
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
}
