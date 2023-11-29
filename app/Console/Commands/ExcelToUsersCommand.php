<?php

namespace App\Console\Commands;

use App\Models\department;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Spatie\SimpleExcel\SimpleExcelReader;

class ExcelToUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:excel-to-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Users From Excel';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // NETWORK ENGINEER added as a Tester of the application
        $designationsKeyWords = [
            'LECTURER',
            'PROFESSOR',
            'CHANCELLOR',
            'TREASURER',
            'NETWORK ENGINEER'
        ];

        $pathToFile = $this->ask('Excel File Location ?');
        SimpleExcelReader::create($pathToFile)
            ->trimHeaderRow()
            ->getRows()
            ->each(function (array $rowProperties) use ($designationsKeyWords) {

                // Uniqueness Filter | No New ID
                if (User::where('internet_id', $rowProperties['emp_idno'])->count()) {
                    return;
                }

                // Filter Teacher | Only Teacher
                $contains = Str::contains(strtoupper($rowProperties['emp_degn']), $designationsKeyWords);
                if ($contains == false) {
                    return;
                }

                $user = new User();
                $user->internet_id = $rowProperties['emp_idno'];
                $user->name = $rowProperties['emp_name'];
                $user->designation = $rowProperties['emp_degn'];
                $user->department = $rowProperties['emp_dept'];
                $user->save();
            });

        // department_id
        $users  = User::all();
        $departments =  $users->groupBy('department')->keys()->sort()->unique();
        foreach ($departments as $dept) {
            // Only New Department
            if (department::where('en_name', $dept)->count() == 0) {
                $department = new department();
                $department->en_name = $dept;
                $department->save();
                User::where('department', $dept)
                    ->update(['department_id' => $department->id]);
            }
        }
    }
}
