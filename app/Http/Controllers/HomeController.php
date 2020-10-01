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
}
