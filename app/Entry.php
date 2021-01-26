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

    static public function getArticles($languageFlag = LANGUAGE_ALL, $limit = PHP_INT_MAX)
    {
        $records = null;

        $languageCondition = ($languageFlag == LANGUAGE_ALL) ? '>=' : '=';
        $releaseCondition = isAdmin() ? '>=' : '=';
        $releaseFlag = isAdmin() ? RELEASEFLAG_NOTSET : RELEASEFLAG_PUBLIC;
		try
		{
			$records = Entry::select()
			    ->where('language_flag', $languageCondition, $languageFlag)
			    ->where('release_flag', $releaseCondition, $releaseFlag)
				->orderByRaw('created_at DESC')
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			logException(__FUNCTION__, $e->getMessage(), __('msgs.Error getting articles'));
		}

        return $records;
    }
}