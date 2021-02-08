<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Log;

use App\Entry;
use App\Gen\Spanish;
use App\Site;
use App\User;

define('PREFIX', 'entries');
define('LOG_CLASS', 'EntryController');

class EntryController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'articles', 'viewArticle', 'read',
        ]);

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
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(PREFIX . '.index', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(PREFIX . '.add', [
			]);
	}

    public function addArticle()
    {
		return view(PREFIX . '.addArticle', [
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
		$record->type_flag 			= $request->type_flag;
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
			$record = Entry::select()
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

		return view(PREFIX . '.view', [
			'record' => $record,
			]);
	}

	public function view(Entry $entry)
    {
		$record = $entry;

		return view(PREFIX . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Entry $entry)
    {
		$record = $entry;

		return view(PREFIX . '.edit', [
			'record' => $record,
			]);
    }

	public function editArticle(Entry $entry)
    {
		$record = $entry;

		return view(PREFIX . '.editArticle', [
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

		return redirect($record->getRedirect()['view']);
	}

    public function confirmDelete(Entry $entry)
    {
		$record = $entry;

		return view(PREFIX . '.confirmdelete', [
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

		return redirect($record->getRedirect()['index']);
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
			$records = Entry::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(PREFIX . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, Entry $entry)
    {
		$record = $entry;

		return view(PREFIX . '.publish', [
			'record' => $record,
			'release_flags' => Entry::getReleaseFlags(),
			'wip_flags' => Entry::getWipFlags(),
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
			logInfo(LOG_CLASS, __('base.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function read(Request $request, Entry $entry)
    {
        return $this->reader($entry, ['return' => 'entries']);
    }

}
