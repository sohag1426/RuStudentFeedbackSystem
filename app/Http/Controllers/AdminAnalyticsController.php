<?php

namespace App\Http\Controllers;

use App\Enums\Semester;
use App\Enums\Year;
use App\Models\AssessmentEvent;
use App\Models\Department;
use App\Services\SessionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class AdminAnalyticsController extends Controller
{
    /**
     * Display the analytics filter form and filtered assessment events.
     */
    public function index(Request $request)
    {
        $departments = Department::orderBy('en_name')->get();
        $years = Year::cases();
        $semesters = Semester::cases();

        $existingSessions = AssessmentEvent::whereNotNull('session')
            ->where('session', '!=', '')
            ->distinct()
            ->pluck('session')
            ->toArray();
        $serviceSessions = SessionService::getSessions();
        $sessions = array_values(array_unique(array_merge($serviceSessions, $existingSessions)));
        rsort($sessions);

        $hasFilters = $this->hasActiveFilters($request);
        $events = null;

        if ($hasFilters) {
            $query = $this->buildFilterQuery($request);
            $events = $query->paginate(50)->withQueryString();
        }

        return view('admin.analytics.index', compact(
            'departments',
            'years',
            'semesters',
            'sessions',
            'events',
            'hasFilters'
        ));
    }

    /**
     * Download the filtered assessment events as PDF.
     */
    public function download(Request $request)
    {
        if (! $this->hasActiveFilters($request)) {
            return redirect()->route('admin-analytics.index')
                ->with('error', 'Please apply at least one filter before downloading the report.');
        }

        $query = $this->buildFilterQuery($request);
        $events = $query->get();

        foreach ($events as $event) {
            if ($event->score === 'undefined' || $event->feedback_percentage == 0) {
                \App\Services\ScoreService::generateScore($event);
            }
        }

        $filters = $this->getFilterSummary($request);

        $pdf = Pdf::loadView('admin.analytics.pdf', compact('events', 'filters'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('analytics-report-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Build the query according to the filter parameters.
     */
    private function buildFilterQuery(Request $request)
    {
        $query = AssessmentEvent::query()
            ->with(['department', 'teacher', 'course', 'group']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('session')) {
            $query->where(function ($q) use ($request) {
                $q->where('session', $request->session)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('session')
                            ->whereHas('group', fn ($g) => $g->where('session', $request->session));
                    });
            });
        }

        if ($request->filled('year')) {
            $query->where(function ($q) use ($request) {
                $q->where('year', $request->year)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('year')
                            ->whereHas('group', fn ($g) => $g->where('year', $request->year));
                    });
            });
        }

        if ($request->filled('semester')) {
            $query->where(function ($q) use ($request) {
                $q->where('semester', $request->semester)
                    ->orWhere(function ($q2) use ($request) {
                        $q2->whereNull('semester')
                            ->whereHas('group', fn ($g) => $g->where('semester', $request->semester));
                    });
            });
        }

        if ($request->filled('start_time')) {
            $query->whereDate('start_time', '>=', $request->start_time);
        }

        if ($request->filled('stop_time')) {
            $query->whereDate('stop_time', '<=', $request->stop_time);
        }

        if ($request->filled('score')) {
            $op = in_array($request->score_operator, ['=', '<', '>', '<=', '>=']) ? $request->score_operator : '=';
            $query->where('score', '!=', 'undefined')
                ->whereRaw('CAST(score AS DECIMAL(5,2)) ' . $op . ' ?', [(float) $request->score]);
        }

        if ($request->filled('feedback_percentage')) {
            $op = in_array($request->feedback_percentage_operator, ['=', '<', '>', '<=', '>=']) ? $request->feedback_percentage_operator : '=';
            $query->where('feedback_percentage', $op, (float) $request->feedback_percentage);
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Build readable filter summary for the PDF report.
     */
    private function getFilterSummary(Request $request): array
    {
        $filters = [];

        if ($request->filled('department_id')) {
            $dept = Department::find($request->department_id);
            $filters['Department'] = $dept ? $dept->en_name : $request->department_id;
        }

        if ($request->filled('session')) {
            $filters['Session'] = $request->session;
        }

        if ($request->filled('year')) {
            $filters['Year'] = $request->year;
        }

        if ($request->filled('semester')) {
            $filters['Semester'] = $request->semester;
        }

        if ($request->filled('start_time')) {
            $filters['Start Date (from)'] = $request->start_time;
        }

        if ($request->filled('stop_time')) {
            $filters['Stop Date (to)'] = $request->stop_time;
        }

        if ($request->filled('score')) {
            $op = in_array($request->score_operator, ['=', '<', '>', '<=', '>=']) ? $request->score_operator : '=';
            $filters['Score'] = $op . ' ' . $request->score;
        }

        if ($request->filled('feedback_percentage')) {
            $op = in_array($request->feedback_percentage_operator, ['=', '<', '>', '<=', '>=']) ? $request->feedback_percentage_operator : '=';
            $filters['%Feedback'] = $op . ' ' . $request->feedback_percentage . '%';
        }

        return $filters;
    }

    /**
     * Determine whether any filter criteria is active in the request.
     */
    private function hasActiveFilters(Request $request): bool
    {
        return $request->filled('department_id')
            || $request->filled('session')
            || $request->filled('year')
            || $request->filled('semester')
            || $request->filled('start_time')
            || $request->filled('stop_time')
            || $request->filled('score')
            || $request->filled('feedback_percentage');
    }
}
