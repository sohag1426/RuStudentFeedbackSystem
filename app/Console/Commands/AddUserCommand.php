<?php

namespace App\Console\Commands;

use App\Mail\AccountCreatedOnTeacherAssessmentSystem;
use App\Models\department;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AddUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add New User';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->table(
            ['id', 'en_name'],
            department::all(['id', 'en_name'])->toArray()
        );

        $user = new User();
        $user->department_id = $this->ask('department_id?');
        $user->role = 'teacher';
        $user->name = $this->ask('name?');
        $user->email = $this->ask('email?');
        $user->email_verified_at = Carbon::now();
        $password = $this->ask('password?');
        $user->password = Hash::make($password);
        $user->save();

        $this->info('User added successfully');

        if ($this->confirm('Do you wish to email access information to the user?')) {
            Mail::to($user)->send(new AccountCreatedOnTeacherAssessmentSystem($user, $password));
        }

        return Command::SUCCESS;
    }
}
