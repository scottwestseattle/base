<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\Tag;
use App\Status;
use App\Tools;
use App\User;

define('PREFIX', '/tags');
define('VIEWS', 'tags');
define('LOG_CLASS', 'TagController');

class TagController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except([
            //'index', 'view', 'permalink',
            'edit', 'update',
            'confirmDelete', 'delete',

            'addUserFavoriteList',
            'createUserFavoriteList',
            'confirmUserFavoriteListDelete',
            'editUserFavoriteList',
        ]);

        $this->middleware('auth')->only([
            'addUserFavoriteList',
            'createUserFavoriteList',
        ]);

        $this->middleware('owner')->only([
            'edit', 'update',
            'confirmDelete', 'delete',
            'confirmUserFavoriteListDelete',
            'editUserFavoriteList',
        ]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];
        //$releaseFlag = Status::getReleaseFlagForUserLevel();
        //$releaseFlagCondition = Status::getConditionForUserLevel();

		try
		{
			$records = Tag::select()
				->orderByRaw('user_id, type_flag, name')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msg.Error getting record list'));
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
		$record = new Tag();

		$record->user_id 		= Auth::id();
		$record->name 			= trimNull($request->title);
		$record->type_flag 		= $request->type_flag;

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

        $redirect = isAdmin() ? $this->redirectTo . '/view/' . $record->id : '/favorites';

		return redirect($redirect);
    }

    public function permalink(Request $request, $permalink)
    {
		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = Status::getReleaseFlagForUserLevel();
        $releaseFlagCondition = Status::getConditionForUserLevel();

		try
		{
			$record = Tag::select()
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

	public function view(Tag $tag)
    {
		$record = $tag;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Tag $tag)
    {
		$record = $tag;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Tag $tag)
    {
		$record = $tag;

		$isDirty = false;
		$changes = '';

		$record->name      = copyDirty($record->name, $request->name, $isDirty, $changes);

        if (isAdmin())
        {
    		$record->type_flag = copyDirty($record->type_flag, $request->type_flag, $isDirty, $changes);
    		$record->user_id   = copyDirty($record->user_id, $request->user_id, $isDirty, $changes);
        }

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

        $redirect = isAdmin() ? 'tags' : 'favorites';

		return redirect($redirect);
	}

    public function confirmDelete(Tag $tag)
    {
		$record = $tag;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Tag $tag)
    {
		$record = $tag;

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

        $redirect = (isAdmin()) ? $this->redirectTo : '/favorites';

		return redirect($redirect);
    }

    public function undelete(Request $request, $id)
    {
		$id = intval($id);

		try
		{
			$record = Tag::withTrashed()
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
			$records = Tag::withTrashed()
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

    public function publish(Request $request, Tag $tag)
    {
		$record = $tag;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Tag $tag)
    {
		$record = $tag;

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

    public function addUserFavoriteList(Request $request)
    {
    	return view('tags.add-user-favorite-list', [
    	]);
    }

    public function createUserFavoriteList(Request $request)
    {
		try
		{
			$record = new Tag();
			$record->name = alphanum($request->name);
			$record->type_flag = TAG_TYPE_DEF_FAVORITE;
			$record->user_id = Auth::id();
			$record->save();

			logInfo(__FUNCTION__, __('proj.New list has been added'), ['name' => $record->name, 'id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new list';
			logException(__FUNCTION__, $msg, $e->getMessage());
		}

		return redirect('/favorites');
    }

    public function editUserFavoriteList(Request $request, Tag $tag)
    {
		$record = $tag;

		return view('tags.edit', [
			'record' => $record,
			'allowTypeChange' => false,
		]);
	}

    public function confirmUserFavoriteListDelete(Tag $tag)
    {
		$count = DB::table('definition_tag')
			->select()
			->where('tag_id', $tag->id)
			->count();

		return view('tags.confirm-user-favorite-list-delete', [
			'record' => $tag,
			'count' => $count,
		]);
    }

}
