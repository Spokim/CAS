<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class WorkShiftsSheet implements FromCollection, WithTitle, WithHeadings, WithCustomStartCell, ShouldAutoSize
{
    protected $workShifts;
    protected $user_id;

    public function __construct($workShifts, $user_id)
    {
        $this->workShifts = $workShifts->map(function ($workShift) {
            unset($workShift['user_id']);
            return $workShift;
        });
        $this->user_id = $user_id;
    }

    public function collection()
    {
        $data = $this->workShifts;

        $data->push(['']);
        $data->push(['', '', '', '', '', '', 'Total time worked', '=ROUNDDOWN(SUM(F4:F' . ($data->count() + 2) . ') / 60, 0) & " hours"', '=MOD(SUM(F4:F' . ($data->count() + 2) . '), 60) & " minutes"']);
        $data->push(['', '', '', '', '', '', 'Total overtime', '=ROUNDDOWN(SUM(G4:G' . ($data->count() + 2) . ') / 60, 0) & " hours"', '=MOD(SUM(G4:G' . ($data->count() + 2) . '), 60) & " minutes"']);

        return $data;
    }

    public function title(): string
    {
        $userName = User::find($this->user_id)->name;
        return $userName;
    }

    public function headings(): array
    {
        return [
            'Work Date',
            'Start Time',
            'End Time',
            'Minutes',
            'Overtime Minutes',
        ];
    }

    public function startCell(): string
    {
        return 'C3';
    }
}
