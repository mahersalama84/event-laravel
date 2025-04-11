<?php

namespace App\Traits;

use App\Data\CreateOccasionData;
use App\Models\Occasion;
use Carbon\Carbon;

trait OccasionTrait
{
    public function toDateTime(CreateOccasionData $occasionData)
    {
        $date = Carbon::parse($occasionData->toArray()['start_date']);
        $year = $date->year;
        $month = $date->month;
        $day = $date->day;

        $time = Carbon::parse($occasionData->toArray()['start_time']);
        $hour = $time->hour;
        $minute = $time->minute;

        $dt = Carbon::create($year, $month, $day, $hour, $minute);
        $dt = $dt->toDateTimeString();
        return $dt;
    }

    public function isExpired(Occasion $occasion, string $time_zone)
    {
        $dateNow = Carbon::now()->format('Y-m-d');
        $timeNow = Carbon::now()->timezone($time_zone)->format('H:i');
        // $timeNow = Carbon::now()->format('H:i');
        $dateTwo = $occasion->start_date->format('Y-m-d');
        $timeTwo = $occasion->start_date->format('H:i');
        if ($dateNow > $dateTwo)
            return "date_expired";
        else if ($dateNow == $dateTwo && $timeNow > $timeTwo)
            return "time_expired";
        else return null;
    }
}
