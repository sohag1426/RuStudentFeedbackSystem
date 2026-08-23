<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEvent;
use App\Models\AssessmentEventStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelReader;

class AssessmentEventStudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function index(AssessmentEvent $assessment_event)
    {
        $students = AssessmentEventStudent::where('event_id', $assessment_event->id)->get();

        return view('teacher.event_students', [
            'assessment_event' => $assessment_event,
            'students' => $students,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function create(AssessmentEvent $assessment_event)
    {
        return view('teacher.event-students-create', [
            'assessment_event' => $assessment_event,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, AssessmentEvent $assessment_event)
    {

        $request->validate([
            'excel_file' => ['required', 'file', 'mimes:xlsx,xls'],
        ]);

        $file = $request->file('excel_file')->store('group_members');

        $path = Storage::path($file);

        $rows = SimpleExcelReader::create($path)->getRows()->toArray();

        foreach ($rows as $row) {
            $keys_found = array_keys($row);
            $expected_keys = [
                'student_id',
                'name',
            ];
            $key_diff = array_diff($expected_keys, $keys_found);
            if (count($key_diff)) {
                continue;
            }
            AssessmentEventStudent::updateOrCreate(
                ['event_id' => $assessment_event->id, 'department_id' => $assessment_event->department_id, 'student_id' => trim((string) $row['student_id'])],
                ['name' => trim((string) $row['name'])]
            );
        }

        Storage::delete($file);

        return redirect()->route('assessment_events.assessment_event_students.index', ['assessment_event' => $assessment_event]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AssessmentEvent  $assessment_event
     * @param  \App\Models\AssessmentEventStudent  $assessment_event_student
     * @return \Illuminate\Http\Response
     */
    public function destroy(AssessmentEvent $assessment_event, AssessmentEventStudent $assessment_event_student)
    {
        $assessment_event_student->delete();
        return redirect()->route('assessment_events.assessment_event_students.index', ['assessment_event' => $assessment_event]);
    }
}
