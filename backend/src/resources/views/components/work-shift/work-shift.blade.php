@extends('layouts.app')

@section('content')
    <div class="container pt-4">
        <div class="tableDiv col-10 mx-auto">

            <h1 class="text-center border-bottom p-4">Create Work Shift</h1>
            @if (session('error'))
                <div class="alert alert-danger mt-3">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success mt-3">
                    {{ session('success') }}
                </div>
            @endif
            <div class="alert alert-danger p-4 col-10 mx-auto">
                <p class="text-justify m-0"><strong>
                        After submission, modifications are not permitted. In the event of any circumstance involving the
                        submission of false data, promptly report it to your superior. Any deliberate submission of false
                        data
                        will lead to immediate termination and legal prosecution to the fullest extent of the law.</p>
                </strong>
            </div>
            <div class="p-4 col-10">
                <form id="workShiftForm" method="POST" action="{{ route('post-work-data') }}" novalidate>
                    @csrf
                    <div class="d-flex">
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date"
                                max="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="d-flex mb-3">
                        <div class="me-3">
                            <label for="startTime" class="form-label">Start Time</label>
                            <input type="text" class="form-control" id="startTime" name="start_time" required>
                            <div class="invalid-feedback" id="timeError1">
                                Please enter a valid time. (HH:MM) 00:00 - 23:59
                            </div>
                        </div>
                        <div>
                            <label for="endTime" class="form-label">End Time</label>
                            <input type="text" class="form-control" id="endTime" name="end_time" required>
                            <div class="invalid-feedback" id="timeError2">
                                Please enter a valid time. (HH:MM) 00:00 - 23:59
                            </div>
                        </div>

                    </div>
                    <div class="mb-3">
                        <label for="overtimeCheckbox" class="form-label">Worked Overtime?</label>
                        <input type="checkbox" class="form-check-input" id="overtimeCheckbox">
                    </div>
                    <div class="mb-3 d-flex d-none" id="overtimeFields">
                        <div class="me-3">
                            <label for="overtimeStartTime" class="form-label">Overtime Start Time</label>
                            <input type="text" class="form-control" id="overtimeStartTime" name="overtime_start_time">
                            <div class="invalid-feedback">
                                Please enter a valid time. (HH:MM) 00:00 - 23:59
                            </div>
                        </div>
                        <div class="me-3">
                            <label for="overtimeEndTime" class="form-label">Overtime End Time</label>
                            <input type="text" class="form-control" id="overtimeEndTime" name="overtime_end_time">
                            <div class="invalid-feedback">
                                Please enter a valid time. (HH:MM) 00:00 - 23:59
                            </div>
                        </div>
                    </div>
                    <button id="submitButtom" type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </div>
    <script src="js/work-shift.js"></script>
@endsection
