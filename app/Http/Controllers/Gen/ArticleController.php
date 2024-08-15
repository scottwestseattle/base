<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Log;

use App\DateTimeEx;
use App\Entry;
use App\Gen\Article;
use App\Gen\History;
use App\Gen\Spanish;
use App\Quiz;
use App\Site;
use App\Status;
use App\User;

define('PREFIX', '/articles/');
define('VIEW', '/' . app()->getLocale() . '/articles/view/');
define('VIEWS', 'gen.articles');
define('LOG_CLASS', 'ArticleController');

class ArticleController extends Controller
{
	private $redirectTo = null;

	public function __construct()
	{
	    $this->redirectTo = route('articles', ['locale' => app()->getLocale()]);

        $this->middleware('admin')->except([
            'index',
            'view',
            'permalink',
            'add', 'create',
            'edit', 'update',
            'confirmDelete', 'delete',
            'read', 'flashcards', 'flashcardsView',
            'quiz'
        ]);

        $this->middleware('auth')->only([
			'add',
			'create',
		]);

        $this->middleware('owner')->only([
			'edit',
			'update',
			'confirmDelete',
			'delete',
		]);

		parent::__construct();
	}

    public function index(Request $request)
    {
        //
        // get the url parameters
        //
        $orderBy = isset($request['sort']) ? $request['sort'] : null;
        $orderBy = strtolower(alphanum($orderBy, false, '-')); // convert to alphanum and allow '-'

        $start = isset($request['start']) ? intval($request['start']) : 0;

        $limit = isset($request['count']) ? intval($request['count']) : DEFAULT_LIST_LIMIT;
        $limit = intval($limit) < 0 ? PHP_INT_MAX : intval($limit);

		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

        $parms = Site::getLanguage();
        $parms['type'] = ENTRY_TYPE_ARTICLE;
        $parms['orderBy'] = $orderBy;
        $parms['start'] = $start;
        $parms['limit'] = $limit;

		try
		{
            // get public articles
            $parms['release'] = 'public';
            $parms['public'] = Entry::getRecentList($parms);

            // get private articles
            $parms['release'] = 'private';
            $parms['private'] = Entry::getRecentList($parms);

            // get other peoples articles
            $parms['release'] = 'other';
            $parms['other'] = isAdmin() ? Entry::getRecentList($parms) : null;

            $parms['activeTab'] = session('articlesTab');
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting articles'));
		}

		return view(VIEWS . '.index', [
			'options' => $parms,
		]);
    }

    public function permalink(Request $request, $lang, $permalink)
    {
 		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = Status::getReleaseFlagForUserLevel();
        $releaseFlagCondition = Status::getConditionForUserLevel();

		try
		{
			$record = Entry::select()
				->where('permalink', $permalink)
				->where(function ($query) use($releaseFlagCondition, $releaseFlag) {$query
    				->where('release_flag', $releaseFlagCondition, $releaseFlag)
					->orWhere('user_id', Auth::id()) // or he is the owner
					;})
				->first();

			if (blank($record))
			    throw new \Exception('article not found');

		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Article not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return $this->view($request, $record);
	}

    public function view(Request $request, Entry $entry)
    {
        $record = $entry;
		$next = null;
		$prev = null;
		$options['wordCount'] = null;
		$options['letterCount'] = null;

		//todo: $id = isset($record) ? $record->id : null;
		//todo: $visitor = $this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
		//todo: $isRobot = isset($visitor) && $visitor->robot_flag;

		if (isset($record))
		{
			$record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
			Entry::countView($record);

			// count the words
			$options['wordCount'] = str_word_count($record->description); // count it before <br/>'s are added

			// count the letters
			$options['letterCount'] = countLetters($record->description);

			// count the lines
			$options['lineCount'] = count($record->getSentences());
			$options['sentences'] = Spanish::getSentences($record->description);
        	$options['qnaPorPara'] = Quiz::mineQna($options['sentences'], Quiz::getQnaParms('por'));
        	$options['qnaEraFue'] = Quiz::mineQna($options['sentences'], Quiz::getQnaParms('era'));

            //dd($options);

			if (strlen($record->description_translation) > 0)
			{
	        	$options['sentences_translation'] = Spanish::getSentences($record->description_translation);
	        	$options['translation_matches'] = (count($options['sentences']) === count($options['sentences_translation']));

                if (count($options['sentences']) >= count($options['sentences_translation']))
    	        	$options['translation'] = Quiz::makeFlashcards($record->description, $record->description_translation);
    	        else
    	        	$options['translation'] = Quiz::makeFlashcards($record->description_translation, $record->description);
			}

			$record->description = nl2br($record->description);
		}
		else
		{
		    $msg = 'record not set';
		    logError(__FUNCTION__ . ': ' . $msg, $msg);
    		return redirect($this->redirectTo);
		}

        $options['backLink'] = '/articles';
        $options['index'] = 'articles';
        $options['backLinkText'] = __('ui.Back to List');
        $options['page_title'] = trans_choice('proj.Article', 1) . ' - ' . $record->title;

        //todo: $next = Entry::getNextPrevEntry($record);
        //todo: $prev = Entry::getNextPrevEntry($record, /* next = */ false);
        //dump($options);

		return view(VIEWS . '.view', [
			'options' => $options,
			'record' => $record,
			]);
	}

    public function add(Request $request, $locale)
    {
		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
			'dates' => DateTimeEx::getDateControlDates(),
			'filter' => DateTimeEx::getDateFilter($request, /* today = */ true),
			]);
	}

    public function create(Request $request, $locale)
    {
		$record = new Entry();

		$record->site_id             = Site::getId();
		$record->user_id             = Auth::id();

		$title 				= $request->title;
		$description_short	= $request->description_short;
		$description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$source				= $request->source;
		$source_credit		= $request->source_credit;
		$source_link		= $request->source_link;

        // get the options strings from the checkboxes
        $options            = '';
		$options            .= isset($request->read_reverse) ? OPTION_READ_REVERSE . ';' : '';
		$options            .= isset($request->read_random) ? OPTION_READ_RANDOM . ';' : '';

   		$urlChanged = false;

        if (isAdmin())
        {
            // anything goes in the text
    		$record->release_flag = RELEASEFLAG_PUBLIC;
        }
        else
        {
    		// deep clean the entries
            $title = alphanumHarsh($title);
            $description_short = alphanumHarsh($description_short);
            $description = alphanumHarsh($description);
            $source = alphanumHarsh($source);
            $source_credit = alphanumHarsh($source_credit);
    		$record->release_flag = RELEASEFLAG_PRIVATE;
    		$source_link = cleanUrl($source_link, $urlChanged);
            $options = alphanumHarsh($options);
        }

		$record->title 				= trimNull($title);
		$record->description_short	= trimNull($description_short);
		$record->description		= trimNull($description);
		$record->source				= trimNull($source);
		$record->source_credit		= trimNull($source_credit);
		$record->source_link		= trimNull($source_link);
		$record->options    		= trimNull($options);
		$record->user_id 			= Auth::id();

		$filter = DateTimeEx::getDateFilter($request);
		$record->display_date	= trimNull($filter['from_date']);

		$record->wip_flag 			= WIP_FINISHED;
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
		$record->type_flag 			= ENTRY_TYPE_ARTICLE;
		$record->permalink          = createPermalink($record->title, $record->created_at);

		try
		{
			if ($record->type_flag <= 0)
				throw new \Exception('Entry type not set');
			if (!isset($record->title))
				throw new \Exception('Title not set');
			if (!isset($record->display_date))
				throw new \Exception('Date not set');
			if ($urlChanged)
				throw new \Exception('URL has been changed'); // throws if we are trimming too many url characters

			$record->save();

			$msg = 'Entry has been added';
			$status = 'success';
			if (strlen($request->description) > MAX_DB_TEXT_COLUMN_LENGTH)
			{
				$msg .= ' - DESCRIPTION TOO LONG, TRUNCATED';
				$status = 'danger';
			}

			logInfo(LOG_CLASS, __('base.New entry has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'), ['url' => $source_link]);
			return back();
		}

		return redirect(VIEW . $record->permalink);
    }

	public function edit(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

        $dates = DateTimeEx::getDateControlDates();
		$filter = DateTimeEx::getDateControlSelectedDate($record->display_date);

		$sentences = Spanish::getSentences($record->description);
		$sentences_translation = Spanish::getSentences($record->description_translation);

   		$flashcards = Quiz::makeFlashcards($record->description, $record->description_translation);

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languageOptions' => getLanguageOptions(),
			'dates' => $dates,
			'filter' => $filter,
			'sentences' => Spanish::getString($sentences),
			'sentences_translation' => Spanish::getString($sentences_translation),
			'flashcards' => $flashcards,
			]);
    }

    public function update(Request $request, $locale, Entry $entry)
    {
		$record = $entry;
		$prevTitle = $record->title;
        $options = '';

		$record->site_id 			= Site::getId();

		$title 				= $request->title;
		$description_short	= $request->description_short;
		$description		= Str::limit(strip_tags($request->description), MAX_DB_TEXT_COLUMN_LENGTH);
		$description_trx	= Str::limit(strip_tags($request->description_translation), MAX_DB_TEXT_COLUMN_LENGTH);
		$source				= $request->source;
		$source_credit		= $request->source_credit;
		$source_link		= $request->source_link;
		$display_date       = $request->source_link;

        // get the options strings from the checkboxes
		$options            .= isset($request->read_reverse) ? OPTION_READ_REVERSE . ';' : '';
		$options            .= isset($request->read_random) ? OPTION_READ_RANDOM . ';' : '';

        // brute force cleaning: remove any blank lines with spaces because they throw off the quizes and line based operations
        $description        = str_replace("\r\n \r\n", "\r\n\r\n", $description);
        $description_trx    = str_replace("\r\n \r\n", "\r\n\r\n", $description_trx);

		// put the date together from the mon day year pieces
		$filter = DateTimeEx::getDateFilter($request);
		$date = trimNull($filter['from_date']);
		$record->display_date = $date;

   		$urlChanged = false;

        if (isAdmin())
        {
            // anything goes
        }
        else
        {
    		// deep clean the entries
            $title = alphanumHarsh($title);
            $description_short = alphanumHarsh($description_short);
            $description = alphanumHarsh($description);
            $description_trx = alphanumHarsh($description_trx);
            $source = alphanumHarsh($source);
            $source_credit = alphanumHarsh($source_credit);
    		$source_link = cleanUrl($source_link, $urlChanged);
            $options = alphanumHarsh($options);
        }

		$record->title 				    = trimNull($title);
		$record->description_short	    = trimNull($description_short);
		$record->description		    = trimNull($description);
		$record->description_translation = trimNull($description_trx);
		$record->source				    = trimNull($source);
		$record->source_credit		    = trimNull($source_credit);
		$record->source_link		    = trimNull($source_link);
		$record->language_flag		    = isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
		$record->type_flag 			    = ENTRY_TYPE_ARTICLE;
		$record->permalink              = createPermalink($record->title, $record->created_at);
		$record->options                = trimNull($options);

		try
		{
			if ($urlChanged)
				throw new \Exception('URL has been changed'); // throws if we are trimming too many url characters

			$record->save();

			logInfo('update article', null, ['title' => $record->title, 'id' => $record->id, 'prevTitle' => $prevTitle, 'title' => $record->title]);

			$msg = 'Entry has been updated';
			$status = 'success';
			if (strlen($request->description) > MAX_DB_TEXT_COLUMN_LENGTH)
			{
				$msg .= ' - DESCRIPTION TOO LONG, TRUNCATED';
				$status = 'danger';
			}

			logInfo(LOG_CLASS, __('base.' . $msg), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record'), ['url' => $source_link]);
			return back();
		}

		return redirect(VIEW . $record->permalink);
	}

    public function confirmDelete(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		$record->description = nl2br($record->description);

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $locale, $id)
    {
		$id = intval($id);

		try
		{
			$record = Article::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('proj.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted(Request $request, $locale)
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Article::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, $locale, Entry $entry)
    {
		$record = $entry;

        if ($request->isMethod('get'))
        {
            // quick publish, set to toggle public / private
            $record->wip_flag = $record->isFinished() ? getConstant('wip_flag.dev') : getConstant('wip_flag.finished');
            $record->release_flag = $record->isPublic() ? RELEASEFLAG_PRIVATE : RELEASEFLAG_PUBLIC;
        }
        else
        {
            $record->wip_flag = $request->wip_flag;
            $record->release_flag = $request->release_flag;
        }

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('proj.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo . 'view/' . $record->permalink);
    }

    public function read(Request $request, $locale, Entry $entry)
    {
        $parms = crackParms($request, ['count' => null]);
        //dump($parms);

        $random = isset($request['random']) ? boolval($request['random']) : false;
        $parms['randomOrder'] = $random;
        $parms['readRandom'] = $entry->readRandom();
        $parms['readReverse'] = $entry->readReverse();

        return $this->reader($entry, $parms);
    }

    //todo: make a quiz from an article: para/por, estaba/estuvo, ser/estar, etc.
	public function quiz(Request $request, $locale, Entry $entry, $qnaType)
    {
        $record = $entry;
		$reviewType = 2;
		$count = 0;
        $qnaType = alpha($qnaType);
		try
		{
		    // take the article text and look for keywords to make into qna
		    // ex: Estábamos preparados [por, para] ello. - para
		    // ex: [Estábamos, Estuvimos] preparados para ella. - Estábamos
		    if ($qnaType == 'por')
		    {
    		    $text = Quiz::mineQna($entry->description, Quiz::getQnaParms('por'))['text'];
		    }
		    else if ($qnaType == 'era')
		    {
    		    $text = Quiz::mineQna($entry->description, Quiz::getQnaParms('era'))['text'];
		    }

    		$quiz = Quiz::makeQnaFromText($text); // split text into questions and answers
    		//dd($quiz);
		}
		catch (\Exception $e)
		{
			$msg = 'Error making article qna';
   			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
			return back();
		}

		$settings = Quiz::getSettings($reviewType);
        $title = $record->title;

        $parms = crackParms($request);
        $parms['sessionName'] = $record->title;
        $parms['sessionId'] = $record->id;
        $history = History::getArray($title, $record->id, HISTORY_TYPE_LESSON, $parms['source'], $reviewType, $count, $parms);

		return view($settings['view'], [
			'prev' => null,
			'next' => null,
			'sentenceCount' => count($quiz),
			'quizCount' => $count,
			'records' => $quiz,
			'canEdit' => true,
			'isMc' => true, //$lesson->isMcOld($reviewType),
            'returnPath' => Site::getReturnPath(),
			'parentTitle' => $title,
			'settings' => $settings,
			'random' => 0,
			'history' => $history,
			'touchPath' => null, //todo: need to implement stats
			]);
    }


	public function flashcards(Request $request, $locale, Entry $entry)
    {
        $record = $entry;
		$reviewType = 1;
        $quiz = null;

        $count = isset($request['count']) ? intval($request['count']) : PHP_INT_MAX;
        $random = isset($request['random']) ? boolval($request['random']) : false;

		try
		{
    		$quiz = Quiz::makeFlashcards($record->description, $record->description_translation);
		}
		catch (\Exception $e)
		{
			$msg = 'Error making flashcards';
   			logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
			return back();
		}

		$settings = Quiz::getSettings($reviewType);
        $history = History::getArray($record->title, $record->id, HISTORY_TYPE_ARTICLE, History::getSubType($request), LESSON_TYPE_QUIZ_FLASHCARDS, $count);

		return view($settings['view'], [
			'sentenceCount' => count($quiz),
			'quizCount' => $count,
			'records' => $quiz,
			'canEdit' => true,
			'isMc' => true, //$lesson->isMc($reviewType),
            'returnPath' => Site::getReturnPath(),
			'parentTitle' => $record->title,
			'settings' => $settings,
			'article' => true,
			'random' => $random,
			'history' => $history,
			'touchPath' => null, // no stats because it's an article instead of definitions
		]);
    }

	public function flashcardsView(Request $request, Entry $entry)
    {
		$record = $entry;

   		$flashcards = Quiz::makeFlashcards($record->description, $record->description_translation);

		return view('shared.flashcards-view', [
			'records' => $flashcards,
			]);
    }

}
