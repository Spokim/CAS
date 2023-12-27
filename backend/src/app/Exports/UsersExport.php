<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UsersExport implements WithMultipleSheets
{
    use Exportable;

    protected $groupedWorkShifts;

    public function __construct($groupedWorkShifts)
    {
        $this->groupedWorkShifts = $groupedWorkShifts;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->groupedWorkShifts as $user_id => $workShifts) {
            $sheets[] = new WorkShiftsSheet($workShifts, $user_id);
        }

        return $sheets;
    }
}

