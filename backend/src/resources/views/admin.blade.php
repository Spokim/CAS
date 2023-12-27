@extends('layouts.app')

@section('content')
    <div class="container mb-4">
        <div class="row grant-revoke-privileges border">
            <div class="col-12 flex-col justify-content-center">
                <h2>Grant/Revoke Supervisor Privileges</h2>
                <p>Should you have any doubts, kindly review the email. This action will be enacted
                    immediately.</p>
            </div>
            <div class="col-12 d-flex justify-content-center mb-4"> <input type="text" placeholder="User Email"
                    aria-label="User Email" id="user-email" name="user-email"></div>
            <div class="col-6 d-flex justify-content-center"><button id="grantPrivileges" class="btn btn-primary"
                    onclick="supervisorPrivileges('Grant')">Grant supervisor privileges</button></div>
            <div class="col-6 d-flex justify-content-center"><button id="revokePrivileges" class="btn btn-danger"
                    onclick="supervisorPrivileges('Revoke')">Revoke supervisor privileges</button></div>
        </div>
        <div class="tableDiv pt-4">
            <h2 class="text-center">Transmit Work Shift Data to Designated Address</h2>
            <div class="col-8 mx-auto">
                <p>This data contains sensitive information and will be exclusively dispatched to the pre-registered
                    address.
                </p>
                <p>Kindly select the necessary dates in the fields below.</p>
            </div>
            <div class="col-6 mx-auto flex-column">
                <div class="d-flex mb-3 justify-content-around">
                    <div class="me-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="startDate"
                            max="{{ now()->format('Y-m-d') }}" required>
                        <div class="invalid-feedback" id="timeError1">
                            Please enter a valid date.
                        </div>
                        <div class="invalid-feedback" id="dateError1">
                            Start date must be before end date.
                        </div>
                    </div>
                    <div>
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate"
                            max="{{ now()->format('Y-m-d') }}" required>
                        <div class="invalid-feedback" id="timeError2">
                            Please enter a valid date.
                        </div>
                        <div class="invalid-feedback" id="dateError2">
                            Start date must be before end date.
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center">
                    <button id="transmitData" class="btn btn-primary" onclick="transmitData()">Send Data</button>
                </div>
            </div>
        </div>
    </div>
    <script src="/js/elementLoad.js"></script>
    <script>
        const userPrivileges = document.getElementById('user-email');
        const supervisorPrivileges = (action) => {
            if (!userPrivileges.value) {
                alert('Please enter a valid email address.');
                return;
            }
            const meta = document.querySelector('meta[name="csrf-token"]');
            if (action === 'Grant') {
                ElementLoad('#grantPrivileges')
                $.ajax({
                    url: "{{ route('grant-supervisor-privileges') }}",
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': meta.content
                    },
                    data: {
                        email: userPrivileges.value,
                    },
                    success: function(response) {
                        userPrivileges.value = '';
                        alert(response.success);
                        ElementLoadReset('#grantPrivileges', 'Grant supervisor privileges');
                    },
                    error: function(xhr, status, error) {
                        alert('Error: ' + xhr.responseJSON.error)
                        ElementLoadReset('#grantPrivileges', 'Grant supervisor privileges');
                    },
                });
            }
            if (action === 'Revoke') {
                ElementLoad('#revokePrivileges')
                $.ajax({
                        url: "{{ route('revoke-supervisor-privileges') }}",
                        type: 'post',
                        headers: {
                            'X-CSRF-TOKEN': meta.content
                        },
                        data: {
                            email: userPrivileges.value,
                        },
                    })
                    .then(response => {
                        userPrivileges.value = '';
                        alert(response.success);
                        ElementLoadReset('#revokePrivileges', 'Revoke supervisor privileges');
                    })
                    .catch(error => {
                        alert('Error: ' + error.responseJSON.error)
                        ElementLoadReset('#revokePrivileges', 'Revoke supervisor privileges');
                    });
            }
        }
    </script>
    <script>
        const transmitData = () => {
            $('#timeError1').hide();
            $('#timeError2').hide();
            $('#dateError1').hide();
            $('#dateError2').hide();
            const startDate = document.querySelector('#startDate').value;
            const endDate = document.querySelector('#endDate').value;
            if (!startDate || !endDate) {
                startDate ? null : $('#timeError1').show();
                endDate ? null : $('#timeError2').show();
                return;
            }
            if (startDate > endDate) {
                $('#dateError1').show();
                $('#dateError2').show();
                return;
            }
            const meta = document.querySelector('meta[name="csrf-token"]');
            $.ajax({
                url: "{{ route('transmit-data') }}",
                type: 'get',
                headers: {
                    'X-CSRF-TOKEN': meta.content
                },
                data: {
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    alert(response.success);
                    ElementLoadReset('#transmitData', 'Send Data');
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + xhr.responseJSON.error)
                    ElementLoadReset('#transmitData', 'Send Data');
                },
                beforeSend: function() {
                    ElementLoad('#transmitData');
                },
            })
        }
    </script>
@endsection
