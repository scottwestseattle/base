<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;

use App\Event;
use App\Home;

class HomeController extends Controller
{
	public function __construct ()
	{
        $this->middleware('auth')->except([
			'frontpage',
		]);
			
		parent::__construct();
	}	

	public function frontpage(Request $request)
	{
		return view('home.frontpage');
	}
	
	public function dashboard(Request $request)
	{
		if (isAdmin())
		{
			if (Event::hasEmergency())
				flash('danger', 'Emergency <a href="/events">events</a> found');
		}
		
		return view('home.dashboard');
	}
	
	public function events(Request $request, $filter = null)
	{
		$filter = alpha($filter);
		
		$rc = Home::getEvents($filter);
		
		if ($rc['emergency'])
			flash('danger', 'Emergency log entries found');
		
		return view('home.events', ['records' => $rc['records']]);
	}
	
	public function confirmdelete(Request $request)
	{
		$filter = alpha($filter);
		
		$rc = Home::getEvents($filter);
		
		if ($rc['emergency'])
			flash('danger', 'Emergency log entries found');
		
		return view('home.confirmdelete', ['records' => $rc['records']]);
	}
}
