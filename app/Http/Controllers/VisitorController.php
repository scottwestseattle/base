<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Visitor;
use App\Status;
use App\Tools;
use App\User;

define('PREFIX', 'visitors');
define('VIEWS', 'visitors');
define('LOG_CLASS', 'VisitorController');

class VisitorController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except(['index', 'view', 'permalink']);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];
        $count = 0;

		try
		{
			$records = Visitor::select()
				//->where('robot_flag', 0)
				->orderByRaw('id DESC')
				->limit(100)
				->get();

			$count = Visitor::select()
				->count();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
			'count' => $count,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request)
    {
		$record = new Visitor();

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
			$record = Visitor::select()
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

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Visitor $visitor)
    {
		$record = $visitor;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Visitor $visitor)
    {
		$record = $visitor;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Visitor $visitor)
    {
		$record = $visitor;

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

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}

    public function confirmDelete(Visitor $visitor)
    {
		$record = $visitor;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Visitor $visitor)
    {
		$record = $visitor;

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
			$record = Visitor::withTrashed()
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
			$records = Visitor::withTrashed()
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

    public function publish(Request $request, Visitor $visitor)
    {
		$record = $visitor;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Visitor $visitor)
    {
		$record = $visitor;

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
			logInfo(LOG_CLASS, __('base.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

}
