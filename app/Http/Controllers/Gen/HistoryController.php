<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Gen\History;
use App\Site;
use App\Status;
use App\User;

define('PREFIX', '/history/');
define('SHOW', '/history/show/');
define('VIEWS', 'gen.history');
define('LOG_CLASS', 'HistoryController');

class HistoryController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'rss', 'addPublic'
        ]);

		parent::__construct();
	}

    public function admin(Request $request)
    {
		$records = [];

		try
		{
			$records = History::select()
				->orderBy('id', 'DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function index(Request $request)
    {
		$records = [];

		try
		{
			$records = History::select()
				//->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->orderByRaw('id DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request)
    {
		$record = new History();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);
        $record->permalink      = createPermalink($record->title);

		try
		{
			$record->save();

			logInfo(LOG_CLASS, __('base.New record has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'));
			return back();
		}

		return redirect($this->redirectTo . '/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
    {
		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = Status::getReleaseFlagForUserLevel();
        $releaseFlagCondition = Status::getConditionForUserLevel();

		try
		{
			$record = History::select()
				//->where('site_id', SITE_ID)
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('permalink not found');
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return $this->view($request, $record);
	}

	public function view(Request $request, History $history)
    {
		$record = $history;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(History $history)
    {
		$record = $history;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, History $history)
    {
		$record = $history;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
        $record->permalink = copyDirty($record->permalink, createPermalink($request->title, $record->created_at), $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo(LOG_CLASS, __('base.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo(LOG_CLASS, __('base.No changes made'), ['record_id' => $record->id]);
		}

		return redirect(SHOW . $record->id);
	}

    public function confirmDelete(History $history)
    {
		$record = $history;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, History $history)
    {
		$record = $history;

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
			$record = History::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = History::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function rss()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = History::getRss();

			//foreach($records as $record)
			//{
			//	dd($record);
			//}
		}
		catch (\Exception $e)
        {
			//dd($e);
			$msg = 'Error getting History rss';
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting history rss'));
		}

		return view(VIEWS . '.rss', [
			'records' => $records,
		]);
    }

	// sample url:
	// http://localhost/history/add-public/Plancha/14/Day+4/4/750
    public function addPublic($programName, $programId, $sessionName, $sessionId, $seconds)
    {
		$msg = History::add(urldecode($programName), $programId, urldecode($sessionName), $sessionId, $seconds);

		return $msg;
	}
}
