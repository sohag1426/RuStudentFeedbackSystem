<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reset-admin-password';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Admin Password';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $id = $this->ask('Admin ID?');
        $admin = Admin::findOrFail($id);
        $this->info("Role: {$admin->role}"." Name: {$admin->name}"." Email: {$admin->email}");
        $password = $this->ask('Password?');
        $admin->password = Hash::make($password);
        $admin->save();
        $this->info('Password reset successfully');
    }
}
