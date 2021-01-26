<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Site;
use App\Status;
use App\Tools;
use App\User;

define('PREFIX', 'sites');
define('VIEWS', 'sites');
define('LOG_CLASS', 'SiteController');

class SiteController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except(['index', 'view', 'permalink']);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];

		try
		{
			$records = Site::select()
				->get(5);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			'languages' => getLanguageOptions(/* includeAll = */ true),
			]);
	}

    public function create(Request $request)
    {
		$record = new Site();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);
		$record->frontpage  	= trimNull($request->frontpage);
        $record->language_flag  = $request->language_flag;

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('msgs.New record has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error adding new record'));
			return back();
		}

		return redirect($this->redirectTo . '/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);

		$record = null;

		try
		{
			$record = Site::select()
				->where('site_id', SITE_ID)
				->where('published_flag', 1)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Record not found'), ['permalink' => $permalink]);
			return back();
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Site $site)
    {
		$record = $site;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Site $site)
    {
		$record = $site;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languages' => getLanguageOptions(/* includeAll = */ true),
			]);
    }

    public function update(Request $request, Site $site)
    {
		$record = $site;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->frontpage = copyDirty($record->frontpage, $request->frontpage, $isDirty, $changes);
		$record->language_flag = copyDirty($record->language_flag, $request->language_flag, $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo(LOG_CLASS, __('msgs.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException(LOG_CLASS, $e->getMessage(), __('msgs.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo(LOG_CLASS, __('msgs.No changes made'), ['record_id' => $record->id]);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}

    public function confirmDelete(Site $site)
    {
		$record = $site;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Site $site)
    {
		$record = $site;

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
			$record = Site::withTrashed()
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
			$records = Site::withTrashed()
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

    public function publish(Request $request, Site $site)
    {
		$record = $site;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Site $site)
    {
		$record = $site;

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

}