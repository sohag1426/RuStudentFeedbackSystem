<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\SimpleExcel\SimpleExcelWriter;

class SampleExcel extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        SimpleExcelWriter::streamDownload('sample-students.xlsx')
            ->addRow([
                'student_id' => '12345678',
                'name' => 'Jane',
            ])
            ->addRow([
                'student_id' => '23456789',
                'name' => 'Doe',
            ])
            ->toBrowser();
    }
}
