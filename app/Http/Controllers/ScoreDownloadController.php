<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\comment;
use App\Models\detailed_score;
use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;
use OpenSpout\Common\Entity\Style\Color;
use OpenSpout\Common\Entity\Style\CellAlignment;
use OpenSpout\Common\Entity\Style\Style;
use OpenSpout\Common\Entity\Style\Border;
use OpenSpout\Common\Entity\Style\BorderPart;

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

        $style = (new Style())
            ->setFontBold()
            ->setFontSize(15)
            ->setShouldWrapText();

        $writer = SimpleExcelWriter::streamDownload('score.xlsx')
            ->noHeaderRow()
            ->addRow(['Course Information: '], $style)
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
                'group_average (The average feedback score for the same student group across all courses)',
                $assessment_event->group_average,
            ])
            ->addRow([
                'group_highest (The highest feedback score for the same student group across all courses)',
                $assessment_event->group_highest,
            ])
            ->addRow([
                "",
                "",
            ])
            ->addRow([
                "question",
                "score",
            ], $style);

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
        ], $style);

        foreach ($comments as $comment) {
            $writer->addRow([
                'comment' => $comment->comment,
            ]);
        }

        $writer->toBrowser();
    }
}
