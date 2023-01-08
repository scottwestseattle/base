<?php

namespace App;

use DateTime;
use DateTimeZone;

//
// Extended DateTime functions
//
class DateTimeEx
{
    static private $colors = [
        '#4682b4', // 'SteelBlue',
        '#008b8b', // 'DarkCyan',
        '#cd5c5c', // 'IndianRed',
        '#9370db', // 'MediumPurple',
        '#20b2aa', // 'LightSeaGreen',
        '#1e90ff', // 'DodgerBlue',
        '#db7093', // 'PaleVioletRed',
    ];

    static private $colorsFull = [
        '#4682b4' => '#5a96c8', // 'SteelBlue',
        '#008b8b' => '#149f9f', // 'DarkCyan',
        '#cd5c5c' => '#e17070', // 'IndianRed',
        '#9370db' => '#a784ef', // 'MediumPurple',
        '#20b2aa' => '#34c6be', // 'LightSeaGreen',
        '#1e90ff' => '#32a4ff', // 'DodgerBlue',
        '#db7093' => '#ef84a7', // 'PaleVioletRed',
    ];

    static public function adjustBrightness($hex, $steps)
    {
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0,min(255,$color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }

	static public function getDayColors()
	{
	    return self::$colors;
    }

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
				$now = self::getLocalDateTime();

				$month = intval($now->format('m'));
				$year = intval($now->format('Y'));

				// if we're showing the full month, then unset the 'day'
				$day = $showFullMonth ? false : intval($now->format('d'));

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

	static public function getColor($index)
	{
	    $index = $index % count(self::$colors);

	    return self::$colors[$index];
	}

	static public function getDayColorLight($key)
	{
	    return self::$colorsFull[$key];
    }

	static public function getDayColor($sDate = null)
	{
		$day = self::getDaysSinceZero($sDate);
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

        // calculate light version of the color for the gradient
        if (false)
        {
            foreach(self::$colors as $color)
            {
                $bgLight = self::adjustBrightness($color, 20);
                dump($color . ", " . $bgLight);
            }
            dd('done');
        }

        return $rc;
    }

    static public function getDaysSinceZero($sDate)
    {
        $dt = null;
        $rc = 0;

        try
        {
            // get the specified date
            $dt = self::getLocalDateTime($sDate);
        }
        catch (\Exception $e)
        {
            dump('bad date/time');
        }

        // set the timezone
        $today = $dt->format('Y-m-d H:i:s (e)');

        // get date zero
        $zero = new DateTime('0000-00-00');

        // get the difference
        $diff = $dt->diff($zero);
        $days = $diff->format('%a');

        $rc = intval($days);

        return $rc;
    }

    static public function getShortDate($sDate, $format = null)
    {
        return self::getShortDateTime($sDate, 'M-d');
    }

    static public function getShortDateTime($sDate, $format = null)
    {
        $format = isset($format) ? $format : 'M-d H:i';

        $rc = self::getLocalDateTime($sDate);

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
	    // date to check
        $date = self::getLocalDateTime($sDate);

        // today
		$now = self::getLocalDateTime();

        // does the day match today
	    return ($date->format('Y-m-d') === $now->format('Y-m-d'));
	}

	static public function getTimestamp($dateTime = null)
	{
        $now = isset($dateTime) ? $dateTime : new DateTime();

        $now = $now->format('Y-m-d H:i:s');

	    return $now;
	}

    static public function getLocalDateTimeToday()
    {
        //
        // get the day range adjusted for the local timezone
        //
        $today = self::getLocalDateTime();
        $start = self::getLocalDateTimeString($today->format('Y-m-d 00:00:00'), /* reverse = */ true);
        $end = self::getLocalDateTimeString($today->format('Y-m-d 23:59:59'), /* reverse = */ true);

        return [
            'start' => $start,
            'end' => $end,
        ];
    }

	static public function getLocalDateTimeString($sDate = null, $reverse = false)
	{
        $now = self::getLocalDateTime($sDate, $reverse);

	    return self::getTimestamp($now);
	}

	static public function getTimezoneOffset()
	{
        return isset($_COOKIE['timezoneClient']) ? intval($_COOKIE['timezoneClient']) : 'not set';
    }

	static public function getLocalDateTime($sDate = null, $reverse = false)
	{
        // client timezone is put into a cookie by javascript after first page load
        $timezone = isset($_COOKIE['timezoneClient']) ? intval($_COOKIE['timezoneClient']) : 0;
        if (isset($sDate))
        {
            // get the provided date string from the db
            $date = new DateTime($sDate);
        }
        else
        {
            // get server time which is GMT
            $date = new DateTime();
        }

        // make time change parameter for DateInterval
        $change = 'PT' . abs($timezone) . 'H';

        if ($timezone > 0)
        {
            if ($reverse)
                $date->sub(new \DateInterval($change)); // we're getting a query range to match local time so reverse it
            else
                $date->add(new \DateInterval($change)); // ahead of GMT so add hours
        }
        else if ($timezone < 0)
        {
            if ($reverse)
                $date->add(new \DateInterval($change)); // // we're getting a query range to match local time so it has to be reversed
            else
                $date->sub(new \DateInterval($change)); // behind GMT so subtract hours
        }
        else
        {
            // GMT, no adjustment needed
        }

        return $date;
	}
}
