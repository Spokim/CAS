@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="tableDiv">
            <div class="text-center">
                <h1 class="newsTitle pt-3">Supervisor</h1>
                @if (session('error'))
                    <div class="alert alert-danger mt-3">
                        {{ session('error') }}
                    </div>
                @endif
            </div>
            <div class="row">
                <div class="col-lg-6 mb-4">
                    <h2 class="text-center">Employees</h2>
                    <div class="xxx custom-scroll">
                        <table class="table table-hover employeeTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    <th class="d-none d-sm-table-cell" scope="col">Email</th>
                                    <th scope="col">Work Shifts</th>
                                </tr>
                            </thead>
                            <tbody id="addEmployees" style="vertical-align: middle">
                                @foreach ($employees as $index => $employee)
                                    <tr>
                                        <th scope="row">{{ $employee->id }}</th>
                                        <td>{{ $employee->name }}</td>
                                        <td class="d-none d-sm-table-cell">{{ $employee->email }}</td>
                                        <td>
                                            <button class="btn btn-primary"
                                                onclick="LoadWorkShift({{ $employee->id }})">View
                                                Work Shifts</button>
                                        </td>
                                    </tr>
                                    @if ($index === 7)
                                        <tr class="load-more-placeholder"></tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-lg-6">
                    <h2 class="text-center">Work Shifts</h2>
                    <div class="zzz custom-scroll">
                        <table class="table table-striped table-hover workshiftTable">
                            <thead>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Date</th>
                                    <th scope="col">Start Time</th>
                                    <th scope="col">End Time</th>
                                    <th scope="col">Work Min</th>
                                    <th scope="col">Overtime Min</th>
                                </tr>
                            </thead>
                            <tbody id="workshiftDataInput" style="vertical-align: middle">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row grant-revoke-privileges border col-md-8 mx-auto">
            <div class="col-12 flex-col justify-content-center">
                <h2>Grant/Revoke Login Privileges</h2>
                <p>Should you have any doubts, kindly review the email in the list above. This action will be enacted
                    immediately.</p>
            </div>
            <div class="col-12 d-flex justify-content-center mb-4">
                <input type="text" placeholder="User Email" aria-label="User Email" id="user-email" name="user-email">
            </div>
            <div class="col-6 d-flex justify-content-center">
                <button id="grantLoginPrivileges" class="btn btn-primary" onclick="loginPrivileges('Grant')">
                    Grant login privileges
                </button>
            </div>
            <div class="col-6 d-flex justify-content-center">
                <button id="revokeLoginPrivileges" class="btn btn-danger" onclick="loginPrivileges('Revoke')">
                    Revoke login privileges
                </button>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script>
        const grantPrivilegesRoute = "{{ route('grant-privileges') }}"
        const revokePrivilegesRoute = "{{ route('revoke-privileges') }}"
        let nextPageUrl = "{{ $employees->nextPageUrl() }}";
        const trialURL = "{{ route('get-work-shifts') }}"
    </script>
    <script src="/js/supervisor.js"></script>
@endsection
