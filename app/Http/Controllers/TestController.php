<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use Config;
use Log;

use App\Test;
use App\Tools;
use App\User;

define('PREFIX', 'tests');
define('LOG_CLASS', 'TestController');

class TestController extends Controller
{
	use SoftDeletes;
	private $redirectTo = '/' . PREFIX;
	
	public function __construct ()
	{
        $this->middleware('admin')->except(['index', 'view', 'permalink']);
		
		$this->prefix = PREFIX;
	
		parent::__construct();
	}
	
    public function index(Request $request)
    {	
		$records = [];
		
		try
		{
			//$siteId = SITE_ID;
			$release_flag = Config::get('constants.release_flag.public');

			$records = Test::select()
				//->where('site_id', '', SITE_ID)
				->where('published_flag', '>=', $release_flag)
				->get();
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error selecting list');
		}	
			
		return view(PREFIX . '.index', [
			'records' => $records,
			'prefix' => $this->prefix,
		]);
    }	

    public function admin(Request $request)
    {
		$records = []; // make this countable so view will always work
		
		try
		{
			$records = Test::select()
//				->where('site_id', SITE_ID)
//				->where('published_flag', '>=', Config::get('constants.release_flag.public'))
//				->where('approved_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error getting admin tests list');			
		}	
			
		return view(PREFIX . '.admin', [
			'records' => $records,
		]);
    }	
	
    public function add()
    {		 
		return view(PREFIX . '.add', [
			]);
	}
		
    public function create(Request $request)
    {					
		$record = new Test();
		
		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);
		$record->permalink		= createPermalink($request->title);

		try
		{
			$record->save();	
			logInfo(LOG_CLASS, 'New record has been added', ['record_id' => $record->id]);
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error adding new test');			
			return back(); 
		}	
			
		return redirect($this->redirectTo);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);
		
		$record = null;
			
		try
		{
			$record = Test::select()
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

		return view(PREFIX . '.view', [
			'record' => $record, 
			]);
	}
	
	public function view(Test $test)
    {		
		$record = $test;
		
		return view(PREFIX . '.view', [
			'record' => $record,
			]);
    }
	
	public function edit(Test $test)
    {		 
		$record = $test;
		
		return view(PREFIX . '.edit', [
			'record' => $record,
			]);
    }
	
    public function update(Request $request, Test $test)
    {
		$record = $test; 
		 
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
	
    public function confirmdelete(Test $test)
    {	
		$record = $test; 
			 
		return view(PREFIX . '.confirmdelete', [
			'record' => $record,		
		]);
    }
	
    public function delete(Request $request, Test $test)
    {	
		$record = $test; 
				
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
			$records = Test::select()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e) 
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error getting undelete records');
		}	
			
		return view(PREFIX . '.undelete', [
			'records' => $records,
		]);		
    }

    public function publish(Request $request, Test $test)
    {			
		$record = $test; 
	
		return view(PREFIX . '.publish', [
			'record' => $record,
		]);
    }
	
    public function publishupdate(Request $request, Test $test)
    {	
		$record = $test; 
		
		$record->published_flag = isset($request->published_flag) ? 1 : 0;
		$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
		$record->finished_flag = isset($request->finished_flag) ? 1 : 0;		
		
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
