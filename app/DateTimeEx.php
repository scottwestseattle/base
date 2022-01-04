<?php

namespace App;

use DateTime;
use DateTimeZone;

//
// Extended DateTime functions
//
class DateTimeEx
{
    static private $_sTimezone = 'America/Chicago';

    static private $colors = [
        'SteelBlue',
        'Orange',
        'DarkCyan',
        'LightSalmon',
        'IndianRed',
        'MediumPurple',
        'LightSeaGreen',
        'DodgerBlue',
        'PaleVioletRed',
    ];

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

		$day = DateTimeEx::getDaysSinceZero($sDate, $sTimeZone);
		$colorCnt = count(self::$colors);
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

    static public function getShortDateTime($sDate)
    {
        $rc = self::convertTimezone($sDate, self::$_sTimezone);

        $rc = $rc->format('M-d H:i');

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
}
