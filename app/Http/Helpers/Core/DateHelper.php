<?php

/**
 * Created By: JISHNU T K
 * Date: 2024/05/22
 * Time: 22:12:02
 * Description: DateHelper.php
 */

namespace App\Http\Helpers\Core;

use Carbon\Carbon;

class DateHelper
{
    const SQL_DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    const SQL_DATE_FORMAT = 'Y-m-d';
    const DEFAULT_DATE_TIME_FORMAT = 'd-m-Y h:i A';
    const DEFAULT_DATE_FORMAT = 'm-d-Y';
    const DATE_PICKER_FORMAT = 'm-d-Y';
    const DATE_TIME_PICKER_FORMAT = 'm-d-Y H:i';

    const DEFAULT_DATE_MONTH_YEAR_FORMAT = 'd-m-Y';

    const REVIEW_DATE_FORMAT = 'F j, Y';


    /**
     * Return Server Time Based on GMT Offset
     *
     * @param $gmtOffset
     * @param $dateTime
     * @param string $format
     * @return Carbon
     */
    public static function getServerTimeFromGmtOffset($gmtOffset, $dateTime, $format = 'Y-m-d H:i:s')
    {
        $timezone = timezone_name_from_abbr("", $gmtOffset * 60, false);
        $browserTime = Carbon::createFromFormat($format, $dateTime, $timezone);
        return $browserTime->setTimezone(config('app.timezone'));
    }

    /**
     * Return Server Time Based on Timezone
     *
     * @param $gmtOffset
     * @param $dateTime
     * @param string $format
     * @return Carbon
     */
    public static function getServerTimeFromTimezone($timezone = 'EST', $dateTime, $format = 'Y-m-d H:i:s')
    {
        $browserTime = Carbon::createFromFormat($format, $dateTime, $timezone);
        return $browserTime->setTimezone(config('app.timezone'));
    }


    /**
     * Convert Server time to user time.
     * @param $timezone
     * @param $dateTime
     * @param string $format
     * @return Carbon
     */
    public static function getOrginalTime($timezone = 'UTC', $dateTime, $outputFormat = 'Y-m-d H:i:s')
    {
        $serverTime = Carbon::createFromFormat('Y-m-d H:i:s', $dateTime, config('app.timezone'));
        return $serverTime->setTimezone($timezone)->format($outputFormat);
    }

    /**
     * Convert Date Time Picker to DB TIME Format
     * @param $value
     * @param string $format
     * @return string
     */
    public static function fromDateTimePicker($value, $format = self::DEFAULT_DATE_FORMAT)
    {
        return Carbon::createFromFormat($format, $value)->format(self::SQL_DATE_TIME_FORMAT);
    }

    /**
     * Convert DB Time to Date Picker format
     * @param $value
     * @param string $format
     * @return string
     */
    public static function toDateTimePicker($value, $format = self::DEFAULT_DATE_FORMAT)
    {
        return self::format($value, $format);
    }

    public static function format($value, $format = self::DEFAULT_DATE_TIME_FORMAT)
    {
        return Carbon::parse($value)->format($format);
    }

    public static function getCurrentHumanReadableTime()
    {
        return Carbon::now()->format(self::DEFAULT_DATE_TIME_FORMAT);
    }

    public static function getCurrentHumanReadableTimeFromDate($value)
    {
        return Carbon::parse($value)->format(self::DEFAULT_DATE_TIME_FORMAT);
    }

    public static function convertMinutesToHours($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return '';
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }

    public static function convertHoursToMinutes($hours)
    {
        $time = explode('.', $hours);
        $hour = $time[0];
        $minute = $time[1] ?? 0;
        return ($hour * 60) + $minute;
    }


    public static function formatTimeDifference($value)
    {
        $carbon = new Carbon($value);
        return $carbon->diffForHumans();


        //        $date_1 = new DateTime($date1);
        //        $date_2 = new DateTime($date2);
        //        $diff = $date_1->diff($date_2);
        //
        //        if ($diff->days > 365) {
        //            return $date_1->format('Y-m-d');
        //        } elseif ($diff->days > 7) {
        //            return $date_1->format('M d');
        //        } elseif ($diff->days > 2) {
        //            return $date_1->format('L - H:i');
        //        } elseif ($diff->days == 2) {
        //            return "Yesterday ".$date_1->format('H:i');
        //        } elseif ($diff->days > 0 OR $diff->h > 1) {
        //            return $date_1->format('H:i');
        //        } elseif ($diff->i >= 1) {
        //            return $diff->i." min ago";
        //        } else {
        //            return "Just now";
        //        }
    }


    public static function getDay($dateTime, $format = 'd/m/Y')
    {
        return Carbon::createFromFormat($format, $dateTime)->day;
    }

    public static function getMonth($dateTime, $format = 'd/m/Y')
    {
        return Carbon::createFromFormat($format, $dateTime)->month;
    }

    public static function getYear($dateTime, $format = 'd/m/Y')
    {
        return Carbon::createFromFormat($format, $dateTime)->year;
    }

    public static function getCurrentYear()
    {
        return Carbon::now()->year;
    }
}
