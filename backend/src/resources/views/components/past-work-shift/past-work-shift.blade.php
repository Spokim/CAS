@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="tableDiv">
            <div class="col-12 col-md-10 mx-auto">
                <h1 class="text-center border-bottom p-4">Past Work Shifts</h1>
                <div class="p-md-4 xxx custom-scroll">
                    <table class="table table-striped table-hover workshiftTable">
                        <thead>
                            <tr>
                                <th scope="col">Date</th>
                                <th scope="col">Start Time</th>
                                <th scope="col">End Time</th>
                                <th scope="col">Minutes</th>
                                <th scope="col">OT</th>
                            </tr>
                        </thead>
                        <tbody id="workshiftDataInput" style="vertical-align: middle">
                        </tbody>
                    </table>
                </div>
                <div class="px-4 flex-column">
                    <p class="text-end" id="totalWorkMin"></p>
                    <p class="text-end" id="totalOvertimeWorkMin"></p>
                </div>
            </div>
            <div class="col-6 mx-auto flex-column">
                <div class="d-flex mb-3 justify-content-around">
                    <div class="me-3">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="start_Date"
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
                        <input type="date" class="form-control" id="endDate" name="end_Date"
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
                    <button class="btn btn-primary" onclick="getWorkData()">Submit</button>
                </div>

            </div>
        </div>
    </div>
    <script>
        let totalWorkMin = 0;
        let totalOvertimeMin = 0;
        let loading = false;

        const getWorkData = () => {
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
            if (loading) {
                return;
            }
            $('#workshiftDataInput').empty();
            totalWorkMin = 0;
            totalOvertimeMin = 0;
            const meta = document.querySelector('meta[name="csrf-token"]');
            $.ajax({
                    url: "{{ route('get-past-work-shift') }}",
                    type: "post",
                    headers: {
                        "X-CSRF-TOKEN": meta.content,
                    },
                    data: {
                        start_date: startDate,
                        end_date: endDate
                    },
                    beforeSend: () => {
                        loading = true;
                        $('#workshiftDataInput').append(`
                            <tr id="loadingSpinner">
                                <td colspan="5" class="text-center">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </td>
                            </tr>
                        `);
                    },
                })
                .then(response => {
                    loading = false;
                    $('#loadingSpinner').remove();
                    for (const workShift of response.workShifts) {
                        totalWorkMin += workShift.work_minutes;
                        totalOvertimeMin += workShift.overtime_minutes;
                        $('#workshiftDataInput').append(`
                                <tr>
                                <td>${workShift.work_date}</td>
                                <td>${workShift.start_time}</td>
                                <td>${workShift.end_time}</td>
                                <td>${workShift.work_minutes}</td>
                                <td>${workShift.overtime_minutes}</td>
                                </tr>
                            `);
                    }
                    // append the total work as hours and minutes
                    $('#totalWorkMin').text(
                        `Total Work: ${Math.floor(totalWorkMin / 60)} hours${totalWorkMin % 60 === 0 ? '' : ` and ${totalWorkMin % 60} minutes`}`
                    );
                    if (totalOvertimeMin > 0) {
                        $('#totalOvertimeWorkMin').text(
                            `Total Overtime: ${Math.floor(totalOvertimeMin / 60)} hours${totalOvertimeMin % 60 === 0 ? '' : ` and ${totalOvertimeMin % 60} minutes`}`
                        );
                    } else {
                        $('#totalOvertimeWorkMin').text('');
                    }
                })
                .catch(error => {
                    loading = false;
                    $('#workshiftDataInput').empty();
                    console.log(error);
                });
        }
    </script>
@endsection
