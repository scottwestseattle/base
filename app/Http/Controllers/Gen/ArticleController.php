<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Log;

use App\Gen\Article;

use App\Entry;
use App\Site;
use App\User;

define('PREFIX', '/articles/');
define('VIEWS', 'gen.articles');
define('LOG_CLASS', 'ArticleController');

class ArticleController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'add', 'edit',
            'read',
        ]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];
		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

		try
		{
		    $parms = Site::getLanguage();
		    $parms['type'] = ENTRY_TYPE_ARTICLE;

			//$records = Entry::getArticles();
		    $records = Entry::getRecentList($parms);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error getting articles'));
		}

		$options['articles'] = $records;

		return view(VIEWS . '.index', [
			'options' => $options,
		]);
    }

    public function view(Request $request, $permalink)
    {
 		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = getReleaseFlagForUserLevel();
        $releaseFlagCondition = getConditionForUserLevel();

		try
		{
			$record = Entry::select()
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('article not found');

		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Article not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

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
			return $this->pageNotFound404($permalink);
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

    public function add()
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request)
    {
		$record = new Entry();

		$record->site_id             = Site::getId();
		$record->user_id             = Auth::id();
		//$record->parent_id 			= $request->parent_id;
		$record->title 				= trimNull($request->title);
		$record->description_short	= trimNull($request->description_short);
		$record->description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$record->source				= trimNull($request->source);
		$record->source_credit		= trimNull($request->source_credit);
		$record->source_link		= trimNull($request->source_link);
		$record->display_date 		= timestamp();
		$record->release_flag 		= RELEASEFLAG_PUBLIC;
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

			$record->save();

			// set up the book tag (if it's a book).  has to be done after the entry is created and has an id
			$record->updateBookTag();

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
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'));
			return back();
		}

		return redirect($record->getRedirect()['view']);
    }

    public function permalink(Request $request, $permalink)
    {
		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = getReleaseFlagForUserLevel();
        $releaseFlagCondition = getConditionForUserLevel();

		try
		{
			$record = Article::select()
				//->where('site_id', SITE_ID)
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('permalink not found');
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function edit(Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Entry $entry)
    {
		$record = $entry;
		$prevTitle = $record->title;

		$record->site_id 			= Site::getId();
		$record->title 				= trimNull($request->title);
		$record->description_short	= trimNull($request->description_short);
		$record->description		= Str::limit($request->description, MAX_DB_TEXT_COLUMN_LENGTH);
		$record->source				= trimNull($request->source);
		$record->source_credit		= trimNull($request->source_credit);
		$record->source_link		= trimNull($request->source_link);
		//todo: $record->display_date 		= Controller::getSelectedDate($request);
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
		$record->type_flag 			= ENTRY_TYPE_ARTICLE;
		$record->permalink          = createPermalink($record->title, $record->created_at);

		$record->updateBookTag();

		try
		{
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
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating article'));
			return back();
		}

		return redirect(PREFIX . $record->permalink);
	}

    public function confirmDelete(Article $article)
    {
		$record = $article;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Article $article)
    {
		$record = $article;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('msgs.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error deleting record'), ['record_id' => $record->id]);
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
			logInfo(LOG_CLASS, __('msgs.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error undeleting record'), ['record_id' => $record->id]);
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
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, Article $article)
    {
		$record = $article;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Article $article)
    {
		$record = $article;

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
			logInfo(LOG_CLASS, __('msgs.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function read(Request $request, Entry $entry)
    {
        return $this->reader($entry, ['return' => PREFIX]);
    }

}
