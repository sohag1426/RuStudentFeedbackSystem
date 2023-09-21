<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\comment;
use App\Models\detailed_score;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Writer\Common\Creator\Style\StyleBuilder;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ScoreDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request, assessment_event $assessment_event)
    {
        $this->authorize('downloadReport', [$assessment_event]);

        // $style = (new StyleBuilder())
        //     ->setFontBold()
        //     ->setFontSize(15)
        //     ->setFontColor(Color::BLACK)
        //     ->setShouldWrapText()
        //     ->setBackgroundColor(Color::WHITE)
        //     ->build();

        $writer = SimpleExcelWriter::streamDownload('score.xlsx')
            ->noHeaderRow()
            ->addRow([
                'department',
                $assessment_event->department->en_name,
            ])
            ->addRow([
                'teacher',
                $assessment_event->teacher->name,
            ])
            ->addRow([
                'course',
                $assessment_event->course->name,
            ])
            ->addRow([
                'score',
                $assessment_event->score,
            ])
            ->addRow([
                'group_average',
                $assessment_event->group_average,
            ])
            ->addRow([
                'group_highest',
                $assessment_event->group_highest,
            ])
            ->addRow([
                "",
                "",
            ])
            ->addRow([
                "question",
                "score",
            ]);

        $detailed_scores = detailed_score::with('question')->where('event_id', $assessment_event->id)->get();

        foreach ($detailed_scores as $detailed_score) {
            $writer->addRow([
                'question' => $detailed_score->question->en,
                'score' => $detailed_score->score,
            ]);
        }

        $comments = comment::where('event_id',  $assessment_event->id)->get();
        $writer->addRow([
            "",
            ""
        ]);

        $writer->addRow([
            "comments",
        ]);

        foreach ($comments as $comment) {
            $writer->addRow([
                'comment' => $comment->comment,
            ]);
        }

        $writer->toBrowser();
    }
}
