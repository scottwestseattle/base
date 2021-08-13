<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

use DB;
use Auth;
use App\Gen\Course;
use App\Quiz;
use App\Status;
use App\User;

define('LESSON_FORMAT_DEFAULT', 0);
define('LESSON_FORMAT_AUTO', 1);

define('LESSONTYPE_NOTSET',          0);
define('LESSONTYPE_TEXT',           10);
define('LESSONTYPE_VOCAB',          20);
define('LESSONTYPE_QUIZ_FIB',       30);
define('LESSONTYPE_QUIZ_MC1',       40);
define('LESSONTYPE_QUIZ_MC2',       41);
define('LESSONTYPE_QUIZ_MC3',       42);
define('LESSONTYPE_QUIZ_MC4',       43);
define('LESSONTYPE_TIMED_SLIDES',   50);
define('LESSONTYPE_READING',        60);
define('LESSONTYPE_LISTS',          70); // favorites lists
define('LESSONTYPE_OTHER',          99);
define('LESSONTYPE_DEFAULT',        LESSONTYPE_TEXT);

class Lesson extends Model
{
	use SoftDeletes;

    const _typeFlags = [
		LESSONTYPE_NOTSET => 'Not Set',
		LESSONTYPE_TEXT => 'Text',
		LESSONTYPE_VOCAB => 'Vocabulary List',
		LESSONTYPE_QUIZ_FIB => 'Fill in the Blank',
		LESSONTYPE_QUIZ_MC1 => 'Multiple Choice - Set Options (MC1)',
		LESSONTYPE_QUIZ_MC2 => 'Multiple Choice - Random Options (MC2)',
		LESSONTYPE_QUIZ_MC3 => 'Multiple Choice - New Layout (MC3)',
		LESSONTYPE_TIMED_SLIDES => 'Timed Slides',
		LESSONTYPE_READING => 'Reading',
		LESSONTYPE_LISTS => 'Favorites Lists',
		LESSONTYPE_OTHER => 'Other',
    ];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function course()
    {
    	return $this->belongsTo('App\Gen\Course', 'parent_id', 'id');
    }

    public function isFinished()
    {
		return Status::isFinished($this->wip_flag);
    }

    public function isPublic()
    {
		return Status::isPublic($this->release_flag);
    }

    static public function getName($lesson)
    {
		$rc = $lesson->lesson_number . '.' . $lesson->section_number . ' ' . $lesson->lesson_title;

    	return $rc;
    }

    public function getFullName()
    {
		$rc = $this->lesson_number . '.' . $this->section_number . ' ' . $this->title;

    	return $rc;
    }

	static public function search($search)
    {
		$records = null;

		try
		{
			$search = '%' . $search . '%';

			if (User::isSuperAdmin())
			{
				// where 'deleted_flag = 0' AND (title like %search% OR text like %search%)
				$records = Lesson::select()
					->join('courses', 'courses.id', '=', 'lessons.parent_id')
					->leftJoin('words', 'lessons.id', '=', 'words.lesson_id')
					->select('lessons.id', 'lessons.lesson_number', 'lessons.section_number', 'lessons.title', 'courses.title as courseTitle')
					->where('lessons.deleted_flag', 0)
					->where('courses.release_flag', RELEASEFLAG_PUBLIC)
					->where(function ($query) use ($search){$query
						->where('lessons.title', 'LIKE', $search)
						->orWhere('lessons.text', 'LIKE', $search)
						->orWhere(function ($query) use ($search){$query
									->whereNull('words.deleted_at')
									->where('words.title', 'LIKE', $search);})
									;})
					->groupBy('lessons.id', 'lessons.lesson_number', 'lessons.section_number', 'lessons.title', 'courses.title')
					->orderBy('courses.title')
					->orderBy('lessons.lesson_number')
					->orderBy('lessons.section_number')
					->get();
			}
			else if (isAdmin())
			{
			}
			else if (Auth::check())
			{
			}
			else
			{
			}
		}
		catch(\Exception $e)
		{
		    $msg = "Search Error";
			logException('lesson search: ' . $msg, $e->getMessage(), $msg, ['search' => $search]);
		}

		return $records;
	}

	public function formatByType($quiz, $reviewType)
    {
		// if $reviewType not set, it uses the type setting on the lesson
		if (!isset($reviewType))
			$reviewType = $this->type_flag;

		switch($reviewType)
		{
			case LESSONTYPE_QUIZ_FIB:
				// FIB doesn't use answer options, so if any, remove them
				$quiz = self::removeEmbeddedAnswers($quiz, $reviewType);
				break;

			case LESSONTYPE_QUIZ_MC1:
				// expects embedded list of answers like [one, two, three] which will be converted to buttons
				$quiz = self::formatMc1($quiz, $reviewType);
				break;

			case LESSONTYPE_QUIZ_MC2:
				// creates random answers and puts them in the questions
				$quiz = $this->formatMc2($quiz, $reviewType);
				break;

			case LESSONTYPE_QUIZ_MC3:
				// create random answers with new quiz layout
				$quiz = self::formatMc3($quiz, $reviewType);
				break;

			case LESSONTYPE_QUIZ_MC4:
				// not used yet, same as LESSONTYPE_QUIZ_MC3
				$quiz = $this->formatMc4($quiz, $reviewType);
				break;

			default:
				break;
		}

		return $quiz;
	}

	// this version puts the answer options into a separate cell
	static public function formatMc3($quiz, $reviewType)
    {
		$quizNew = [];
		$answers = [];

		$max = count($quiz) - 1; // max question index
		if ($max > 0)
		{
			$randomOptions = 5;
			$cnt = 0;
			foreach($quiz as $record)
			{
				$options = [];

				if (preg_match('#\[(.*)\]#is', $record['q']))
				{
					// there is already an answer so it will be handled in formatMc1
				}
				else
				{
					//
					// get random answers from other questions
					//

					// using 100 just so it's not infinite, only goes until three unique options are picked
					$pos = rand(0, $randomOptions - 1); // position of the correct answer
					for ($i = 0; $i < 100 && count($options) < $randomOptions; $i++)
					{
						// pick three random options
						$rnd = rand(0, $max);	// answer from other random question
						$option = $quiz[$rnd];
						//dd($option['a']);

						// not the current question AND has answer text AND answer not used yet
						if ($option['id'] != $record['id'] && strlen($option['a']) > 0 && !array_key_exists($option['a'], $options))
						{
							if ($pos == count($options))
							{
								// add in the real answer randomly
								$options[$record['a']] = $record['a'];
							}

							$options[$option['a']] = $option['a'];

							if ($pos == count($options))
							{
								// add in the real answer randomly
								$options[$record['a']] = $record['a'];
							}
						}
						else
						{
							//dump('duplicate: ' . $option);
						}
					}

					$quizNew[$cnt]['options'] = $options;
				}

				$quizNew[$cnt]['q'] = $record['q'];
				$quizNew[$cnt]['a'] = $record['a'];
				$quizNew[$cnt]['id'] = $record['id'];
				$quizNew[$cnt]['ix'] = $record['id'];

				//dump($quizNew[$cnt]);

				$cnt++;
			}
		}

		//dd($quizNew);
		return self::formatMc1($quizNew, $reviewType);
	}

	//todo: not used yet
	private function formatMc4($quiz, $reviewType)
    {
		return self::formatMc3($quiz, $reviewType);
	}

	private function formatMc2($quiz, $reviewType)
    {
		$quizNew = [];
		$answers = [];

		$max = count($quiz) - 1; // max question index
		if ($max > 0)
		{
			$randomOptions = 5;
			$cnt = 0;
			foreach($quiz as $record)
			{
				if (preg_match('#\[(.*)\]#is', $record['q']))
				{
					// there is already an answer so it will be handled in formatMc1
				}
				else
				{
					//
					// get random answers from other questions
					//
					$options = [];

					// using 100 just so it's not infinite, only goes until three unique options are picked
					$pos = rand(0, $randomOptions - 1); // position of the correct answer
					for ($i = 0; $i < 100 && count($options) < $randomOptions; $i++)
					{
						// pick three random options
						$rnd = rand(0, $max);	// answer from other random question
						$option = $quiz[$rnd];
						//dd($option['a']);

						// not the current question AND has answer text AND answer not used yet
						if ($option['id'] != $record['id'] && strlen($option['a']) > 0 && !array_key_exists($option['a'], $options))
						{
							if ($pos == count($options))
							{
								// add in the real answer randomly
								$options[$record['a']] = $record['a'];
							}

							$options[$option['a']] = $option['a'];

							if ($pos == count($options))
							{
								// add in the real answer randomly
								$options[$record['a']] = $record['a'];
							}
						}
						else
						{
							//dump('duplicate: ' . $option);
						}
					}

					//dump($options);

					$q = $record['q'] . ' [';
					foreach($options as $option)
					{
						// put the options at the end of the question like: [one, two, three]
						$q .= $option . ', ';
					}
					$q .= ']';
					$q = str_replace(', ]', ']', $q);

					$record['q'] = $q;
				}

				$quizNew[$cnt]['q'] = $record['q'];
				$quizNew[$cnt]['a'] = $record['a'];
				$quizNew[$cnt]['id'] = $record['id'];
				$quizNew[$cnt]['ix'] = $record['id'];
				//dd($quiz[$i]);

				$cnt++;
			}
		}

		//dd($quizNew);
		return self::formatMc1($quizNew, $reviewType);
	}

	// creates buttons for each answer option
	// and puts them into the question
	static private function formatMc1($quiz, $reviewType)
    {
		/*
		I [am, is, are] hungry. - am
		<button class="btn btn-primary" type="button">am</button>
		<button class="btn btn-primary" type="button">is</button>
		<button class="btn btn-primary" type="button">are</button>
		*/

		$quizNew = [];
		$i = 0;
		//dump($quiz);
		foreach($quiz as $record)
		{
			$a = trim($record['a']);
			$q = trim($record['q']);
			$id = $record['id'];

			if (strlen($a) > 0)
			{
				$buttonId = 0;
				if (array_key_exists('options', $record) && is_array($record['options']))
				{
					// use the options
					$options = $record['options'];

					//
					// create a button for each answer option
					//
					$buttons = '';
					foreach($options as $m)
					{
						// mark the correct button so it can be styled during the quiz
						$buttonClass = ($m == $a) ? 'btn-right' : 'btn-wrong';

						$buttons .= self::formatButton($m, $buttonId++, $buttonClass);
					}

					// put the formatted info back into the quiz
					$quizNew[] = [
						'q' => $q,
						'a' => $a,
						'options' => $buttons,
						'id' => $record['id'],
        				'ix' => $record['id'],
					];
				}
				else
				{
					// get the answer options from the question text
					$answers = Quiz::getCommaSeparatedAnswers($q);

					if (count($answers) > 0)
					{
						//
						// create a button for each answer option
						//
						$buttons = '';
						foreach($answers as $m)
						{
							//todo: doesn't work $m = str_replace("'", "", trim($m)); // fix single apostrophe, for example N'Djamena

							// mark the correct button so it can be styled during the quiz
							$buttonClass = ($m == $a) ? 'btn-right' : 'btn-wrong';

							$buttons .= Quiz::formatButton($m, $buttonId++, $buttonClass);

						}
						//dd($buttons);

						// replace the options with the buttons
						if ($reviewType == LESSONTYPE_QUIZ_MC1)
						{
							// embed the buttons in the question text
							//$q = preg_replace("/\[.*\]/is", $buttons, $q);
							//$buttons = null; // set 'options' to null below
							$q = preg_replace("/\[.*\]/is", '_______', $q);
						}
						else
						{
							$q = preg_replace("/\[.*\]/is", '', $q);
						}

						// put the formatted info back into the quiz
						$quizNew[] = [
							'q' => $q,
							'a' => $a,
							'id' => $record['id'],
							'ix' => $record['id'],
							'options' => $buttons,
						];
					}
				}
			}
		}

		//dd($quizNew);
		return $quizNew;
	}

    public function removeEmbeddedAnswers($quiz, $reviewType)
	{
		$quizNew = [];
		$i = 0;
		//dump($quiz);

		foreach($quiz as $record)
		{
			$a = trim($record['a']);
			$q = trim($record['q']);
			$id = $record['id'];

			// get the answer options from the question text, like: Algeria [Rabat, Jerusalem, Algiers]
			$answers = Quiz::getCommaSeparatedAnswers($q);

			if (count($answers) > 0) // if there are embedded answers
			{
				// remove them
				$q = preg_replace("/\[.*\]/is", "", $q);
			}

			// put the formatted info back into the quiz
			$quizNew[] = [
				'q' => $q,
				'a' => $a,
				'id' => $record['id'],
				'ix' => $record['id'],
				'options' => null,
			];
		}

		return $quizNew;
	}

    public function isText()
	{
	    return !$this->isTimedSlides();
    }

    public function isTimedSlides()
	{
	    $rc = false;

	    if ($this->type_flag == LESSONTYPE_TIMED_SLIDES)
	        $rc = true;

	    return $rc;
    }

    public function isLists()
	{
	    $rc = false;

	    if ($this->type_flag == LESSONTYPE_LISTS)
	        $rc = true;

	    return $rc;
    }

    public function isMc($reviewType = null)
	{
		$v = false;

		if (!isset($reviewType))
			$reviewType = $this->type_flag;

		switch($reviewType)
		{
			case LESSONTYPE_QUIZ_MC1:
			case LESSONTYPE_QUIZ_MC2:
			case LESSONTYPE_QUIZ_MC3:
			case LESSONTYPE_QUIZ_MC4:
				$v = true;
				break;
			default:
				break;
		}

		return $v;
	}

	public function getLines($text)
    {
		$raw = [];
		$records = [];

		$text = str_replace('&nbsp;', '', $text);

		// first, try to split based on <br />
		if (preg_match('/<br[ ]*\/>/is', $text))
		{
			$text = str_replace('<p>', '', $text);
			$text = str_replace('</p>', '', $text);
			$raw = explode('<br />', $text);
			//dd($raw);

			foreach($raw as $record)
			{
				$records[] = trim($record);
			}
		}
		else
		{
			// try to split on <p>'s, each paragraph is one line
			preg_match_all('#<p>(.*?)</p>#is', $text, $raw, PREG_SET_ORDER);
			//dd($raw);

			foreach($raw as $record)
			{
				if (count($record) > 1)
					$records[] = trim($record[1]);
			}
		}

		//dd($records);

		return $records;
	}

    public function updateVocab()
	{
		$rc = [];

		if ($this->type_flag == LESSONTYPE_VOCAB)
		{
			$words = self::getLines($this->text);
			//dd($words);

			$rc = Word::addList($this->id, $words);
		}

		return $rc;
	}

    public function getVocab()
	{
		$rc['records'] = null;
		$rc['hasDefinitions'] = false;

		if ($this->type_flag == LESSONTYPE_VOCAB)
		{
			// if user logged in, get his copy of the vocab for this lesson with definitions
			$rc['recordsUser'] = Auth::check() ? Word::getLessonUserWords($this->id) : [];

			// get the official lesson copy which never has definitions
			$rc['records'] = Word::getByType($this->id, WORDTYPE_LESSONLIST);

			// put the lists together
			$recordsNew = [];
			$cnt = 0;
			$cntDefinitions = 0;
			foreach($rc['records'] as $record)
			{
				// check for users definition
				foreach($rc['recordsUser'] as $recordUser)
				{
					if ($recordUser->vocab_id == $record->id)
					{
						$rc['records'][$cnt]->description = $recordUser->description;
						$cntDefinitions++;
						break;
					}
				}

				$cnt++;
			}

			$rc['hasDefinitions'] = ($cnt == $cntDefinitions);

			//dd($rc);
		}

		return $rc;
	}

    public function isVocab()
	{
		$v = false;

		switch($this->type_flag)
		{
			case LESSONTYPE_VOCAB:
				$v = true;
				break;
			default:
				break;
		}

		return $v;
	}

    public function isQuiz()
	{
		$v = false;

		switch($this->type_flag)
		{
			case LESSONTYPE_QUIZ_FIB:
			case LESSONTYPE_QUIZ_MC1:
			case LESSONTYPE_QUIZ_MC2:
			case LESSONTYPE_QUIZ_MC3:
			case LESSONTYPE_QUIZ_MC4:
				$v = true;
				break;
			default:
				break;
		}

		return $v;
	}

    public function isReading()
	{
		return ($this->type_flag == LESSONTYPE_READING);
	}

    public function getLessonType()
	{
		return $this->type_flag;
	}

    static public function getTypes()
	{
		return self::_typeFlags;
	}

    public function getChapterIndex()
	{
		$records = Lesson::getIndex($this->parent_id, $this->lesson_number);

		return $records;
	}

    static public function getChapters($parentId)
	{
		$records = Lesson::getIndex($parentId);

		return $records->groupBy('lesson_number');
	}

    static public function getIndex($parent_id, $chapter = '%')
	{
		$parent_id = intval($parent_id);
		$parent_id = $parent_id > 0 ? $parent_id : '%';
		$released = isAdmin() ? '%' : '1';

		$records = [];

		$records = Lesson::select()
			->where('deleted_flag', 0)
			->where('parent_id', 'like', $parent_id)
			->where('published_flag', 'like', $released)
			->where('approved_flag', 'like', $released)
			->where('lesson_number', 'like', $chapter)
			->orderBy('parent_id')
			->orderBy('lesson_number')
			->orderBy('section_number')
			->get();

		return $records;
	}

    static public function convertCodes($text)
	{
		$v = $text;

		// replace underscores with input controls
		//$v = preg_replace('#___#is', "<input />", $v); //todo: do this at view time, don't permanently convert

		//
		// format tables
		//

		/*
		<div class="table-borderless">
		<table class="table lesson-table-sm">
		*/

		// this will wipe out any first table if more than 1 tables
		//$v = preg_replace('#(<table.*</table>)#is', "<div class=\"table-borderless\">$1</div>", $v); // wrap in div
		//$v = preg_replace('#border=\".*\"#is', "class=\"table lesson-table-sm\"", $v); // add table classes

		//dd($v);

		return $v;
	}

    public function isUnfinished()
    {
    	return (!$this->finished_flag || !$this->approved_flag || !$this->published_flag);
    }

    public function getStatus()
    {
		$text = '';
		$color = '';
		$done = true;
		$btn = '';

		if (!$this->approved_flag)
		{
			$text = 'Approve';
			$color = 'yellow';
			$btn = 'btn-warning';
			$done = false;
		}
		else if (!$this->published_flag)
		{
			$text = 'Publish';
			$color = 'green';
			$btn = 'btn-success';
			$done = false;
		}

    	return ['text' => $text, 'color' => $color, 'btn' => $btn, 'done' => $done];
    }

    public function getFinishedStatus()
    {
		$text = '';
		$color = '';
		$done = true;
		$btn = '';

		if (!$this->finished_flag)
		{
			$text = 'Finish';
			$color = 'red';
			$btn = 'btn-danger';
			$done = false;
		}

    	return ['text' => $text, 'color' => $color, 'btn' => $btn, 'done' => $done];
    }

    public function getDisplayNumber()
    {
    	return $this->lesson_number . '.' . $this->section_number;
    }

    public function renumber($renumberAll)
    {
		$renumbered = false;

		// The format is Lesson.Section or Chapter.Section
		if ($renumberAll)
		{
			// renumber all records
			$records = Lesson::select()
				->where('deleted_flag', 0)
				->where('parent_id', $this->parent_id)
				->where('lesson_number', $this->lesson_number)
				->orderBy('section_number')
				->get();

			$next = 1;
			foreach($records as $record)
			{
				$record->section_number = $next++;
				$record->save();
				$renumbered = true;
			}
		}
		else
		{
			// check if record with this section_number already exists
			$c = Lesson::select()
				->where('id', '<>', $this->id)
				->where('parent_id', $this->parent_id)
				->where('deleted_flag', 0)
				->where('lesson_number', $this->lesson_number)
				->where('section_number', $this->section_number)
				->count();

			if ($c > 0)
			{
				// renumber starting from current record
				$records = Lesson::select()
					->where('id', '<>', $this->id)
					->where('parent_id', $this->parent_id)
					->where('deleted_flag', 0)
					->where('lesson_number', $this->lesson_number)
					->where('section_number', '>=', $this->section_number)
					->orderBy('section_number')
					->get();

				$next = $this->section_number + 1;
				foreach($records as $record)
				{
					$record->section_number = $next++;
					$record->save();
					$renumbered = true;
					//dump($record->title . ': ' . $next);
				}
				//die;
			}
		}

		return $renumbered;
    }

	public function getPrevChapter()
	{
		return $this->getNextPrevChapter();
	}

    public function getChapterCount()
    {
		$r = Lesson::select()
			->where('deleted_flag', 0)
			->where('parent_id', $this->parent_id)
			->where('published_flag', 'like', isAdmin() ? '%' : 1)
			->where('approved_flag', 'like', isAdmin() ? '%' : 1)
			->where('lesson_number', $next ? '>' : '<', $this->lesson_number)
			->orderByRaw('lesson_number ASC, section_number ' . ($next ? 'ASC' : 'DESC '))
			->first();

		return $r ? $r->id : null;
    }

	public function getNextChapter()
	{
		return $this->getPrevNextChapter(/* next = */ true);
	}

	// default is prev
    public function getPrevNextChapter($next = false)
    {
		$r = Lesson::select()
			->where('deleted_flag', 0)
			->where('parent_id', $this->parent_id)
			->where('published_flag', 'like', isAdmin() ? '%' : 1)
			->where('approved_flag', 'like', isAdmin() ? '%' : 1)
			->where('lesson_number', $next ? '>' : '<', $this->lesson_number)
			->orderByRaw('lesson_number ASC, section_number ' . ($next ? 'ASC' : 'DESC '))
			->first();

		return $r ? $r->id : null;
    }

    static public function getLast($parentId)
    {
		$r = Lesson::select()
			->where('deleted_flag', 0)
			->where('parent_id', $parentId)
			->orderByRaw('lesson_number DESC, section_number DESC')
			->first();

		return $r;
    }

	static public function getPrev($curr)
	{
		return Lesson::getNextPrev($curr);
	}

	static public function getNext($curr)
	{
		return Lesson::getNextPrev($curr, /* next = */ true);
	}

	// default is prev
	static protected function getNextPrev($curr, $next = false)
	{
		if (isAdmin())
		{
			$r = Lesson::select()
				->where('deleted_flag', 0)
				->where('parent_id', $curr->parent_id)
				->where('lesson_number', $curr->lesson_number)
				->where('section_number',  $next ? '>' : '<', $curr->section_number)
				->orderByRaw('lesson_number ASC, section_number ' . ($next ? 'ASC' : 'DESC '))
				->first();
		}
		else
		{
			$r = Lesson::select()
				->where('deleted_flag', 0)
				->where('parent_id', $curr->parent_id)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('lesson_number', $curr->lesson_number)
				->where('section_number',  $next ? '>' : '<', $curr->section_number)
				->orderByRaw('lesson_number ASC, section_number ' . ($next ? 'ASC' : 'DESC '))
				->first();
		}

		return $r ? $r->id : null;
	}

    static public function getLessonNumbers($start = 1, $end = 15)
    {
    	return self::makeNumberArray($start, $end);
    }

    static public function getSectionNumbers($start = 1, $end = 15)
    {
    	return self::makeNumberArray($start, $end);
    }

    static public function makeNumberArray($start = 1, $end = 10)
    {
		$v = [];

		for ($i = $start; $i < $end; $i++)
			$v[$i] = $i;

		return $v;
	}

	static public function getCurrentLocation()
	{
		$record['lesson'] = null;
		$record['date'] = null;

        /* todo:
		// get last location view from event log
		$event = Event::getLast(LOG_TYPE_TRACKING, LOG_MODEL_LESSONS, LOG_ACTION_VIEW);
		if (isset($event))
		{
			// load this lesson
			$record['lesson'] = Lesson::getLesson($event->record_id);

			// get the time of last visit
			$record['date'] = $event->created_at;
		}
		else
		{
			//todo: check for location cookie
		}
        */

    	return $record;
	}

	static public function setCurrentLocation($courseId, $lessonId, $lastLesson)
	{
		if (Auth::check())
		{
		    if ($lastLesson)
		        ; //todo: Event::clearTracking(LOG_MODEL_LESSONS, LOG_ACTION_VIEW, $courseId);
		    else
			    ; //todo: Event::logTracking(LOG_MODEL_LESSONS, LOG_ACTION_VIEW, $lessonId, $courseId);
		}
		else
		{
			//todo: set cookie
		}
	}

    static public function getLesson($id)
	{
		$id = intval($id);
		$record = null;

		try
		{
			$record = Lesson::select()
				->where('deleted_flag', 0)
				->where('id', $id)
				->first();
		}
		catch(\Exception $e)
		{
		    $msg = "Error getting current location";
			logException(__FUNCTION__, $e->getMessage(), $msg, ['lessonId' => $id]);
		}

		return $record;
	}

    static public function getCourse($id)
    {
		$record = null;
		$id = intval($id);

		try
		{
			$lesson = Lesson::select()
				->where('deleted_flag', 0)
				->where('id', $id)
				->first();

			if (!isset($lesson))
				throw new \Exception("Lesson not found " . $id);

			if (!isset($lesson->Course))
				throw new \Exception("Course not found for lesson " . $id);

			$record = $lesson->Course;
		}
		catch(\Exception $e)
		{
		    $msg = "Error getting course from lesson id";
			logException(__FUNCTION__, $e->getMessage(), $msg, ['lessonId' => $id]);
		}

		return $record;
	}


	static public function getQuizScores($count)
	{
		$count = intval($count);
		$records = [];

		try
		{
			$records = DB::table('events')
				->join('lessons', 'events.record_id', '=', 'lessons.id')
				->join('courses', 'courses.id', '=', 'lessons.parent_id')
				->select('events.*', 'lessons.lesson_number', 'lessons.section_number', 'lessons.title as lesson_title', 'lessons.id as lesson_id', 'courses.id as course_id', 'courses.title as course_title')
				->where('events.deleted_flag', 0)
				->where('lessons.deleted_flag', 0)
				->where('events.user_id', Auth::id())
				->where('events.type_flag', LOG_TYPE_TRACKING)
				->where('events.model_flag', LOG_MODEL_LESSONS)
				->where('events.action_flag', LOG_ACTION_QUIZ)
				->orderByRaw('events.id DESC')
				->limit($count)
				->get();
		}
		catch (\Exception $e)
		{
		    $msg = "Error getting quiz results";
			logException(__FUNCTION__, $e->getMessage(), $msg, ['count' => $count]);
		}

		return $records;
	}

	static public function getQuizResultColor($score)
	{
		$color = 'light';
		$score = intval($score);

		if ($score >= 90)
			$color = 'primary';
		else if ($score >= 70)
			$color = 'success';
		else if ($score >= 60)
			$color = 'warning';
		else
			$color = 'danger';

		return $color;
	}

	public function getTime()
	{
		$seconds = isset($this->seconds) ? intval($this->seconds) : TIMED_SLIDES_DEFAULT_SECONDS;
		$breakSeconds = isset($this->break_seconds) ? intval($this->break_seconds) : TIMED_SLIDES_DEFAULT_BREAK_SECONDS;

        $rc['runSeconds'] = $seconds;
        $rc['runTime'] = secondsToTime($seconds);

        $rc['breakSeconds'] = $breakSeconds;
        $rc['breakTime'] = secondsToTime($breakSeconds);

		return $rc;
	}

	static public function getTimes($records)
	{
		$seconds = 0;
		$breakSeconds = 0;

		foreach($records as $record)
		{
			$seconds += isset($record->seconds) ? intval($record->seconds) : TIMED_SLIDES_DEFAULT_SECONDS;
			$breakSeconds += isset($record->break_seconds) ? intval($record->break_seconds) : TIMED_SLIDES_DEFAULT_BREAK_SECONDS;
		}

		$rc['seconds'] = $seconds;
		$rc['breakSeconds'] = $breakSeconds;

		$rc['timeSeconds'] = secondsToTime($seconds);
		$rc['timeTotal'] = secondsToTime($seconds + $breakSeconds);

		return $rc;
	}

	// the new version was moved to Quiz but isn't being used
	static private function formatButton($text, $id, $class)
    {
		$button = '<div><button id="'
            . $id
            . '" onclick="checkAnswerMc1('
            . $id . ', \''
		    . $text . '\')" class="btn btn-primary btn-quiz-mc3 '
		    . $class . '">'
		    . $text
		    . '</button></div>';

		//dump($button);

		return $button;
	}
}
