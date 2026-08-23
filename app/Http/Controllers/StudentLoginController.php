<?php

namespace App\Http\Controllers;

use App\Models\AssessmentEventStudent;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class StudentLoginController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = Department::all();
        return view('student.student-login', [
            'departments' => $departments,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|string',
            'password' => 'required|string',
        ]);

        // student count
        $student_count = AssessmentEventStudent::where('student_id', $request->student_id)->count();
        if ($student_count == 0) {
            return redirect()->route('student-login-form')
                ->withInput($request->only('student_id'))
                ->with('error', 'There is no course available for you to provide feedback.');
        }

        $assessment_event_student = AssessmentEventStudent::where('student_id', $request->student_id)->first();

        // check password
        $verify = $this->verify($request->student_id, $request->password);
        if (! isset($verify['error_code']) || $verify['error_code'] !== 0) {
            return redirect()->route('student-login-form')
                ->withInput($request->only('student_id'))
                ->with('error', 'Invalid Student ID or Password. Please try again.');
        }

        // add session and cache token
        $token = Str::random(40);
        Cache::put('student_token_' . $assessment_event_student->id, $token, now()->addMinutes(120));
        $request->session()->put('student_auth_token_' . $assessment_event_student->id, $token);

        // show available assessments
        return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student]);
    }

    /**
     * Verify Internet ID and Password
     *
     * @param  string $user
     * @param  string $password
     * @return array
     */
    public static function verify(string $user, string $password): array
    {
        try {
            $url = config('verify.url');
            $request_data = [
                'ru_user' => $user,
                'ru_pass' => $password,
                'key'     => config('verify.key'),
            ];

            $data_string = base64_encode(json_encode($request_data));

            $response = Http::timeout(10)
                ->withBody($data_string, 'text/plain')
                ->post($url);

            if ($response->successful()) {
                $decoded = json_decode(base64_decode($response->body()), true);
                if (is_array($decoded) && isset($decoded['error_code'])) {
                    return $decoded;
                }
            }
        } catch (\Throwable $th) {
            Log::error('Student verification error: ' . $th->getMessage());
        }

        return [
            'error_code' => 1,
            'message' => 'Verification failed',
        ];
    }
}
