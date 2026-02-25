<?php

namespace App\Http\Controllers;

use App\Models\assessment_event;
use App\Models\comment;
use App\Models\detailed_score;
use App\Models\questions_group;
use Illuminate\Http\Request;
use OpenSpout\Common\Entity\Style\Style;
use Spatie\SimpleExcel\SimpleExcelWriter;

class ScoreDownloadController extends Controller
{
    /**
     * Handle the incoming request.
     *
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
                $assessment_event->course->name.'('.$assessment_event->course->code.')',
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
                '',
                '',
            ])
            ->addRow([
                'question',
                'score',
            ], $style);

        $detailed_scores = detailed_score::with('question')->where('event_id', $assessment_event->id)->get();

        foreach ($detailed_scores as $detailed_score) {
            $writer->addRow([
                'question' => $detailed_score->question->en,
                'score' => $detailed_score->score,
            ]);
        }

        $writer->addRow([
            '',
            '',
        ]);

        $writer->addRow([
            'Questions Group',
        ], $style);

        $groups = $detailed_scores->groupBy('questions_group_id');

        foreach ($groups as $questionsGroupId => $group) {
            $questionsGroup = questions_group::find($questionsGroupId);
            if (! $questionsGroup) {
                continue;
            }

            $questionsCount = $group->count();
            if ($questionsCount == 0) {
                continue;
            }

            $totalScore = $group->sum('score');
            $averageScore = round($totalScore / $questionsCount, 2);

            $writer->addRow([
                $questionsGroup->en_name,
                $averageScore,
            ]);
        }

        /*
        $comments = comment::where('event_id', $assessment_event->id)->get();
        $writer->addRow([
            '',
            '',
        ]);

        $writer->addRow([
            'comments',
        ], $style);

        foreach ($comments as $comment) {
            $writer->addRow([
                'comment' => $comment->comment,
            ]);
        }
        */

        $writer->toBrowser();
    }
}
