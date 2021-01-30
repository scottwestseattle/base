<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use DB;
use Lang;

use App\Event;
use App\Tag;
use App\Tools;

define('PREFIX', 'tags');
define('LOG_MODEL', 'Tag');
define('TITLE', 'Tag');
define('TITLE_LC', 'tag');
define('TITLE_PLURAL', 'tags');
define('REDIRECT', '/tags');
define('REDIRECT_ADMIN', '/tags');

class TagController extends Controller
{
	public function __construct ()
	{
        $this->middleware('is_admin')->except([
			'addUserFavoriteList', 'createUserFavoriteList',
			'confirmUserFavoriteListDelete', 'delete',
			'editUserFavoriteList', 'update',
		]);

		// all owner except for adds
		$this->middleware('is_owner')->except('addUserFavoriteList', 'createUserFavoriteList');
		
		// no public pages, only owner or logged in user pages
		$this->middleware('auth');

		$this->prefix = PREFIX;
		$this->title = TITLE;
		$this->titlePlural = TITLE_PLURAL;

		parent::__construct();
	}
	
	// for admin only
    public function index()
    {		
		$tags = Tag::select()
			->orderBy('name')
			->get();
		
    	return view('tags.index', $this->getViewData([
				'records' => $tags,
			]));
    }

    public function entries(Tag $tag)
    {		
    	return view('entries.index', ['entries' => $tag->entries]);
    }
	
    public function add(Request $request)
    {		
    	return view('tags.add', $this->getViewData());
    }

    public function create(Request $request)
    {
		try
		{
			$record = new Tag();
			$record->name = Tools::alphanum($request->name);
			$record->type_flag = $request->type_flag;
			$record->user_id = Auth::id();
			$record->save();

			Event::logAdd(LOG_MODEL, $record->name, '', $record->id);
			Tools::flash('success', 'New list has been added');
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new ' . TITLE_LC;
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, $record->mag, null, $msg . ': ' . $e->getMessage());
			Tools::flash('danger', $msg);

			return back();
		}
				
		return redirect($this->getReferrer($request, '/vocabulary'));
    }
	
    public function addUserFavoriteList(Request $request)
    {		
    	return view('tags.add-user-favorite-list', $this->getViewData());
    }

    public function createUserFavoriteList(Request $request)
    {
		try
		{
			$record = new Tag();
			$record->name = Tools::alphanum($request->name);
			$record->type_flag = TAG_TYPE_DEFINITION_FAVORITE;
			$record->user_id = Auth::id();
			$record->save();

			Event::logAdd(LOG_MODEL, $record->name, '', $record->id);
			Tools::flash('success', 'New list has been added');
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new ' . TITLE_LC;
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, $record->mag, null, $msg . ': ' . $e->getMessage());
			Tools::flash('danger', $msg);
		}
				
		return redirect($this->getReferrer($request, '/vocabulary')); 				
    }

    public function editUserFavoriteList(Request $request, Tag $tag)
    {
		$record = $tag;
			
		return view('tags.edit', $this->getViewData([
			'record' => $record,
			'allowTypeChange' => false,
		]));
	}
	
    public function edit(Tag $tag)
    {
		$record = $tag;
			
		return view('tags.edit', $this->getViewData([
			'record' => $record,
			'allowTypeChange' => true,
		]));
    }
	
    public function update(Request $request, Tag $tag)
    {			 
		$tag->name = $request->name;
		$tag->type_flag = $request->type_flag;
		$tag->save();
		
		return redirect($this->getReferrer($request, '/vocabulary')); 
    }

    public function confirmUserFavoriteListDelete(Tag $tag)
    {	
		$count = DB::table('definition_tag')
			->select()
			->where('tag_id', $tag->id)
			->count();

		return view('tags.confirm-user-favorite-list-delete', $this->getViewData([
			'record' => $tag,
			'count' => $count,
		]));
    }

    public function confirmdelete(Tag $tag)
    {	
		$countDefinitions = DB::table('definition_tag')
			->select()
			->where('tag_id', $tag->id)
			->count();

		$countEntries = DB::table('entry_tag')
			->select()
			->where('tag_id', $tag->id)
			->count();
	
		return view('tags.confirmdelete', $this->getViewData([
			'record' => $tag,
			'countDefinitions' => $countDefinitions,
			'countEntries' => $countEntries,
			'cantDelete' => ($countDefinitions > 0 || $countEntries > 0),
		]));
    }
	
    public function delete(Request $request, Tag $tag)
    {
		//dd($tag->definitionsUser);
		foreach($tag->definitionsUser as $record)
		{
			$tag->definitionsUser()->detach($record->id);
		}
		
		$tag->delete();
		
		return redirect($this->getReferrer($request, '/vocabulary')); 
    }	
}

