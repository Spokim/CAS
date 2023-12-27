<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class AfterStartTime implements Rule
{
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        $startTime = request()->input('start_time');

        return strtotime($value) > strtotime($startTime);
    }

    public function message()
    {
        return 'The end time must be after the start time.';
    }
}
