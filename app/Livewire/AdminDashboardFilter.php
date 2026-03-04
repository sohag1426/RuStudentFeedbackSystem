<?php

namespace App\Livewire;

use App\Models\student_group;
use Livewire\Component;

class AdminDashboardFilter extends Component
{
    public $selectedDepartment;

    public $departments;

    public $department_id;

    public $student_groups;

    public function render()
    {
        return view('livewire.admin-dashboard-filter');
    }

    public function mount($selectedDepartment, $departments)
    {
        $this->selectedDepartment = $selectedDepartment;
        $this->departments = $departments;
    }

    public function updated($name, $value)
    {
        if ($name == 'department_id') {
            $this->student_groups = student_group::where('department_id', $value)->get();
        }
    }
}
