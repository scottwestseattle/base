<?php

namespace App;

use DateTime;
use DateTimeZone;

//
// Extended DateTime functions
//
class DateTimeEx
{
    static private $_sTimezone = 'GMT'; // 'America/Chicago'

    static private $colors = [
        'SteelBlue',
        'DarkCyan',
        'IndianRed',
        'MediumPurple',
        'LightSeaGreen',
        'DodgerBlue',
        'PaleVioletRed',
    ];

    static public function getDateControlDates()
    {
		$months = [
			1 => 'January',
			2 => 'February',
			3 => 'March',
			4 => 'April',
			5 => 'May',
			6 => 'June',
			7 => 'July',
			8 => 'August',
			9 => 'September',
			10 => 'October',
			11 => 'November',
			12 => 'December',
		];

		$days = [];
		$daysOrdinal = [];
		for ($i = 1; $i <= 31; $i++)
		{
			$days[$i] = $i;
			$daysOrdinal[$i] = $i . 'th';
		}

		// set the only ones that are different
		$daysOrdinal[1] = '1st';
		$daysOrdinal[2] = '2nd';
		$daysOrdinal[3] = '3rd';
		$daysOrdinal[21] = '21st';
		$daysOrdinal[22] = '22nd';
		$daysOrdinal[23] = '23rd';
		$daysOrdinal[31] = '31st';

		$years = [];
		$startYear = 1997; //
		$endYear = intval(date('Y')) + 1; // end next year
		for ($i = $startYear; $i <= $endYear; $i++)
		{
			$years[$i] = $i;
		}

		$dates = [
			'months' => $months,
			'years' => $years,
			'days' => $days,
			'days_ordinal' => $daysOrdinal,
		];

		return $dates;
	}

    static public function getDateFilter($request, $today = false, $showFullMonth = false)
    {
		$dates = [];

		$dates['selected_month'] = false;
		$dates['selected_day'] = false;
		$dates['selected_year'] = false;
		$dates['month_flag'] = false;

		$showStatementMonth = false;
		$month = 0;
		$year = 0;
		$day = 0;

		if (isset($request) && (isset($request->day) && $request->day > 0 || isset($request->month) && $request->month > 0 || isset($request->year) && $request->year > 0))
		{
			// date filter is on, use it
			if (isset($request->month))
				if (($month = intval($request->month)) > 0)
					$dates['selected_month'] = $month;

			if (isset($request->day))
				if (($day = intval($request->day)) > 0)
				{
					$dates['selected_day'] = $day;

					// if day is set and the 'month' checkbox  is checked, then show the month ending on selected day
					$showStatementMonth = isset($request->month_flag);
					$dates['month_flag'] = $showStatementMonth;
				}

			if (isset($request->year))
				if (($year = intval($request->year)) > 0)
					$dates['selected_year'] = $year;
		}
		else
		{
			if ($today)
			{
				$month = intval(date("m"));
				$year = intval(date("Y"));

				// if we're showing the full month, then unset the 'day'
				$day = $showFullMonth ? false : intval(date("d"));

				// if nothing is set use current month
				$dates['selected_day'] = $day;
				$dates['selected_month'] = $month;
				$dates['selected_year'] = $year;
			}
			else
			{
				$dates['from_date'] = null;
				$dates['to_date'] = null;

				return $dates;
			}
		}

		//
		// put together the search dates
		//

		// set month range
		$fromMonth = 1;
		$toMonth = 12;
		if ($month > 0)
		{
			$fromMonth = $month;
			$toMonth = $month;
		}

		// set year range
		$fromYear = 2010;
		$toYear = 2050;
		if ($year > 0)
		{
			$fromYear = $year;
			$toYear = $year;
		}
		else
		{
			// if month set without the year, default to current year
			if ($month > 0)
			{
				$fromYear = intval(date("Y"));
				$toYear = $fromYear;
			}
		}

		$fromDay = 1;
		$toDate = "$toYear-$toMonth-01";
		$toDay = intval(date('t', strtotime($toDate)));

		if ($day > 0)
		{
			if ($showStatementMonth) // show the month ending on the specified day (to match bank statements)
			{
				// put the 'to' date together so we can make a DateTime
				$date = new DateTime($fromYear . '-' . $fromMonth . '-' . $day);

				// use DateInterval to subtract one month and then add one day.  do it this way to handle month and year edge cases
				// bank statement ranges look like this: 2019-01-13 to 2020-01-12
				$date->sub(new DateInterval('P1M')); // subtract one month
				$date->add(new DateInterval('P1D')); // add one day
				//dd($date);

				// take it apart again so it works with existing code below
				$fromYear = $date->format('Y');
				$fromMonth = $date->format('m');
				$fromDay = $date->format('d');
			}
			else // just show one day
			{
				$fromDay = $day;
			}

			$toDay = $day;
		}

		$dates['from_date'] = '' . $fromYear . '-' . $fromMonth . '-' . $fromDay;
		$dates['to_date'] = '' . $toYear . '-' . $toMonth . '-' . $toDay;

		return $dates;
	}

    static public function getDateControlSelectedDate($date)
    {
		$date = DateTime::createFromFormat('Y-m-d', $date);

		$parts = [
			'selected_day' => intval($date->format('d')),
			'selected_month' => intval($date->format('m')),
			'selected_year' => intval($date->format('Y')),
		];

		return $parts;
	}

    static public function getSelectedDate($request)
    {
		$filter = self::getDateFilter($request);

		$date = trimNull($filter['from_date']);

		return $date;
	}

	static public function getDayColors()
	{
	    return self::$colors;
    }

	static public function getColor($index)
	{
	    $index = $index % count(self::$colors);

	    return self::$colors[$index];
	}

	static public function getDayColor($sDate)
	{
        $sTimeZone = 'America/Chicago';

		$day = self::getDaysSinceZero($sDate, $sTimeZone);
		$colorCnt = count(self::$colors);

		// put day in our range of color codes
		$day = ($day) % $colorCnt;

		if ($day >= 0 && $day < $colorCnt)
		{
		    // expected value
		}
		else
		{
		    // unexpected, set to 0
		    $day = 0;
		}

		$rc = self::$colors[$day];

        return $rc;
    }

    static public function getDaysSinceZero($sDate, $sTimeZone)
    {
        $tz = new DateTimeZone($sTimeZone);
        $dt = null;
        $rc = 0;

        if (isset($sDate) && strlen($sDate) > 0)
        {
            try
            {
                // get the specified date
                $dt = new DateTime($sDate);
            }
            catch (\Exception $e)
            {
                dump('bad date/time');
            }

            // set the timezone
            $dt->setTimezone($tz);
            $today = $dt->format('Y-m-d H:i:s (e)');

            // get date zero
            $zero = new DateTime('0000-00-00', $tz);

            // get the difference
            $diff = $dt->diff($zero);
            $days = $diff->format('%a');

            $rc = intval($days);
        }

        return $rc;
    }

    static public function getShortDateTime($sDate, $format = null)
    {
        $format = isset($format) ? $format : 'M-d H:i';
        $rc = self::convertTimezone($sDate, self::$_sTimezone);

        //todo: this is how dates should be localized... but it has to be installed.
        // use IntlDateFormatter;
        //$formatter = new IntlDateFormatter('de_DE', IntlDateFormatter::SHORT, IntlDateFormatter::SHORT);
        //$formatter->setPattern('E d.M.yyyy');

        $l = __('dt.' . $rc->format('l'));
        $M = __('dt.' . $rc->format('M'));
        $d = $rc->format('d');
        $Y = $rc->format('Y');

        //todo: quick fix until we get a better solution
        if ($format === 'l, M d')
        {
			$rc = $l . ', ' . $M . ' ' . $d;
        }
        else if ($format === 'M d, Y')
        {

			$rc = $M . ' ' . $d . ', ' . $Y;
        }
        else
        {
            $rc = $rc->format($format);
        }

        return $rc;
    }

    static public function convertTimezone($sDate, $sTimeZone)
    {
        $tz = new DateTimeZone($sTimeZone);
        $rc = new DateTime($sDate);
        $rc->setTimezone($tz);

        return $rc;
    }

	static public function isExpired($sDate)
	{
		$rc = false;

		if (isset($sDate))
		{
			try
			{
				$expiration = new DateTime($sDate);
				$now = new DateTime('NOW');
				$rc = ($now <= $expiration);
			}
			catch(\Exception $e)
			{
				logException(__FUNCTION__, $e->getMessage(), 'Error checking expired date', ['date' => $sDate]);
				logEmergency(__FUNCTION__, 'Error checking expired date');
			}
		}

		return !$rc;
	}

	static public function isToday($sDate)
	{
        $tz = new DateTimeZone(self::$_sTimezone);

        $date = new DateTime($sDate);
        $date->setTimezone($tz);

		$now = new DateTime('NOW');
        $now->setTimezone($tz);

	    return ($date->format('Y-m-d') === $now->format('Y-m-d'));
	}
}
