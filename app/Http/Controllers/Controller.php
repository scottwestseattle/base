<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App;
use App\Entry;
use App\Gen\Spanish;
use App\Site;
use Auth;

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

    static public function reader(Entry $entry, $options)
    {
        $record = $entry;
		$readLocation = $entry->tagRecent(); // tag it as recent for the user so it will move to the top of the list
		Entry::countView($entry);

		$record = $entry;
		$lines = [];

		$lines = Spanish::getSentences($record->title);
		$lines = array_merge($lines, Spanish::getSentences($record->description_short));
		$lines = array_merge($lines, Spanish::getSentences($record->description));
		//dd($lines);

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $record->title,
			'recordId' => $record->id,
			'options' => $options,
			'readLocation' => Auth::check() ? $readLocation : null,
			'contentType' => 'Entry',
			'languageCodes' => getSpeechLanguage($record->language_flag),
		]);
    }
}
