<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

use App;
use App\Entry;
use App\Gen\History;
use App\Gen\Spanish;
use App\Site;
use Auth;
use Config;
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

    	$debug = session('debug');
    	if (isset($debug))
    	{
            Config::set('app.debug', $debug);
    	}
        //$debug = Config::get('app.debug');
	    //dump($debug);

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

    // generic ajax function to set a Session tag/value pair
	public function setSession(Request $request)
	{
	    $rc = RETURN_CODE_ERROR;
	    $tag = isset($request['tag']) ? alpha($request['tag']) : null;
	    $value = isset($request['value']) ? intval($request['value']) : RETURN_CODE_ERROR;

        // save session info
        if (strlen($tag) > 0 && $value != RETURN_CODE_ERROR)
        {
            session([$tag => $value]);
            $rc = $value;
        }

        return $rc;
    }

	static private function setLocale($locale)
	{
		session(['locale' => $locale]);
		App::setLocale($locale);
	}

	static function setLanguage($languageId)
	{
        Cookie::queue('languageId', intval($languageId), COOKIE_WEEK);
    }

	static function setUserLevel($userLevel)
	{
        Cookie::queue('userLevel', intval($userLevel), COOKIE_WEEK);
    }

    static public function getLines(Entry $record)
    {
        $noTranslation = __('proj.(no translation)');

        if ($record->hasTranslation())
        {
            $lines['text'] = Spanish::getSentences($record->title);
            $lines['translation'] = [$record->title]; // not translated

            if (strlen($record->description_short) > 0)
            {
                $short = Spanish::getSentences($record->description_short);
                $lines['text'] = array_merge($lines['text'], $short);
                $lines['translation'] = array_merge($lines['translation'], $short); // not translated
            }

            $lines['text'] = array_merge($lines['text'], Spanish::getSentences($record->description));
            $lines['translation'] = array_merge($lines['translation'], Spanish::getSentences($record->description_translation));
        }
        else
        {
            // the original way to gather sentences
            $lines['text'] = Spanish::getSentences($record->title);
            $lines['text'] = array_merge($lines['text'], Spanish::getSentences($record->description_short));
            $lines['text'] = array_merge($lines['text'], Spanish::getSentences($record->description));
        }

		return $lines;
    }

    static public function reader(Entry $entry, $options)
    {
        $record = $entry;
		$readLocation = $record->tagRecent(); // tag it as recent for the user so it will move to the top of the list
		Entry::countView($record);

        $lines = self::getLines($record);

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];

        $history = History::getArray($record->title, $record->id, $record->getHistoryType(), $options['source'], LESSON_TYPE_READER, count($lines), $options);

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $record->title,
			'recordId' => $record->id,
			'options' => $options,
			'readLocation' => Auth::check() ? $readLocation : null,
			'contentType' => 'Entry',
			'languageCodes' => getSpeechLanguage($record->language_flag),
			'labels' => $labels,
			'history' => $history,
		]);
    }
}
