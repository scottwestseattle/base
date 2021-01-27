<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App;
use App\Site;

define('SITE_ID', 0);

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	private $_site = null;

	public function getSiteLanguage()
	{
	    $id = $this->site()->language_flag;

        $language = getSpeechLanguage($id);

		return $language;
	}

	public function site()
	{
		if (!isset($this->_site))
		{
			$this->_site = Site::get();

			if (!isset($this->_site))
			{
			    // make a dummy site, only happens if site record hasn't been added yet
			    $this->_site = new Site();
			    $this->_site->title = 'not set';
			    $this->_site->description = 'not set - add site record';
			    $this->_site->language_flag = LANGUAGE_ALL;
			}
		}
        //dump('site: ' . $this->_site);

		return $this->_site;
	}

	public function __construct ()
	{
		// session don't work in constructors (or in middleware), so this is the work arround:
		$this->middleware(function ($request, $next){
			// set locale according to selected language
			$locale = session('locale');
			if (isset($locale))
				App::setLocale($locale);

			return $next($request);
		});	}

    // this handles switching languages from dropdown
	public function language($locale)
	{
		//dump($locale);
		if (ctype_alpha($locale))
		{
			session(['locale' => $locale]);
			App::setLocale($locale);
		}

		return back();
	}

	public function routeLocale(Request $request)
	{
	    $locale = $request->segments()[0];

		self::setLocale($locale);

	    // trim off the locale: "es/about" to "/about"
		return redirect(substr($request->path(), 2));
	}

	static private function setLocale($locale)
	{
		session(['locale' => $locale]);
		App::setLocale($locale);
	}
}
