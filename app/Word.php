<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

define('LOG_MODEL', 'Word');

class Word extends Model
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
			$record = Word::select()
				->where($column, $value)
				->where('type_flag', $type)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting phrase';
			logException(LOG_MODEL . '.' . __FUNCTION__, $e->getMessage(), $msg, ['value' => $value]);
		}

		return $record;
	}

	static public function getSnippets($parms = null)
	{
		$records = [];

		$limit = is_array($parms) && array_key_exists('limit', $parms) ? $parms['limit'] : PHP_INT_MAX;
		$orderBy = is_array($parms) && array_key_exists('orderBy', $parms) ? $parms['orderBy'] : 'id DESC';
		$languageId = is_array($parms) && array_key_exists('languageId', $parms) ? $parms['languageId'] : 0;
		$languageFlagCondition = is_array($parms) && array_key_exists('languageFlagCondition', $parms) ? $parms['languageFlagCondition'] : '>=';

		try
		{
			$records = Word::select()
				->where('type_flag', WORDTYPE_SNIPPET)
				->where('language_flag', $languageFlagCondition, $languageId)
				->orderByRaw($orderBy)
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting phrases';
			logException('word', $msg . ': ' . $e->getMessage());
		}

		return $records;
	}

}
