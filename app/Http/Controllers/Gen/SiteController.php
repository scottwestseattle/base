<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use Config;
use Log;

use App\Gen\Site;
use App\Tools;
use App\User;

define('PREFIX', 'gen/sites');
define('VIEWS', 'gen.sites');
define('LOG_CLASS', 'SiteController');

class SiteController extends Controller
{
	use SoftDeletes;
	private $redirectTo = '/' . PREFIX;
	
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
			logException(LOG_CLASS, $e->getMessage(), 'Error selecting list');
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
		$record = new Site();
		
		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);

		try
		{
			$record->save();	
			logInfo(LOG_CLASS, 'New record has been added', ['record_id' => $record->id]);
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error adding new site');			
			return back(); 
		}	
			
		return redirect(self::$redirectTo . '/view/' . $record->id);		
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
			logException(LOG_CLASS, $e->getMessage(), 'Record not found', ['permalink' => $permalink]);			
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
			]);
    }
	
    public function update(Request $request, Site $site)
    {
		$record = $site; 
		 
		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
								
		if ($isDirty)
		{						
			try
			{
				$record->save();
				logInfo(LOG_CLASS, 'Record has been updated', ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e) 
			{
				logException(LOG_CLASS, $e->getMessage(), 'Error updating record', ['record_id' => $record->id]);
			}				
		}
		else
		{
			logInfo(LOG_CLASS, 'No changes made to record', ['record_id' => $record->id]);						
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}
	
    public function confirmdelete(Site $site)
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
			logInfo(LOG_CLASS, 'Record has been deleted', ['record_id' => $record->id]);
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error deleting record', ['record_id' => $record->id]);
			return back();
		}	
			
		return redirect($this->redirectTo);
    }	
	
    public function undelete()
    {	
		$records = []; // make this countable so view will always work
		
		try
		{
			$records = Site::select()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error getting undelete records');
		}	
			
		return view(VIEWS . '.undelete', [
			'records' => $records,
		]);		
    }

    public function publish(Request $request, Site $site)
    {			
		$record = $site; 
	
		return view(VIEWS . '.publish', [
			'record' => $record,
		]);
    }
	
    public function publishupdate(Request $request, Site $site)
    {	
		$record = $site; 
		
		$record->wip_flag = $request->wip_flag;
		$record->release_flag = $request->release_flag;
		
		try
		{
			$record->save();
			logInfo(LOG_CLASS, 'Record status has been updated', ['record_id' => $record->id]);			
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error updating record status', ['record_id' => $record->id]);
			return back();
		}				
		
		return redirect($this->redirectTo);
    }	
	
}
