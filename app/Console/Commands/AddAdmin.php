<?php

namespace App\Console\Commands;

use App\Mail\AccountCreatedOnTeacherAssessmentSystem;
use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AddAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-admin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add-Admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = new Admin();
        $user->role = 'admin';
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
    }
}
