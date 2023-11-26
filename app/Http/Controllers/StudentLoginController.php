<?php

namespace App\Http\Controllers;

use App\Models\assessment_event_student;
use App\Models\department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudentLoginController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $departments = department::all();
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
            'student_id' => 'required|numeric',
            'password' => 'required|string',
        ]);

        // student count
        $student_count = assessment_event_student::where('student_id', $request->student_id)->count();
        if ($student_count == 0) {
            return redirect()->route('student-login-form')->with('info', 'There is no course available for you to provide feedback');
        }

        $assessment_event_student = assessment_event_student::where('student_id', $request->student_id)->first();

        // check password
        $verify = $this->verify($request->student_id, $request->password);
        if ($verify['error_code'] !== 0) {
            return redirect()->route('student-login-form')->with('info', 'Wrong Student Information');
        }

        // add token
        $token = random_int(100000000, 999999999);
        Cache::put($assessment_event_student->id, $token, now()->addMinutes(120));

        // show available assessments
        return redirect()->route('assessment_event_students.assessment_events.index', ['assessment_event_student' => $assessment_event_student]);
    }

    /**
     * Verify Internet ID and Password
     *
     * @param  int $internet_id
     * @param  string $model
     * @return \Illuminate\Http\Response
     *	Response On Success
     *	====================
     *	Array
     *	(
     *	    [error_code]  => 0
     *	    [name]        => A. F. M. Mahbubur Rahman
     *	    [designation] => Lecturer
     *	    [department]  => Computer Science & Engineering
     *	)
     *
     * 	Response On Error
     * 	=================
     * 	Array
     *	(
     *	    [error_code]  => 3
     *	    [name]        => 0
     *	    [designation] => 0
     *	    [department]  => 0
     *	)
     */
    public static function verify(string $user, string $password)
    {
        $url = config('verify.url');
        $ru_user = $user;
        $ru_pass = $password;

        $request_data = array(
            "ru_user" => $ru_user,
            "ru_pass" => md5($ru_pass),
            "key"     => config('verify.key')
        );

        $data_string = base64_encode(json_encode($request_data));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $result = curl_exec($ch);

        // $result;
        if (curl_errno($ch)) {
            $msg_status = curl_strerror(curl_errno($ch));
            echo $msg_status;
        }

        $result_array = json_decode(base64_decode($result), true);

        return $result_array;
    }
}
