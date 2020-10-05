<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Exception;
use Log;

use App\Event;

class EventController extends Controller
{
	public function __construct ()
	{
        $this->middleware('auth');
			
		parent::__construct();
	}	

	public function index(Request $request, $filter = null)
	{
		$filter = alpha($filter);
		
		$rc = Event::get($filter);
		
		//logEmergency('test');
		//logError('error');
		//throw new Exception('test exception');
		if ($rc['emergency'] > 0)
			flash('danger', trans_choice('base.emergency events found', $rc['emergency'], ['count' => $rc['emergency']]));
		
		return view('events.index', ['records' => $rc['records']]);
	}
	
	public function confirmdelete(Request $request)
	{
		$rc = Event::get();
		
		//logEmergency('system down');
		return view('events.confirmdelete', ['records' => $rc['records'], 'hasEmergencyEvents' => $rc['emergency']]);
	}
	
	public function delete(Request $request, $filter = null)
	{
		$rc = Event::deleteEvents($filter);
		
		return redirect('/events');

	}
	
}
