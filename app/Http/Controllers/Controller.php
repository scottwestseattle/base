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
use Cookie;
use Lang;

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

	static function setLanguage($languageId)
	{
        Cookie::queue('languageId', intval($languageId), MS_YEAR);
    }

    static public function reader(Entry $entry, $options)
    {
        $record = $entry;
		$readLocation = $record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
		Entry::countView($record);

		$lines = [];

		$lines = Spanish::getSentences($record->title);
		$lines = array_merge($lines, Spanish::getSentences($record->description_short));
		$lines = array_merge($lines, Spanish::getSentences($record->description));

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];
        //dump($labels);

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $record->title,
			'recordId' => $record->id,
			'options' => $options,
			'readLocation' => Auth::check() ? $readLocation : null,
			'contentType' => 'Entry',
			'languageCodes' => getSpeechLanguage($record->language_flag),
			'labels' => $labels,
		]);
    }
}
