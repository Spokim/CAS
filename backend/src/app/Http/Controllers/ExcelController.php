<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\UsersExport;
use App\Mail\ExcelMail;
use App\Models\Work_shift;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function export(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        $query = Work_shift::query();
        if ($startDate) {
            $query->where('work_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('work_date', '<=', $endDate);
        }
        $workShifts = $query->select('work_date', 'start_time', 'end_time', 'work_minutes', 'overtime_minutes', 'user_id')->get();

        // Group work shifts by person
        $groupedWorkShifts = $workShifts->groupBy('user_id');

        // Create an Excel file with separate worksheets for each person
        Excel::store(new UsersExport($groupedWorkShifts), 'public/work_shifts.xlsx');

        $emailTo = env('MAIL_TO_SECURE_ADDRESS');

        Mail::to($emailTo)->send(new ExcelMail(storage_path('app/public/work_shifts.xlsx')));
        return response()->json(['success' => "Email sent with attached Excel file."], 200);
    }
}
