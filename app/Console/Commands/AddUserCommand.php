<?php

namespace App\Console\Commands;

use App\Models\department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*+----+-------------+---------------+---------+---------------------------+-------+--------+-------------+------------+-------------------+----------+----------------+---------------------+---------------------+
        | id | internet_id | department_id | role    | name                      | email | mobile | designation | department | email_verified_at | password | remember_token | created_at          | updated_at          |
        +----+-------------+---------------+---------+---------------------------+-------+--------+-------------+------------+-------------------+----------+----------------+---------------------+---------------------+
        |  1 |    20000774 |             8 | teacher | DR.  KH.  FARHAD  HOSSAIN | NULL  | NULL   | PROFESSOR   | BENGALI    | NULL              | NULL     | NULL           | 2023-11-23 15:47:07 | 2023-11-23 15:47:18 |
        +----+-------------+---------------+---------+---------------------------+-------+--------+-------------+------------+-------------------+----------+----------------+---------------------+---------------------+
        */
        $internetId = $this->ask('Internet ID ?');
        $departmentName = $this->ask('Department Name ?');

        $departments = department::where('en_name', 'like', "%$departmentName%")->get(['id', 'en_name']);
        $this->table(['id', 'en_name'], $departments->toArray());

        $departmentId = $this->ask('Department ID ?');
        $department = department::findOrFail($departmentId);

        $role = $this->ask('Role ? teacher|DepartmentChair|DepartmentManager');
        $name = $this->ask('Name ?');
        $email = $this->ask('Email ?');
        $mobile = $this->ask('Mobile ?');
        $designation = $this->ask('Designation ?');

        $user = new User();
        $user->internet_id = $internetId;
        $user->department_id = $department->id;
        $user->role = $role;
        $user->name = $name;
        $user->email = $email;
        $user->mobile = $mobile;
        $user->designation = $designation;
        $user->department = $department->en_name;
        $user->email_verified_at = Carbon::now();
        $user->save();

        $this->info('User added successfully');

    }
}
