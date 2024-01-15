<?php

namespace App\Filament\Helper;

use DateTime;

class TimesheetHelper
{
    public static function calculateLogTimeInMinutes($checkin_time, $checkout_time, $break_time)
    {
        $checkin_time = new DateTime($checkin_time);
        $checkout_time = new DateTime($checkout_time);
        $interval = $checkin_time->diff($checkout_time);
        return ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i - $break_time;
    }

    public static function calculateLogTimeInString($log_time_in_minutes)
    {
        return sprintf(
            '%d:%02d',
            intdiv($log_time_in_minutes, 60),
            $log_time_in_minutes %  60
        );
    }
}
