<div>
    <form class="d-flex align-content-start flex-wrap " action="{{ route('admin-dashboard') }}" method="get">

        {{-- department_id --}}
        <div class="form-group col-md-4">
            <select name="department_id" id="department_id" class="form-control" wire:model.live="department_id">
                @if ($selectedDepartment)
                    <option value='{{ $selectedDepartment->id }}' @selected(true)>
                        {{ $selectedDepartment->en_name }}</option>
                @else
                    <option value=''>Department...</option>
                @endif

                @foreach ($departments->sortBy('en_name') as $department)
                    <option value="{{ $department->id }}">
                        {{ $department->en_name }}
                        (Feedback Event Count: {{ $department->events()->count() }})
                    </option>
                @endforeach
            </select>
        </div>
        {{-- department_id --}}

        {{-- group_id --}}
        <div class="form-group col-md-4">
            <select name="group_id" id="group_id" class="form-control">
                <option value=''>Student Group...</option>
                @if ($student_groups)
                    @foreach ($student_groups as $student_group)
                        <option value="{{ $student_group->id }}">
                            {{ $student_group->name }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
        {{-- group_id --}}

        <div class="form-group col-md-2">
            <button type="submit" class="btn btn-dark">
                FILTER
            </button>
        </div>

    </form>
</div>
