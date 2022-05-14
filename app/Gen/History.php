<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use App\Status;

class History extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function getRss()
    {
		$records = History::select()
			->orderByRaw('created_at DESC')
			->get();

		return $records;
	}

    static public function get($limit = PHP_INT_MAX)
    {
		$records = [];

		try
		{
			$records = History::select()
				->where('user_id', Auth::id())
				->orderByRaw('id DESC')
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

        $counts = [];
        foreach ($records as $record)
        {
            $date = \App\DateTimeEx::getShortDateTime($record->created_at, 'm-d-Y');
            if (!isset($counts[$date]))
            {
                $counts[$date] = 0;
            }

            $counts[$date]++;
        }

        $history['records'] = $records;
        $history['counts'] = $counts;

		return $history;
	}

	static public function add($programName, $programId, $sessionName, $sessionId, $seconds)
	{
	    $msg = '';
		$record = new History();

        // inverse quotation mark makes the save fail: ¡
        $programName = str_replace('¡', '', $programName);
        $sessionName = str_replace('¡', '', $sessionName);

		$record->ip_address = ipAddress();
		$record->program_name = trimNull($programName, true);
		$record->program_id = intval($programId);
		$record->session_name = trimNull($sessionName, true);;
		$record->session_id = intval($sessionId);
		$record->seconds = intval($seconds);
		$record->user_Id = Auth::id();

		try
		{
			$record->save();
			$msg = 'History record added';
            logInfo($msg, null, $parms = ['program' => $record->program_name, 'session' => $record->session_name, 'seconds' => $record->seconds]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding record';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('proj.Error adding history record'));
		}

		return $msg;
	}
}
