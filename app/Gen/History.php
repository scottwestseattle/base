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

    static public function getRss($type)
    {
		$records = History::select()
		    ->where('type_flag', $type)
			->orderByRaw('created_at DESC')
			->get();

		return $records;
	}

    static public function getAdmin()
    {
        return self::get(PHP_INT_MAX, isAdmin());
    }

    static public function get($limit = PHP_INT_MAX, $isAdmin = false)
    {
		$records = [];

        if ($isAdmin)
        {
            $userId = -1;
            $userIdCondition = '>=';
        }
        else
        {
            $userId = Auth::id();
            $userIdCondition = '=';
        }

		try
		{
			$records = History::select()
				->where('user_id', $userIdCondition, $userId)
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

	static public function add($request)
	{
	    $msg = '';
		$record = new History();
		$notUsed = false;

        $programName = isset($request['programName']) ? alphanum($request['programName']) : null;
        $programId = isset($request['programId']) ? intval($request['programId']) : 0;
        $programType = isset($request['programType']) ? intval($request['programType']) : 0;
        $programSubType = isset($request['programSubType']) ? intval($request['programSubType']) : 0;
        $sessionName = isset($request['sessionName']) ? alphanum($request['sessionName']) : null;
        $sessionId = isset($request['sessionId']) ? intval($request['sessionId']) : 0;
        $route = isset($request['route']) ? cleanUrl($request['route'], $notUsed) : null;
        $seconds = isset($request['seconds']) ? intval($request['seconds']) : 0;
        $count = isset($request['count']) ? intval($request['count']) : 0;
        $score = isset($request['score']) ? intval($request['score']) : 0;
        $extra = isset($request['extra']) ? intval($request['extra']) : 0;

        // inverse quotation mark makes the save fail: ¡
        $programName = str_replace('¡', '', $programName);
        $sessionName = str_replace('¡', '', $sessionName);

		$record->ip_address = ipAddress();
		$record->program_name = trimNull($programName, true);
		$record->program_id = intval($programId);
        $record->type_flag = intval($programType);
        $record->subtype_flag = intval($programSubType);
		$record->session_name = trimNull($sessionName, true);;
		$record->session_id = intval($sessionId);
		$record->route = trimNull($route);
		$record->user_Id = Auth::id();

        // stats
		$record->seconds = intval($seconds);
		$record->count = intval($count);
		$record->score = intval($score);
		$record->extra = intval($extra);

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

	public function getStats()
	{
	    $rc = -1;
        $seconds = intval($this->seconds);
        $score = intval($this->score);
        $count = intval($this->count);
        $extra = intval($this->extra);

        if ($seconds > 0)
            $rc = $seconds;
        else if ($score > 0)
            $rc = $score;
        else if ($count > 0)
            $rc = $count;

	    return $rc;
	}

	static public function getSubTypeInfo($subType)
	{
	    $name = 'not set';
	    $action = 'not set';
	    $actionInt = 0;

	    switch($subType)
	    {
            case LESSON_TYPE_QUIZ_MC:
	            $name = __('proj.Quiz');
	            $action = 'quiz';
	            $actionInt = 2;
	            break;
            case LESSON_TYPE_QUIZ_FLASHCARDS:
	            $name = __('proj.Flashcards');
	            $action = 'flashcards';
	            $actionInt = 1;
	            break;
            case LESSON_TYPE_READER:
	            $name = __('proj.Reader');
	            $action = 'read';
	            break;
	        case LESSON_TYPE_TIMED_SLIDES:
	            $name = __('proj.Exercise');
	            $action = 'slides';
	            break;
	        default:
	            break;
	    }

	    return ['name' => $name, 'action' => $action, 'actionInt' => $actionInt];
	}

	public function getProgramName()
	{
	    $rc = 'not set';

	    if (strlen($this->program_name) > 0)
	        $rc = $this->program_name;
	    else
	        $rc = self::getTypeInfo($this->type_flag)['name'];

	    return $rc;
    }

	public function isType($type)
	{
	    return ($this->type_flag === $type);
    }

	public function getInfo()
	{
        $action = '';
        $subTypeName = '';

        if ($this->subtype_flag > 0)
        {
            $subTypeInfo = self::getSubTypeInfo($this->subtype_flag);
            $action = $subTypeInfo['action'];
            $actionInt = $subTypeInfo['actionInt'];
            $subTypeName = $subTypeInfo['name'];
            if ($this->isType(HISTORY_TYPE_FAVORITES))
            {
                // Regular favorites lists created by users
                $action = $this->route . '/' . $this->program_id .  '/' . $actionInt;
            }
            else if ($this->isType(HISTORY_TYPE_DICTIONARY))
            {
                // From the "Dictionary" section of "Favorites"
                if ($this->route == 'read-examples') //todo: fix hardecoded route
                    $action = $this->route . '?count=' . $this->count . '&a=' . $action;
                else
                    $action = $this->route . '/' . $action;
            }
            else if ($this->isType(HISTORY_TYPE_LESSON))
            {
                // From lesson exercises
                $action = $this->program_id . '/' . $actionInt . '/' . $this->count;
            }
            else
            {
                if ($this->program_id > 0)
                {
                    $action .= '/' . $this->program_id;
                }
                else if ($this->count > 0)
                {
                    $action .= '/' . $this->count;
                }
            }
        }

        $rc = self::getTypeInfo($this->type_flag, $action);
        $rc['stats'] = $this->getStats();
        $rc['programName'] = $this->getProgramName();
        $rc['subTypeName'] = $subTypeName;

        return $rc;
    }

	static public function getTypeInfo($type, $action = '')
	{
	    $name = 'not found';
        $url = null;

	    switch($type)
	    {
            case HISTORY_TYPE_NOTSET:
            case HISTORY_TYPE_NOTUSED:
	            $name = __('project.Not Used');
	            break;
            case HISTORY_TYPE_FAVORITES:
	            $name = trans_choice('proj.Favorite', 2);
	            $url = '/definitions/' . $action;
	            break;
            case HISTORY_TYPE_ARTICLE:
	            $name = trans_choice('proj.Article', 1);
	            $url = '/articles/' . $action;
	            break;
            case HISTORY_TYPE_BOOK:
	            $name = trans_choice('proj.Book', 1);
	            $url = '/books/' . $action;
	            break;
	        case HISTORY_TYPE_LESSON:
	            $name = trans_choice('proj.Lesson', 2);
	            $url = '/lessons/review/' . $action;
	            break;
	        case HISTORY_TYPE_EXERCISE:
	            $name = trans_choice('proj.Exercise', 2);
	            break;
	        case HISTORY_TYPE_DICTIONARY:
	            $name = __('proj.Dictionary');
	            $url = '/definitions/' . $action;
	            break;
	        case HISTORY_TYPE_SNIPPETS:
	            $name = __('proj.Practice Text');
	            $url = '/snippets/' . $action;
	            break;
	        case HISTORY_TYPE_OTHER:
	            $name = __('proj.Other');
	            break;
	        default:
	            break;
	    }

	    return ['name' => $name, 'url' => $url, 'hasUrl' => isset($url)];
	}

    static function getReviewType($parameter)
    {
        $parameter = strtolower(alpha($parameter));

        $rc = LESSON_TYPE_QUIZ_MC;

        if ($parameter == 'flashcards')
            $rc = LESSON_TYPE_QUIZ_FLASHCARDS;
        else if ($parameter == 'quiz')
            $rc = LESSON_TYPE_QUIZ_MC;

        return $rc;
    }

    static function getReviewTypeInt($parameter)
    {
        $parameter = intval($parameter);

        $rc = LESSON_TYPE_QUIZ_MC;

        if ($parameter == 1)
            $rc = LESSON_TYPE_QUIZ_FLASHCARDS;
        else if ($parameter == 2)
            $rc = LESSON_TYPE_QUIZ_MC;

        return $rc;
    }

    static function getArrayShort($programType, $programSubType, $count)
    {
        return self::getArray(null, 0, $programType, $programSubType, $count);
    }

    static function getArray($programName, $programId, $programType, $programSubType, $count, $options = null)
    {
        $rc =  [
            'historyPath' => HISTORY_URL,
			'programName' => $programName,
			'programId' => $programId,
			'programType' => $programType,
			'programSubType' => $programSubType,
			'sessionName' => null,
			'sessionId' => 0,
			'count' => $count,
			'score' => 0,
			'seconds' => 0,
			'extra' => 0,
			'route' => null,
        ];

        if (isset($options['sessionName']))
            $rc['sessionName'] = $options['sessionName'];
        if (isset($options['sessionId']))
            $rc['sessionId'] = $options['sessionId'];
        if (isset($options['route']))
            $rc['route'] = $options['route'];
        if (isset($options['score']))
            $rc['score'] = $options['score'];
        if (isset($options['seconds']))
            $rc['seconds'] = $options['seconds'];
        if (isset($options['extra']))
            $rc['extra'] = $options['extra'];

        //dump($rc);

        return $rc;
    }

}
