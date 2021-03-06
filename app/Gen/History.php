<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

	static public function add($programName, $programId, $sessionName, $sessionId, $seconds)
	{
	    $msg = '';
		$record = new History();

		$record->ip_address = ipAddress();
		$record->program_name = trimNull($programName, true);
		$record->program_id = intval($programId);
		$record->session_name = trimNull($sessionName, true);;
		$record->session_id = intval($sessionId);
		$record->seconds = intval($seconds);

		try
		{
			$record->save();

			$msg = 'History record added';
            logInfo($msg, null, $parms = ['program' => $record->program_name, 'session' => $record->session_name, 'seconds' => $record->seconds]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding record';
			logException('History', $e->getMessage(), __('proj.Error adding history record'));
		}

		return $msg;
	}
}
