<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
		
		if ($rc['emergency'])
			flash('danger', 'Emergency log entries found');
		
		return view('events.index', ['records' => $rc['records']]);
	}
	
	public function confirmdelete(Request $request)
	{
		$rc = Event::get();
		
		return view('events.confirmdelete', ['records' => $rc['records'], 'hasEmergencyEvents' => $rc['emergency']]);
	}
	
	public function delete(Request $request, $filter = null)
	{
		$rc = Event::deleteEvents($filter);
		
		return redirect('/events');

	}
	
}
