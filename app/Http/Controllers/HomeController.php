<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
