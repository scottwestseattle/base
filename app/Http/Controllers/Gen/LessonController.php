<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Log;

use App\Gen\Lesson;
use App\Gen\Course;
use App\Entry;
use App\Quiz;
use App\User;
use App\VocabList;

define('PREFIX', 'lessons');
define('VIEWS', 'gen.lessons');
define('LOG_MODEL', 'lessons');
define('TITLE', 'Lesson');
define('TITLE_LC', 'lesson');
define('TITLE_PLURAL', 'Lessons');
define('REDIRECT', '/lessons');
define('REDIRECT_ADMIN', '/lessons/admin');

class LessonController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
			'index', 'review', 'reviewmc', 'read', 'view',
			'start', 'permalink', 'logQuiz', 'rss', 'rssReader'
		]);

		$this->prefix = PREFIX;
		$this->title = TITLE;
		$this->titlePlural = TITLE_PLURAL;

		parent::__construct();
	}

    public function index(Request $request, $parent_id)
    {
		$parent_id = intval($parent_id);

		$records = []; // make this countable so view will always work

		try
		{
			$records = Lesson::getIndex($parent_id);
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting ' . $this->title . ' list';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['parent_id' => $parent_id]);
		}

		return view(VIEWS . '.index', [
		    'titlePlural' => $this->titlePlural,
			'records' => $records,
		]);
    }

    public function admin(Request $request)
    {
		$records = []; // make this countable so view will always work

		try
		{
            $records = Lesson::select()
                ->orderByRaw('parent_id, lesson_number, section_number')
                ->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting lesson list';
			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return view(VIEWS . '.admin', [
			'records' => $records,
		]);
    }

    static public function getCourses($source)
    {
		$records = []; // make this countable so view will always work
		try
		{
			$records = Course::getIndex();
		}
		catch (\Exception $e)
		{
			$msg = 'Lesson: Error getting course list';
			logException(__FUNCTION__, $e->getMessage(), $msg, ['source' => $source]);
		}

		return $records;
	}

    //todo: add canned descriptions somewhere
    private $_timedSlideDescriptions = [
        'cobra' => 'Estiramiento de Cobra',
        'leg-lift' => 'Lay flat on your back with arms by your sides and lift both legs about one foot into the air while keeping them straight.',
    ];

    public function add(Course $course)
    {
		$lessons = null;
		$chapter = 1;
		$section = 1;

		if (isset($course->id))
		{
			$record = Lesson::getLast($course->id);
			if (isset($course->id) && isset($record)) // if a lesson already exists, get the next increment
			{
				$chapter = $record->lesson_number;		// use the current chapter
				$section = $record->section_number + 1;	// use the next section number

				$lessons = $record->getChapterIndex(); // get lessons to show in a list
			}
		}

		return view(VIEWS . '.add', [
			'course' => $course,										// parent
			'courses' => self::getCourses('add'),	// for the course dropdown
			'chapter' => $chapter,
			'section' => $section,
			'lessons' => $lessons,
			'photoPath' => '/img/plancha/',
			]);
	}

    public function create(Request $request)
    {
		$record = new Lesson();

		$record->user_id 		= Auth::id();
		$record->parent_id 		= $request->parent_id;
		$record->title 			= $request->title;
		$record->title_chapter 	= $request->title_chapter;
		$record->description	= $request->description;
		$record->text			= self::convertFromHtml($request->text);
		$record->permalink		= createPermalink($request->title);
		$record->lesson_number	= intval($request->lesson_number);
		$record->section_number	= intval($request->section_number);
        $record->type_flag      = intval($request->type_flag);
        $record->main_photo     = $request->main_photo;
        $record->seconds        = $request->seconds;
        $record->break_seconds  = $request->break_seconds;
        $record->reps           = $request->reps;

        if ($record->isTimedSlides())
        {
            $record->published_flag = 1;
            $record->approved_flag = 1;
            $record->finished_flag = 1;
		}

		try
		{
			$record->save();
            $msg = 'New ' . TITLE_LC . ' has been added';
			logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new ' . TITLE_LC;
			logException(__FUNCTION__, $e->getMessage(), $msg, ['title' => $record->title]);

			return back();
		}

		if (isset($record->id))
		{
		    if ($record->isText())
			    return redirect('/lessons/edit/' . $record->id);
			else
			    return redirect('/lessons/view/' . $record->id);
		}
		else
			return redirect('/lessons/admin/' . $record->parent_id);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);

		$record = null;

		try
		{
			$record = Lesson::select()
				->where('site_id', SITE_ID)
				->where('deleted_flag', 0)
				->where('published_flag', 1)
				->where('approved_flag', 1)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Lesson Not Found';
			logInfo(__FUNCTION__, $msg, ['permalink' => $permalink]);

			return back();
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	private static function autoFormat($text)
    {
		$t = $text;

		$posEx = strpos($t, 'For example:');
		$posH3 = strpos($t, '<h3>');

		if ($posEx && $posH3 && $posEx < $posH3)
		{
			$t = str_replace('For example:', 'For example:<div class="lesson-examples">', $t);
			$t = str_replace('<h3>', '</div><h3>', $t);
		}

		return $t;
	}

	public function view(Lesson $lesson)
    {
		$lesson->text = self::convertToHtml($lesson->text);

		if ($lesson->format_flag == LESSON_FORMAT_AUTO)
		{
			$lesson->text = LessonController::autoFormat($lesson->text);
		}

		$prev = Lesson::getPrev($lesson);
		$next = Lesson::getNext($lesson);
		$nextChapter = $lesson->getNextChapter();

        $lastLesson = (!isset($next) && !isset($nextChapter)); // if on last lesson
		Lesson::setCurrentLocation($lesson->parent_id, $lesson->id, $lastLesson);

		// count the <p>'s as sentences
		preg_match_all('#<p>#is', $lesson->text, $matches, PREG_SET_ORDER);
		$sentenceCount = count($matches);

		// if there's a table, count the rows, add it to the count
		if (strpos($lesson->text, '<table') !== false)
		{
			preg_match_all('#<tr#is', $lesson->text, $matches, PREG_SET_ORDER); // a formatted table not using <p>'s
			$sentenceCount += count($matches);
		}

		// only vocab pages may have vocab
		$vocab = $lesson->getVocab();

		// get course time to show
		$records = Lesson::getIndex($lesson->parent_id, $lesson->lesson_number);

		$times = Lesson::getTimes($records);

		return view(VIEWS . '.view', [
			'record' => $lesson,
			'prev' => $prev,
			'next' => $next,
			'sentenceCount' => count($matches),
			'courseTitle' => isset($lesson->course) ? $lesson->course->title : '',
			'nextChapter' => $nextChapter,
			'lessons' => $lesson->getChapterIndex(),
			'vocab' => $vocab['records'],
			'hasDefinitions' => $vocab['hasDefinitions'], // if the user has already added one or more definitions
			'photoPath' => '/img/plancha/',
			'times' => $times,
			]);
    }

	public function convertToList(Lesson $lesson)
    {
        $text = html_entity_decode($lesson->text); // make it plain text

		$qna = LessonController::makeQuiz($text);
        //dd($qna);

		if (VocabList::import($qna, $lesson->title, $lesson->isMc()))
    		return redirect('/vocab-lists');
    	else
    	    return back();
    }

	public function edit(Lesson $lesson)
    {
		return view(VIEWS . '.edit', [
			'record' => $lesson,
			'courses' => self::getCourses('edit'), // for the course dropdown
			'tinymce' => true,
			'photoPath' => '/img/plancha/',
			]);
    }

    public function update(Request $request, Lesson $lesson)
    {
		$record = $lesson;

		$rc = $record->updateVocab(); // if it's vocab, it will save the word list
		if (!self::getSafeArrayBool($rc, 'error', false))
		{
			// don't do anything destructive
		}

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->title_chapter = copyDirty($record->title_chapter, $request->title_chapter, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->text = copyDirty($record->text, self::convertFromHtml(self::cleanHtml($request->text)), $isDirty, $changes);
		$record->parent_id = copyDirty($record->parent_id, $request->parent_id, $isDirty, $changes);
		$record->type_flag = copyDirty($record->type_flag, $request->type_flag, $isDirty, $changes);
		$record->options = copyDirty($record->options, $request->options, $isDirty, $changes);
		$record->main_photo = copyDirty($record->main_photo, $request->main_photo, $isDirty, $changes);
		$record->seconds = copyDirty($record->seconds, $request->seconds, $isDirty, $changes);
		$record->break_seconds = copyDirty($record->break_seconds, $request->break_seconds, $isDirty, $changes);
		$record->reps = copyDirty($record->reps, $request->reps, $isDirty, $changes);

		// autoformat is currently just a checkbox but the db value is a flag
		$format_flag = isset($request->autoformat) ? LESSON_FORMAT_AUTO : LESSON_FORMAT_DEFAULT;
		$record->format_flag = copyDirty($record->format_flag, $format_flag, $isDirty, $changes);

		// renumber action
		$renumberAll = isset($request->renumber_flag) ? true : false;

		$numbersChanged = $renumberAll; // if the numbering changes, then we need to check if an auto-renumber is needed
		$record->lesson_number = copyDirty($record->lesson_number, $request->lesson_number, $numbersChanged, $changes);
		$record->section_number = copyDirty($record->section_number, $request->section_number, $numbersChanged, $changes);
		if ($numbersChanged)
			$isDirty = true;

		if ($isDirty)
		{
			try
			{
				$record->save();

				$msg = 'Lesson has been updated';
                logInfo(__FUNCTION__, $msg, ['id' => $record->id]);

				if ($numbersChanged)
				{
					if ($record->renumber($renumberAll)) // check for renumbering
					{
						$msg = TITLE_PLURAL . ' have been renumbered';
                        logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
					}
				}
			}
			catch (\Exception $e)
			{
			    $msg = "Error updating record";
    			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
			}
		}
		else
		{
			$msg = 'No changes made to ' . TITLE_LC;
            logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
		}

        if ($record->isText())
            $redirect = '/lessons/view/' . $record->id;
        else
            $redirect = '/lessons/start/' . $record->id;

		return redirect($redirect);
	}

	public function edit2(Lesson $lesson)
    {
		return view(VIEWS . '.edit2', [
			'record' => $lesson,
			]);
    }

    public function update2(Request $request, Lesson $lesson)
    {
		$record = $lesson;

		$isDirty = false;
		$changes = '';

		$record->text = copyDirty($record->text, self::convertFromHtml(self::cleanHtml($request->text)), $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				$msg = 'Lesson has been updated';
                logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
			}
			catch (\Exception $e)
			{
                $msg = 'Error updating lesson';
    			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
			}
		}
		else
		{
			$msg = 'No changes made to lesson';
            logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}

    public function confirmdelete(Lesson $lesson)
    {
		return view(VIEWS . '.confirmdelete', [
			'record' => $lesson,
		]);
    }

    public function delete(Request $request, Lesson $lesson)
    {
		$record = $lesson;

		try
		{
			$record->deleteSafe();
			$msg = 'Lesson has been deleted';
            logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error deleting record';
   			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
		}

		return redirect(REDIRECT_ADMIN);
    }

    public function undelete()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Lesson::select()
//				->where('site_id', SITE_ID)
				->where('deleted_flag', 1)
				->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting ' . TITLE_LC . 'undelete list';
   			logException(__FUNCTION__, $e->getMessage(), $msg);
		}

		return view(VIEWS . '.undelete', [
			'records' => $records,
		]);
	}

    public function publish(Request $request, Lesson $lesson)
    {
		return view(VIEWS . '.publish', [
			'record' => $lesson,
		]);
    }

    public function publishupdate(Request $request, Lesson $lesson)
    {
		$record = $lesson;

		$record->published_flag = isset($request->published_flag) ? 1 : 0;
		$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
		$record->finished_flag = isset($request->finished_flag) ? 1 : 0;

		try
		{
			$record->save();
			$msg = 'Lesson status has been updated';
            logInfo(__FUNCTION__, $msg, ['id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error publishing lesson';
   			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
		}

		return redirect('/lessons/view/' . $lesson->id);
    }

	// this is the original way
	public function makeQuiz($text)
    {
		$records = [];

		// count the paragraphs as sentences
		preg_match_all('#<p>(.*?)</p>#is', $text, $records, PREG_SET_ORDER);

		$qna = [];
		$cnt = 0;
		foreach($records as $record)
		{
		    // had to do this because html_entity_decode() wouldn't work in explode
		    $line = str_replace('&nbsp;', ' ', htmlentities($record[1]));
            $line = html_entity_decode($line); // decode it back

            // this doesn't work
            //$line = html_entity_decode($record[1]); // doesn't change &nbsp; to space
            //dump($line);

			$parts = explode(' - ', $line); // split the line into q and a, looks like: "question text - correct answer text"
            //dd($parts);

			if (count($parts) > 0)
			{
				$records[$cnt]['q'] = trim($parts[0]);
				$records[$cnt]['a'] = array_key_exists(1, $parts) ? trim($parts[1]) : '';
				$records[$cnt]['id'] = $cnt;
				$records[$cnt]['options'] = '';
			}
			//dd($qna);

			$cnt++;
		}

		//dd($records);

		return $records;
	}

	//
	// this is the new way, updated for review.js
	//
	public function makeQna($text)
    {
		$records = [];

		// chop it into lines by using the <p>'s
	    $text = str_replace(['&ndash;', '&nbsp;'], ['-', ' '], $text);
		preg_match_all('#<p>(.*?)</p>#is', $text, $records, PREG_SET_ORDER);

		$qna = [];
		$cnt = 0;
		$delim = (strpos($text, ' | ') !== false) ? ' | ' : ' - ';
		foreach($records as $record)
		{
			$line = $record[1];
			$line = strip_tags($line);

			$parts = explode($delim, $line); // split the line into q and a, looks like: "question text - correct answer text"
            //dd($parts);

			if (count($parts) > 0)
			{
				$q = trim($parts[0]);
				$qna[$cnt]['q'] = $q;
				$qna[$cnt]['a'] = array_key_exists(1, $parts) ? trim($parts[1]) : null;
				$qna[$cnt]['definition'] = 'false';
				$qna[$cnt]['translation'] = '';
				$qna[$cnt]['extra'] = '';
				$qna[$cnt]['id'] = $cnt;
				$qna[$cnt]['ix'] = $cnt; // this will be the button id, just needs to be unique
				$qna[$cnt]['options'] = '';

				if (!isset($qna[$cnt]['a']))
					throw new \Exception('parse error: ' . $q);
			}

			$cnt++;
		}

		//dd($qna);

		return $qna;
	}

	public function reviewOrig(Lesson $lesson, $reviewType = null)
    {
		$prev = Lesson::getPrev($lesson);
		$next = Lesson::getNext($lesson);

		$quiz = LessonController::makeQuiz($lesson->text);
		$quiz = $lesson->formatByType($quiz, $reviewType);

		//todo: not working yet
		$quizText = [
			'Round' => 'Round',
			'Correct' => 'Correct',
			'TypeAnswers' => 'Type the Answer',
			'Wrong' => 'Wrong',
			'of' => 'of',
		];

		return view(VIEWS . '.review-orig', [
			'record' => $lesson,
			'prev' => $prev,
			'next' => $next,
			'sentenceCount' => count($quiz),
			'records' => $quiz,
			'questionPrompt' => '', //'What is the answer?',
			'questionPromptReverse' => '', // 'What is the question?',
			'canEdit' => true,
			'quizText' => $quizText,
			'isMc' => $lesson->isMc($reviewType),
			]);
    }

	public function reviewmc(Lesson $lesson, $reviewType = null)
    {
		$prev = Lesson::getPrev($lesson);
		$next = Lesson::getNext($lesson);

		$quiz = LessonController::makeQuiz($lesson->text); // splits text into questions and answers
		$quiz = $lesson->formatByType($quiz, $lesson->type_flag); // format the answers according to quiz type

		//todo: not working yet
		$quizText = [
			'Round' => 'Round',
			'Correct' => 'Correct',
			'TypeAnswers' => 'Type the Answer',
			'Wrong' => 'Wrong',
			'of' => 'of',
		];

		$options = Quiz::getOptionArray($lesson->options);

		$options['prompt'] = self::getSafeArrayString($options, 'prompt', 'Select the correct answer');
		$options['prompt-reverse'] = self::getSafeArrayString($options, 'prompt-reverse', 'Select the correct question');
		$options['question-count'] = self::getSafeArrayInt($options, 'question-count', 0);
		$options['font-size'] = self::getSafeArrayString($options, 'font-size', '120%');

		return view(VIEWS . '.reviewmc', [
			'record' => $lesson,
			'prev' => $prev,
			'next' => $next,
			'sentenceCount' => count($quiz),
			'records' => $quiz,
			'options' => $options,
			'canEdit' => true,
			'quizText' => $quizText,
			'isMc' => $lesson->isMc($reviewType),
            'returnPath' => PREFIX . '/view',
			]);
    }

	//
	// this is the version updated to work with review.js
	//
	public function review(Lesson $lesson, $reviewType = null)
    {
        $record = $lesson;
		$reviewType = intval($reviewType);
		$prev = Lesson::getPrev($lesson);
		$next = Lesson::getNext($lesson);

		try
		{
			$quiz = self::makeQna($lesson->text); // split text into questions and answers
		}
		catch (\Exception $e)
		{
			$msg = 'Error making lesson qna';
   			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
			return back();
		}

		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'prev' => $prev,
			'next' => $next,
			'sentenceCount' => count($quiz),
			'records' => $quiz,
			'canEdit' => true,
			'isMc' => true, //$lesson->isMc($reviewType),
            'returnPath' => '/' . PREFIX . '/view/' . $lesson->id,
			'parentTitle' => $lesson->title,
			'settings' => $settings,
			]);
    }

    public function read(Request $request, Lesson $lesson)
    {
		$record = $lesson;
		$text = [];

		//$title = getSentences($record->title);
		$text = str_replace("<br />", "\r\n", $record->text);
		$text = getSentences($text);
		//$text = array_merge($title, $text);
		//dd($text);

		$record['lines'] = $text;
        $languageCodes = getSpeechLanguage($record->language_flag);

    	return view('shared.reader', [
			'record' => $record,
			'readLocation' => null,
			'speechLanguage' => 'es-ES',
			'contentType' => 'Lesson',
			'languageCodes' => $languageCodes,
		]);
    }

    public function logQuiz($lessonId, $score)
    {
		$rc = '';

		if (Auth::check())
		{
			Event::logTracking(LOG_MODEL_LESSONS, LOG_ACTION_QUIZ, $lessonId, $score);
			$rc = 'event logged';
		}
		else
		{
			//todo: set cookie
			$rc = 'user not logged in: event not logged';
		}

		return $rc;
	}

    public function toggleFinished(Lesson $lesson)
    {
		$rc = '';

		if (Auth::check())
		{
			//Event::logTracking(LOG_MODEL_LESSONS, LOG_ACTION_QUIZ, $lessonId, $score);
			$rc = 'event logged';
		}
		else
		{
			//todo: set cookie
			$rc = 'user not logged in: event not logged';
		}

		$rc = 'Not implemented yet';

		return $rc;
	}

	public function start(Lesson $lesson)
    {
		Lesson::setCurrentLocation($lesson->id);

		$records = Lesson::getIndex($lesson->parent_id, $lesson->lesson_number);

		if (false) // one time fix for file namespace
		{
			foreach($records as $record)
			{
				$photo = str_replace('-', '_', $record->main_photo);
				$photo = str_replace(' ', '_', $photo);

				if ($photo != $record->main_photo)
				{
					//dump($photo);
					$record->main_photo = $photo;
					$record->save();
				}
			}
		}

		$times = Lesson::getTimes($records);

		// get background images by random album
		$bgAlbums = [
			'pnw', 'europe', 'africa', 'uk'
		];
		$ix = rand(1, count($bgAlbums)) - 1;
		$album = $bgAlbums[$ix];
        $bgs = Image::getPhotos('/img/backgrounds/' . $album . '/');
		//dump($album);

        foreach($bgs as $key => $value)
        {
            $bgs[$key] = 0;
        }
        //dd($bgs);

		return view(VIEWS . '.runtimed', [
			'record' => $lesson,
			'records' => $records,
			'returnPath' => 'courses/view',
			'displayTime' => $times['timeTotal'],
			'totalSeconds' => $times['seconds'],
			'bgs' => $bgs,
			'bgAlbum' => $album,
			]);
    }

    public function rssReader(Lesson $lesson)
    {
		$records = Lesson::getIndex($lesson->parent_id, $lesson->lesson_number);

		$qna = [];

		foreach ($records as $record)
		{
			$lines = explode("\r\n", strip_tags(html_entity_decode($record->text)));
			//dd($lines);

			$cnt = 0;
			foreach($lines as $line)
			{
				$line = trim($line);
				if (strlen($line) > 0)
				{
					$parts = explode(" - ", $line);

					$qna[$cnt]['q'] = null;
					$qna[$cnt]['a'] = null;

					if (count($parts) > 0)
						$qna[$cnt]['q'] = $parts[0];

					if (count($parts) > 1)
						$qna[$cnt]['a'] = $parts[1];

					$cnt++;
				}
			}

			$record['qna'] = $qna;
		}

		return view(VIEWS . '.rss-reader', [
			'record' => $lesson,
			'records' => $records,
			]);
	}

	public function rss(Lesson $lesson)
    {
		$records = Lesson::getIndex($lesson->parent_id, $lesson->lesson_number);

		return view(VIEWS . '.rss', [
			'record' => $lesson,
			'records' => $records,
			]);
    }

    static public function convertFromHtml($text)
    {
		$v = $text;

		if (strpos($v, '[') !== false)
		{
			//$v = str_replace('[', '<', $v);
			//$v = str_replace(']', '>', $v);
		}
		else if (strpos($v, '<') !== false)
		{
			// has regular html, so convert it
			//$v = str_replace('<', '[', $v);
			//$v = str_replace('>', ']', $v);
		}

		// replace <table border="1">
		$v = preg_replace("/\<table( *)border=\"1\"\>/", "<table class=\"table table-borderless\">", $v);

		return $v;
	}

	// <div role="fancy-table-xs-border-header">
    static public function convertToHtml($text)
    {
		$v = $text;
		$f = '';
		// check for custom formatting HTML tags
		$fancyTableTag = 'fancy-table';
		$endTag = '</div>';
		if (strpos($v, $fancyTableTag) !== false)
		{
			// do the fancy formatting
			$lines = explode("\r\n", $text);
			$inTableLine = 0;
			$header = false; // default
			foreach($lines as $line)
			{
				$pos = strpos($line, $fancyTableTag);
				if ($pos !== false)
				{
					$inTableLine++;

					// get table attributes, looks like: "fancy-table-xs-border-header"
					$attr = substr($line, $pos);
					$parts = explode('-', $attr);
					$border = 'table-borderless'; // default
					$size = 'lesson-table-xs';	  // default
					if (count($parts) > 4) // header
						$header = (trim($parts[4], '">') == 'header');
					if (count($parts) > 3) // border
						$border = Str::startsWith(trim($parts[3], '">'), 'borderless') ? 'table-borderless' : '';
					if (count($parts) > 2) // size
						$size = 'lesson-table-' . trim($parts[2], '">');

					$table = '<table class="table lesson-table ' . $border . ' ' . ' ' . $size . '">';

					//dump($parts);
					//dump($header);
					$f .= $table;
				}
				else if (strpos($line, $endTag) !== false)
				{
					$inTableLine = 0;
					$header = false;
					$f .= '</table>';
				}
				else if ($inTableLine > 0)
				{
					$col1 = null;
					$col2 = null;
					$col3 = null;

					// clean up the line but don't removing styling
					$line = str_replace('<p>', '', $line);
					$line = str_replace('</p>', '', $line);
					$line = str_replace('&nbsp;', '', $line);
					$line = str_replace('<br />', '', $line);

					$parts = explode('|', $line);
					if (count($parts) > 2)
						$col3 = trim($parts[2]);
					if (count($parts) > 1)
						$col2 = trim($parts[1]);
					if (count($parts) > 0)
						$col1 = trim($parts[0]);

					if (count($parts) > 0)
					{
						$rowStart = '<tr>';
						$rowEnd = '</tr>';
						$colStartTag = '<td>';
						$colEndTag = '</td>';

						if ($header && $inTableLine == 1)
						{
							$rowStart = '<thead><tr>';
							$rowEnd = '</tr></thead>';
							$colStartTag = '<th>';
							$colEndTag = '</th>';
						}

						$row = $rowStart;

						if (isset($col1))
							$row .= $colStartTag . $col1 . $colEndTag;

						if (isset($col2))
							$row .= $colStartTag . $col2 . $colEndTag;

						if (isset($col3))
							$row .= $colStartTag . $col3 . $colEndTag;

						$row .= $rowEnd;
						$f .= $row;
					}
					else
					{
						$f .= $line;
					}

					$inTableLine++;
				}
				else
				{
					$f .= $line;
				}

			}
			$v = $f;
		}
		else if (strpos($v, '[') !== false)
		{
			//$v = str_replace('[', '<', $v);
			//$v = str_replace(']', '>', $v);
		}
		else if (strpos($v, '<') !== false)
		{
			// has regular html, leave it alone
		}
		else
		{
			// no html so add br's
			$v = nl2br($v);
		}

		// do custom word replacements
		$v = str_ireplace('(irregular)', '<span class="irregular">irregular</span>', $v); // make (irregular) fancy

		// format Fancy Numbers: "FN1. "
		$v = preg_replace('/FN([0-9]*)\.([ ]*)/', '<span class="fn">$1</span>', $v); //  $1 resolves to the part matched in the parenthesis

		// format Fancy Bullets, type 1: "FB1"
		//$v = preg_replace('/FB1([ ]*)/', '<div style="font-size:12px;" class="fb glyphicon glyphicon-arrow-right mr-2 ml-4 mb-1"></div>', $v); //
		$v = preg_replace('/FB1([ ]*)/', '<div style="font-size:18px;" class="middle mr-2 ml-0 mb-1">→</div>', $v); //
		$v = preg_replace('/FB2([ ]*)/', '<div style="font-size:12px;" class="middle mr-2 ml-0 mb-1 glyphicon glyphicon-triangle-right green"></div>', $v); //

		return $v;
	}

    static public function getSafeArrayString($array, $key, $default)
    {
		return self::safeArrayGetString($array, $key, $default);
	}

    static public function getSafeArrayBool($array, $key, $default)
    {
		$rc = $default;
		$s = self::getSafeArrayString($array, $key, null);
		if (isset($s))
		{
			$rc = $s;
		}

		return $rc;
	}

    static public function safeArrayGetString($array, $key, $default)
    {
        $v = $default;

        if (isset($array) && is_array($array) && array_key_exists($key, $array))
        {
            $v = $array[$key];
        }

        return $v;
    }

    static public function cleanHtml($text)
	{
		$v = preg_replace('#style="(.*?)"#is', "", $text); // remove styles
		$v = preg_replace('#<p >#is', "<p>", $v); // fix <p>
		//one time fix: $v = self::convertParens($v);

		return $v;
	}

}
