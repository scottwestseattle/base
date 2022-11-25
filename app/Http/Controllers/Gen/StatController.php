<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Gen\Stat;
use App\Site;
use App\Status;
use App\User;

define('PREFIX', '/stats/');
define('SHOW', '/stats/show/');
define('VIEWS', 'gen.stats');
define('LOG_CLASS', 'StatController');

class StatController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'updateStats',
        ]);

        $this->middleware('auth')->only([
            'updateStats',
		]);


		parent::__construct();
	}

    public function admin(Request $request)
    {
		$records = [];

		try
		{
			$records = Stat::select()
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
			$records = Stat::select()
				->orderByRaw('updated_at DESC')
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
		$record = new Stat();

		$record->user_id 		= Auth::id();
		$record->definition_id	= intval($request->definition_id);
		$record->reads          = intval($request->reads);
        $record->views          = intval($record->views);
        $record->qna_attempts   = intval($record->qna_attempts);
        $record->qna_correct    = intval($record->qna_correct);

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

	public function view(Request $request, Stat $stat)
    {
		$record = $stat;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Stat $stat)
    {
		$record = $stat;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Stat $stat)
    {
		$record = $stat;

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

    public function confirmDelete(Stat $stat)
    {
		$record = $stat;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Stat $stat)
    {
		$record = $stat;

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

	static public function updateStats(Request $request)
	{
	    $msg = Stat::updateStats($request);

		return $msg;
	}

}
