<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Log;

use App\Event;
use App\Home;
use App\User;

class HomeController extends Controller
{
	public function __construct ()
	{
        $this->middleware('auth')->except([
			'frontpage',
			'about',
			'sitemap',
			'mvc',
			'privacy',
			'terms',
			'contact',
		]);

		parent::__construct();
	}

	public function frontpage(Request $request)
	{
		return view('home.frontpage');
	}

	public function about(Request $request)
	{
		return view('home.about');
	}

	public function sitemap(Request $request)
	{
		return view('home.sitemap');
	}

	public function privacy(Request $request)
	{
		return view('home.privacy');
	}

	public function terms(Request $request)
	{
		return view('home.terms');
	}

	public function contact(Request $request)
	{
		return view('home.contact');
	}

	public function dashboard(Request $request)
	{
		$events = null;
		$users = null;

		if (isAdmin())
		{
			$users = User::count();

			$events = Event::get();
			if ($events['emergency'] > 0)
				flash('danger', trans_choice('base.emergency events found', $events['emergency'], ['count' => $events['emergency']]));
		}

		return view('home.dashboard', ['events' => $events, 'users' => $users]);
	}

}
