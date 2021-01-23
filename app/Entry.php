<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function isFinished()
    {
		return ($this->wip_flag >= getConstant('wip_flag.finished'));
    }

    public function isPublic()
    {
		return ($this->release_flag >= getConstant('release_flag.public'));
    }

    public function getStatus()
    {
		return ($this->release_flag);
    }

    static public function getArticles($limit = PHP_INT_MAX)
    {
        $records = null;

		try
		{
			$records = Entry::select()
				->orderByRaw('created_at DESC')
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			logException(__FUNCTION__, $e->getMessage(), __('msgs.Record not found'));
		}

        return $records;
    }
}
