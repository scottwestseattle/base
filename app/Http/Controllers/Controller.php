<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App;
use Cookie;

define('SITE_ID', 0);

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
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

	public function en(Request $request) {
		return self::handleLocalePrefix($request, 'en');
	}
	public function es(Request $request) {
		return self::handleLocalePrefix($request, 'es');
	}
	public function zh(Request $request) {
		return self::handleLocalePrefix($request, 'zh');
	}

	static private function handleLocalePrefix($request, $locale)
	{
		self::setLocale($locale);
		return redirect(self::getUrl($request, $locale));
	}
	
	static private function setLocale($locale)
	{
		session(['locale' => $locale]);
		App::setLocale($locale);
	}

	static private function getUrl(Request $request, $locale)
	{
		$url = '';
		$skipped = false;
		$segments = $request->segments();
		foreach($segments as $segment)
		{
			if (!$skipped && $segment == $locale)
			{
				// skip the first locale
				$skipped = true;
			}
			else
			{
				$url .= '/' . $segment;
			}
		}
		
		return $url;
	}			
	
	
	public function getViewData($vd)
	{
		return $vd;
	}	
}
