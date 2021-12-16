<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Log;

use App\Entry;
use App\Gen\Article;
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
            'read',
            'add', 'create',
            'edit', 'update',
        ]);

        $this->middleware('auth')->only([
			'add',
			'create',
		]);

        $this->middleware('owner')->only([
			'edit',
			'update',
		]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];
		//$this->saveVisitor(LOG_MODEL_ARTICLES, LOG_PAGE_INDEX);

        $parms = Site::getLanguage();
        $parms['type'] = ENTRY_TYPE_ARTICLE;
        $options = [];

		try
		{
            // get public articles
            $parms['release'] = 'public';
    		$options['public'] = Entry::getRecentList($parms);

            // get private articles
            $parms['release'] = 'private';
    		$options['private'] = Entry::getRecentList($parms);
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
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
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

    public function add()
    {
		return view(VIEWS . '.add', [
			'languageOptions' => getLanguageOptions(),
			'selectedOption' => getLanguageId(),
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
            $source = alphanumHarsh($source);
            $source_credit = alphanumHarsh($source_credit);
            $source_link = alphanumHarsh($source_link);
        }

		$record->title 				= trimNull($title);
		$record->description_short	= trimNull($description_short);
		$record->description		= trimNull($description);
		$record->source				= trimNull($source);
		$record->source_credit		= trimNull($source_credit);
		$record->source_link		= trimNull($source_link);

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

		return redirect(VIEW . $record->permalink);
    }

	public function edit(Entry $entry)
    {
		$record = $entry;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languageOptions' => getLanguageOptions(),
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
		$source				= $request->source;
		$source_credit		= $request->source_credit;
		$source_link		= $request->source_link;

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
            $source = alphanumHarsh($source);
            $source_credit = alphanumHarsh($source_credit);
            $source_link = alphanumHarsh($source_link);
        }

		$record->title 				= trimNull($title);
		$record->description_short	= trimNull($description_short);
		$record->description		= trimNull($description);
		$record->source				= trimNull($source);
		$record->source_credit		= trimNull($source_credit);
		$record->source_link		= trimNull($source_link);
		$record->language_flag		= isset($request->language_flag) ? $request->language_flag : Site::getLanguage()['id'];
		$record->type_flag 			= ENTRY_TYPE_ARTICLE;
		$record->permalink          = createPermalink($record->title, $record->created_at);

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

		return redirect(VIEW . $record->permalink);
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
			logInfo(LOG_CLASS, __('proj.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error deleting record'), ['record_id' => $record->id]);
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
			logInfo(LOG_CLASS, __('proj.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function read(Request $request, Entry $entry)
    {
        return $this->reader($entry, ['return' => PREFIX]);
    }

}
