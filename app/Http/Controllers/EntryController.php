<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Entry;
use App\Status;
use App\User;

define('PREFIX', 'entries');
define('VIEWS', 'entries');
define('LOG_CLASS', 'EntryController');

class EntryController extends Controller
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
			$records = Entry::select()
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
			]);
	}

    public function create(Request $request)
    {
		$record = new Entry();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);

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
			$record = Entry::select()
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

	public function view(Entry $entry)
    {
		$record = $entry;

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

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);

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

    public function confirmDelete(Entry $entry)
    {
		$record = $entry;

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
			$record = Entry::withTrashed()
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
			$records = Entry::withTrashed()
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

		$record->wip_flag = $request->wip_flag;
		$record->release_flag = $request->release_flag;

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
