<table id="data_table" class="table table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Student ID</th>
            <th scope="col">Student Name</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($student_group_members as $student_group_member)
            <tr>
                <td scope="row">{{ $student_group_member->id }}</td>
                <td>{{ $student_group_member->student_id }}</td>
                <td>{{ $student_group_member->name }}</td>
                <td>
                    @can('delete', $student_group_member)
                        <form method="POST"
                            action="{{ route('student_group_members.destroy', ['student_group_member' => $student_group_member]) }}"
                            onsubmit="return confirm('Are you sure you want to remove the item?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">DELETE</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
