<?php

namespace App\Http\Controllers;

use App\Models\question;
use Illuminate\Http\Request;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $questions = question::all();

        return view('teacher.questions', [
            'questions' => $questions,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('teacher.questions-create');
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
            'en' => 'string|required',
            'bn' => 'string|required',
        ]);

        $question = new question();
        $question->department_id = $request->user()->department_id;
        $question->en = $request->en;
        $question->bn = $request->bn;
        $question->save();

        return redirect()->route('questions.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function edit(question $question)
    {
        return view('teacher.questions-edit', [
            'question' => $question,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\question  $question
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, question $question)
    {
        $request->validate([
            'en' => 'string|required',
            'bn' => 'string|required',
        ]);

        $question->en = $request->en;
        $question->bn = $request->bn;
        $question->save();

        return redirect()->route('questions.index');
    }
}
