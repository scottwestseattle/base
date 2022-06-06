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
define('VIEW', '/articles/view/');
define('VIEWS', 'gen.articles');
define('LOG_CLASS', 'ArticleController');

class ArticleController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index',
            'view',
            'permalink',
            'add', 'create',
            'edit', 'update',
            'confirmDelete', 'delete',
            'read', 'flashcards', 'flashcardsView'
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

    public function index(Request $request, $orderBy = null)
    {
        $orderBy = strtolower(alpha($orderBy));
		$records = [];
		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

        $parms = Site::getLanguage();
        $parms['type'] = ENTRY_TYPE_ARTICLE;
        $parms['orderBy'] = $orderBy;
        $options = [];

		try
		{
            // get public articles
            $parms['release'] = 'public';
            $options['public'] = Entry::getRecentList($parms);

            // get private articles
            $parms['release'] = 'private';
            $options['private'] = Entry::getRecentList($parms);

            // get other peoples articles
            $parms['release'] = 'other';
            $options['other'] = isAdmin() ? Entry::getRecentList($parms) : null;
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting articles'));
		}

		return view(VIEWS . '.index', [
			'options' => $options,
		]);
    }

    public function permalink(Request $request, $permalink)
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

		//todo: $id = isset($record) ? $record->id : null;
		//todo: $visitor = $this->saveVisitor(LOG_MODEL_ENTRIES, LOG_PAGE_PERMALINK, $id);
		//todo: $isRobot = isset($visitor) && $visitor->robot_flag;

		if (isset($record))
		{
			$record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
			Entry::countView($record);
			$options['wordCount'] = str_word_count($record->description); // count it before <br/>'s are added
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

		return view(VIEWS . '.view', [
			'options' => $options,
			'record' => $record,
			]);
	}

    public function add(Request $request)
    {
		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
			'dates' => DateTimeEx::getDateControlDates(),
			'filter' => DateTimeEx::getDateFilter($request, /* today = */ true),
			]);
	}

    public function create(Request $request)
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
        }

		$record->title 				= trimNull($title);
		$record->description_short	= trimNull($description_short);
		$record->description		= trimNull($description);
		$record->source				= trimNull($source);
		$record->source_credit		= trimNull($source_credit);
		$record->source_link		= trimNull($source_link);
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

	public function edit(Entry $entry)
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

    public function update(Request $request, Entry $entry)
    {
		$record = $entry;
		$prevTitle = $record->title;

		$record->site_id 			= Site::getId();

		$title 				= $request->title;
		$description_short	= $request->description_short;
		$description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$description_trx	= Str::limit($request->description_translation, MAX_DB_TEXT_COLUMN_LENGTH);
		$source				= $request->source;
		$source_credit		= $request->source_credit;
		$source_link		= $request->source_link;
		$display_date       = $request->source_link;

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

    public function confirmDelete(Entry $entry)
    {
		$record = $entry;

		$record->description = nl2br($record->description);

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Entry $entry)
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

    public function undelete(Request $request, $id)
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

    public function deleted()
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

    public function publish(Request $request, Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Entry $entry)
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

    public function read(Request $request, Entry $entry)
    {
        $count = isset($request['count']) ? intval($request['count']) : null;
        $random = isset($request['random']) ? boolval($request['random']) : false;

        $options['randomOrder'] = $random;
        $options['count'] = $count;
        $options['return'] = PREFIX;

        return $this->reader($entry, $options);
    }

	public function flashcards(Request $request, Entry $entry)
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

        $history = History::getArray($record->title, $record->id, HISTORY_TYPE_ARTICLE, LESSON_TYPE_QUIZ_FLASHCARDS, $count);

		return view($settings['view'], [
			'touchPath' => '/',
			'sentenceCount' => count($quiz),
			'quizCount' => $count,
			'records' => $quiz,
			'canEdit' => true,
			'isMc' => true, //$lesson->isMc($reviewType),
            'returnPath' => '/articles/view/' . $entry->permalink,
			'parentTitle' => $record->title,
			'settings' => $settings,
			'article' => true,
			'random' => $random,
			'history' => $history,
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
