<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Status;
use App\DateTimeEx;
use Auth;
use DB;

class Stat extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function addRead($definition_id)
    {
        return self::updateStats(['definition_id' => $definition_id, 'reads' => 1]);
    }

    static public function addView($definition_id)
    {
        return self::updateStats(['definition_id' => $definition_id, 'views' => 1]);
    }

    // test link: http://localhost/definitions/update-stats/9404
    static public function updateStats($request)
    {
	    $msg = '';
		$notUsed = false;

        // must be logged
        if (!Auth::check())
        {
            $msg = 'user not logged in';
            return $msg;
        }

        $definitionId = isset($request['definition_id']) ? abs(intval($request['definition_id'])) : 0;

        //
        // make sure the def hasn't already been deleted in another window
        //
		try
		{
			$record = Definition::select()
				->where('id', $definitionId)
				->count();
		}
		catch (\Exception $e)
		{
		    $msg = 'proj.Error getting definition record to set stats for, may be already deleted.';
			logException(LOG_CLASS, $e->getMessage(), __($msg));
			return $msg;
		}

        //
        // check if the stat record exists for this definition and user
        //
		try
		{
			$record = Stat::select()
				->where('user_id', Auth::id())
				->where('definition_id', $definitionId)
				->first();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting stat record'));
		}

        if (empty($record))
        {
            // make a new stat record
    		$record = new Stat();
    		$record->definition_id = $definitionId;
    		$record->user_Id = Auth::id();
            $record->type_flag = 1;
        }
        else
        {
            // update the existing stat record
        }

        // use abs to make sure they are positives so the counts and scores can't get reduced
        $views = isset($request['views']) ? abs(intval($request['views'])) : 0;
        $reads = isset($request['reads']) ? abs(intval($request['reads'])) : 0;
        $qna_attempts = isset($request['qna_attempts']) ? abs(intval($request['qna_attempts'])) : 0;
        $qna_correct = isset($request['qna_correct']) ? abs(intval($request['qna_correct'])) : 0;
        //
        // stats: if they're not set a 0 will be added
        //
        $now = DateTimeEx::getTimestamp();
		$record->views += $views;
		if ($views > 0) // if views is updated, set viewed_at field
            $record->viewed_at = $now;

        // add reads
		$record->reads += $reads;

        //
        // qna stats / calculate the score
        //
		$record->qna_correct    += $qna_correct;
		$record->qna_attempts   += $qna_attempts;

        $qna_score = ($record->qna_correct > 0) ? ($record->qna_correct / $record->qna_attempts) : 0.0;
        $record->qna_score = $qna_score;
		if ($qna_attempts > 0) // if qna, set qna_at field
            $record->qna_at = $now;

		try
		{
		    if ($definitionId === 0)
		        throw new \Exception('definition not set');
		    if ($record->views === 0 && $record->reads === 0 && $record->qna_attempts === 0 && $record->qna_correct === 0)
		        throw new \Exception('no stats are set');

			$record->save();
			$msg = 'Stats record added or updated';
            logInfo($msg, null, $parms = ['definition_id' => $definitionId,
                'views' => $views,
                'reads' => $reads,
                'qna_attempts' => $qna_attempts,
                'qna_correct' => $qna_correct,
                'qna_score' => $qna_score,
                ]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding record';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('proj.Error adding stats record'));
		}

		return $msg;
    }
}
