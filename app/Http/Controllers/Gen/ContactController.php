<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Gen\Contact;
use App\Site;
use App\Status;
use App\User;

define('PREFIX', '/contacts/');
define('SHOW', '/contacts/view/');
define('VIEWS', 'gen.contacts');
define('LOG_CLASS', 'ContactController');

class ContactController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('auth')->only([
			'add',
			'create',
			'index',
		]);

        $this->middleware('owner')->only([
			'edit',
			'update',
			'confirmDelete',
			'delete',
			'show',
			'view',
		]);

		parent::__construct();
	}

    public function admin(Request $request)
    {
		$records = [];

		try
		{
			$records = Contact::select()
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
			$records = Contact::select()
			    ->where('user_id', Auth::id())
				->orderByRaw('name')
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
		$record = new Contact();

		$record->user_id 		= Auth::id();
		$record->name 			= trimNull($request->name);
        $record->access         = trimNull($request->access);
        $record->lastUpdated    = trimNull($request->lastUpdated);
        $record->verifyMethod   = trimNull($request->verifyMethod);
        $record->address        = trimNull($request->address);
		$record->notes      	= trimNull($request->notes);
		$record->numbers        = format_number(trimNull($request->numbers));

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

		return redirect(PREFIX);
    }

	public function view(Request $request, Contact $contact)
    {
		$record = $contact;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Contact $contact)
    {
		$record = $contact;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Contact $contact)
    {
		$record = $contact;

		$isDirty = false;
		$changes = '';

		$record->name = copyDirty($record->name, $request->name, $isDirty, $changes);
		$record->user_id = copyDirty($record->user_id, Auth::id(), $isDirty, $changes);
		$record->access = copyDirty($record->access, $request->access, $isDirty, $changes);
		$record->lastUpdated = copyDirty($record->lastUpdated, $request->lastUpdated, $isDirty, $changes);
		$record->verifyMethod = copyDirty($record->verifyMethod, $request->verifyMethod, $isDirty, $changes);
		$record->address = copyDirty($record->address, $request->address, $isDirty, $changes);
		$record->notes = copyDirty($record->notes, $request->notes, $isDirty, $changes);
        $request->numbers = format_number($request->numbers);
		$record->numbers = copyDirty($record->numbers, $request->numbers, $isDirty, $changes);

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

		return redirect(PREFIX);
	}

    public function confirmDelete(Contact $contact)
    {
		$record = $contact;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Contact $contact)
    {
		$record = $contact;

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
			$record = Contact::withTrashed()
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
			$records = Contact::withTrashed()
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

    public function publish(Request $request, Contact $contact)
    {
		$record = $contact;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Contact $contact)
    {
		$record = $contact;

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
			logInfo(LOG_CLASS, __('base.Status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

}
