@foreach ($employees as $index => $employee)
    <tr>
        <th scope="row">{{ $employee->id }}</th>
        <td>{{ $employee->name }}</td>
        <td class="d-none d-sm-table-cell">{{ $employee->email }}</td>
        <td>
            <button class="btn btn-primary" onclick="LoadWorkShift({{ $employee->id }})">View Work Shifts</button>
        </td>
    </tr>

    @if ($index === 7)
        <tr class="load-more-placeholder"></tr>
    @endif
@endforeach
