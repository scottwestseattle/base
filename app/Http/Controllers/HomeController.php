<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
		return view('home.dashboard');
	}
	
	public function events(Request $request)
	{
		$records = Home::getEvents();
		
		return view('home.events', ['records' => $records]);
	}
}
