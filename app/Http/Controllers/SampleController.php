<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use Log;

use App\Sample;
use App\Tools;
use App\User;

define('PREFIX', 'samples');
define('LOG_MODEL', 'samples');
define('TITLE', 'Sample');
define('TITLE_LC', 'sample');
define('TITLE_PLURAL', 'Samples');
define('REDIRECT', '/samples');
define('REDIRECT_ADMIN', '/samples/admin');

class SampleController extends Controller
{
	use SoftDeletes;
	
	public function __construct ()
	{
        $this->middleware('admin')->except(['index', 'view', 'permalink']);
		
		$this->prefix = PREFIX;
		$this->title = TITLE;
		$this->titlePlural = TITLE_PLURAL;
		
		parent::__construct();
	}
	
    public function index(Request $request)
    {	
		$records = [];
		
		try
		{
			$siteId = SITE_ID;
			$release_flag = RELEASE_PUBLIC;
			
			if (Tools::isAdmin())
			{
			}

			$records = Sample::select()
				->where('site_id', '', SITE_ID)
				->where('published_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			$msg = 'Error selecting list';
			Log::error($msg, ['id' => Auth::id()]);
			//Event::logException(LOG_MODEL, LOG_ACTION_SELECT, $msg, null, $e->getMessage());
			flash('danger', $msg);
		}	
			
		return view(PREFIX . '.index', $this->getViewData([
			'records' => $records,
		]));
    }	

    public function admin(Request $request)
    {
		$records = []; // make this countable so view will always work
		
		try
		{
			$records = Sample::select()
//				->where('site_id', SITE_ID)
//				->where('published_flag', 1)
//				->where('approved_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			$msg = 'Error getting ' . TITLE_LC . ' list';
			Event::logException(LOG_MODEL, LOG_ACTION_SELECT, $msg, null, $e->getMessage());
			Tools::flash('danger', $msg);
		}	
			
		return view(PREFIX . '.admin', $this->getViewData([
			'records' => $records,
		]));
    }	
	
    public function add()
    {		 
		return view(PREFIX . '.add', $this->getViewData([
			]));
	}
		
    public function create(Request $request)
    {					
		$record = new Sample();
		
		$record->user_id 		= Auth::id();				
		$record->title 			= $request->title;
		$record->description	= $request->description;
		$record->permalink		= Tools::createPermalink($request->title);

		try
		{
			$record->save();
			
			Event::logAdd(LOG_MODEL, $record->title, $record->site_url, $record->id);			
			Tools::flash('success', 'New ' . TITLE_LC . ' has been added');
		}
		catch (\Exception $e) 
		{
			$msg = 'Error adding new ' . TITLE_LC;
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, $record->title, null, $msg . ': ' . $e->getMessage());
			Tools::flash('danger', $msg);

			return back(); 
		}	
			
		return redirect(REDIRECT_ADMIN);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);
		
		$record = null;
			
		try
		{
			$record = Sample::select()
				->where('site_id', SITE_ID)
				->where('published_flag', 1)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e) 
		{
			$msg = 'Entry Not Found: ' . $permalink;
			
			Tools::flash('danger', $msg);			
			Event::logError(LOG_MODEL, LOG_ACTION_PERMALINK, /* title = */ $msg);
			
			return back();					
		}	

		return view(PREFIX . '.view', $this->getViewData([
			'record' => $record, 
			], LOG_MODEL, LOG_PAGE_PERMALINK));
	}
	
	public function view(Sample $sample)
    {		
		$record = $sample;
		
		return view(PREFIX . '.view', $this->getViewData([
			'record' => $record,
			], LOG_MODEL, LOG_PAGE_VIEW));
    }
	
	public function edit(Sample $sample)
    {		 
		$record = $sample;
		
		return view(PREFIX . '.edit', $this->getViewData([
			'record' => $record,
			]));
    }
	
    public function update(Request $request, Sample $sample)
    {
		$record = $sample; 
		 
		$isDirty = false;
		$changes = '';

		$record->title = Tools::copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = Tools::copyDirty($record->description, $request->description, $isDirty, $changes);
								
		if ($isDirty)
		{						
			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL, $record->title, $record->id, $changes);			
				Tools::flash('success', TITLE . ' has been updated');				
			}
			catch (\Exception $e) 
			{
				$msg = 'Error updating ' . TITLE_LC;
				Event::logException(LOG_MODEL, LOG_ACTION_EDIT, $record->title, null, $msg . ': ' . $e->getMessage());
				Tools::flash('danger', $msg);
			}				
		}
		else
		{
			Tools::flash('success', 'No changes made to ' . TITLE_LC);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}
	
    public function confirmdelete(Sample $sample)
    {	
		$record = $sample; 
	
		$vdata = $this->getViewData([
			'record' => $record,
		]);				
		 
		return view(PREFIX . '.confirmdelete', $vdata);
    }
	
    public function delete(Request $request, Sample $sample)
    {	
		$record = $sample; 
				
		try 
		{
			$record->deleteSafe();
			Event::logDelete(LOG_MODEL, $record->title, $record->id);					
			Tools::flash('success', $this->title . ' has been deleted');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, $record->title, $record->id, $e->getMessage());
			Tools::flash('danger', $e->getMessage());			
		}	
			
		return redirect(REDIRECT_ADMIN);
    }	
	
    public function undelete()
    {	
		$records = []; // make this countable so view will always work
		
		try
		{
			$records = Sample::select()
//				->where('site_id', SITE_ID)
				->where('deleted_flag', 1)
				->get();
		}
		catch (\Exception $e) 
		{
			$msg = 'Error getting ' . TITLE_LC . ' list';
			Event::logException(LOG_MODEL, LOG_ACTION_UNDELETE, $msg, null, $e->getMessage());
			Tools::flash('danger', $msg);
		}	
			
		return view(PREFIX . '.undelete', $this->getViewData([
			'records' => $records,
		]));		
    }

    public function publish(Request $request, Sample $sample)
    {			
		$record = $sample; 
	
		return view(PREFIX . '.publish', $this->getViewData([
			'record' => $record,
		]));
    }
	
    public function publishupdate(Request $request, Sample $sample)
    {	
		$record = $sample; 
		
		$record->published_flag = isset($request->published_flag) ? 1 : 0;
		$record->approved_flag = isset($request->approved_flag) ? 1 : 0;
		$record->finished_flag = isset($request->finished_flag) ? 1 : 0;		
		
		try
		{
			$record->save();
			Event::logEdit(LOG_MODEL, $record->title, $record->id, 'published/approved/finished status updated');			
			Tools::flash('success', $this->title . ' status has been updated');
		}
		catch (\Exception $e) 
		{
			Event::logException(LOG_MODEL, LOG_ACTION_ADD, $record->title, null, $e->getMessage());
			Tools::flash('danger', $e->getMessage());
		}				
		
		return redirect(REDIRECT_ADMIN);
    }	
}
