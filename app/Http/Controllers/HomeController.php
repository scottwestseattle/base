<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Str;

use App;
use Log;

use App\Entry;
use App\Event;
use App\Home;
use App\Site;
use App\User;

define('LOG_CLASS', 'HomeController');

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
	    $view = 'home.frontpage';

	    //
	    // Get the site info for the current domain
	    //
	    $d = domainName();
		try
		{
			$record = Site::select()
				->where('title', $d)
				->first();

            if (!isset($record))
                throw new \Exception('Site not found');

            if (blank($record->frontpage))
                throw new \Exception('Site frontpage not set');

            $view = 'home.' . $record->frontpage;
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Record not found'), ['domain' => $d]);
		}

		//
		// get articles
		//
	    $articles = Entry::getArticles(5);

		return view($view, [
		    'articles' => $articles,
		]);
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
