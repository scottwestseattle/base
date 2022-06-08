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

        $this->middleware('auth')->only([
			'index',
		]);

        $this->middleware('owner')->only([
			'update',
		]);

		parent::__construct();
	}

    public function admin(Request $request)
    {
		$records = History::get();
        $history['maxDays'] = 5;

		return view(VIEWS . '.admin', [
			'history' => $records,
		]);
    }

    public function index(Request $request)
    {
        if (isAdmin())
        {
            return $this->admin($request);
        }

		$records = History::get();

		return view(VIEWS . '.index', [
			'history' => $records,
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

		$record->program_name = copyDirty($record->program_name, $request->program_name, $isDirty, $changes);
		$record->program_id = copyDirty($record->program_id, $request->program_id, $isDirty, $changes);
		$record->session_name = copyDirty($record->session_name, $request->session_name, $isDirty, $changes);
		$record->session_id = copyDirty($record->session_id, $request->session_id, $isDirty, $changes);
		$record->route = copyDirty($record->route, $request->route, $isDirty, $changes);
		$record->type_flag = copyDirty($record->type_flag, $request->type_flag, $isDirty, $changes);
		$record->subtype_flag = copyDirty($record->subtype_flag, $request->subtype_flag, $isDirty, $changes);
		$record->count = copyDirty($record->count, $request->count, $isDirty, $changes);
		$record->seconds = copyDirty($record->seconds, $request->seconds, $isDirty, $changes);
		$record->score = copyDirty($record->score, $request->score, $isDirty, $changes);
		$record->extra = copyDirty($record->extra, $request->extra, $isDirty, $changes);

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

		return redirect($this->redirectTo);
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

    public function rss(Request $request)
    {
        $type = isset($request['type']) ? intval($request['type']) : 0;

		$records = []; // make this countable so view will always work

		try
		{
			$records = History::getRss($type);

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
	// http://domain.com/history/add-public?type=2&programName=Plancha&programId=14&sessionName=Day+4&sessionId=4&seconds=750
    public function addPublic(Request $request)
    {
        $msg = History::add($request);
		//$msg = History::add($typeFlag, urldecode($programName), $programId, urldecode($sessionName), $sessionId, $seconds);

        $rc = '<a href="/history">' . $msg . '</a>';

		return $rc;
	}
}
