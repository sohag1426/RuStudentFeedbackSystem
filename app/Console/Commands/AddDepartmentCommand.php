<?php

namespace App\Console\Commands;

use App\Models\department;
use Illuminate\Console\Command;

class AddDepartmentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:add-department';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Department';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $en_name = $this->ask('English Name?');

        $department = new department();
        $department->en_name = $en_name;
        $department->save();

        $this->info('Department added successfully');
        $this->info("ID: {$department->id}");
    }
}
