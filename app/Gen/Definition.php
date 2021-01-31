<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Definition extends Model
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

	static public function get($type, $value, $column = 'id')
	{
		$record = null;

		try
		{
			$record = Definition::select()
				->where($column, $value)
				->where('type_flag', $type)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting record';
			logException(LOG_MODEL . '.' . __FUNCTION__, $e->getMessage(), $msg, ['value' => $value]);
		}

		return $record;
	}

	static public function getSnippet($value)
	{
		$record = null;

		try
		{
			$record = Definition::select()
				->where('examples', $value)
				->where('type_flag', DEFTYPE_SNIPPET)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting record';
			logException(LOG_MODEL . '.' . __FUNCTION__, $e->getMessage(), $msg, ['value' => $value]);
		}

		return $record;
	}

	static public function getSnippets($parms = null)
	{
		$records = [];

		$limit = isset($parms['limit']) ? $parms['limit'] : PHP_INT_MAX;
		$orderBy = isset($parms['orderBy']) ? $parms['orderBy'] : 'updated_at DESC';
		$languageId = isset($parms['languageId']) ? $parms['languageId'] : 0;
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '>=';

		try
		{
			$records = Definition::select()
				->where('type_flag', DEFTYPE_SNIPPET)
				->where('language_flag', $languageFlagCondition, $languageId)
				->orderByRaw($orderBy)
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting phrases';
			logException('snippet', $msg . ': ' . $e->getMessage());
		}

		return $records;
	}

}
