<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
//use Illuminate\Support\Str;

use App;
use Cookie;
use Log;

use App\Entry;
use App\Event;
use App\Home;
use App\Site;
use App\User;
use App\Word;

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
	    $dn = domainName();
	    $language = LANGUAGE_ALL;
        $options = [];
		try
		{
			$record = Site::select()
				->where('title', $dn)
				->first();

            if (!isset($record))
                throw new \Exception('Site not found');

            if (blank($record->frontpage))
                throw new \Exception('Site frontpage not set');

            $viewFile = resource_path() . '/views/home/' . $record->frontpage . '.blade.php';
            if (!file_exists($viewFile))
                throw new \Exception('Site frontpage file not found: ' . $viewFile);

            if (isset($record->language_flag))
                $language = $record->language_flag;

            $options['title'] = $record->description;

            $view = 'home.' . $record->frontpage;
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error loading site'), ['domain' => $dn]);
		}

        $options['loadSpeechModules'] = false; // this loads js and css
        $options['languageCodes'] = getSpeechLanguage($language);
        $options['showAllButton'] = true;
        $options['snippetLanguages'] = getLanguageOptions();
        $options['returnUrl'] = '/';

        if ($language == LANGUAGE_ALL)
            $options = self::getOptionsLanguage($options);
        else
            $options = self::getOptions($options, $language);

        // get the snippets for the appropriate langauge
    	$languageFlagCondition = '=';
        $snippetsLimit = 5;
        if ($language == LANGUAGE_ALL)
        {
    		$languageFlagCondition = '>=';
            $snippetsLimit = 10;
        }

        $snippets = Word::getSnippets([
            'limit' => $snippetsLimit,
            'languageId' => $language,
            'languageFlagCondition' => $languageFlagCondition
        ]);
        $options['records'] = $snippets;
        $options['snippet'] = null;

        $snippetId = intval(Cookie::get('snippetId'));
        if (isset($snippetId) && $snippetId > 0)
        {
            $options['snippet'] = Word::get(WORDTYPE_SNIPPET, $snippetId, 'id');
            //dd($options) ;
        }

		return view($view, [
		    'options' => $options,
		]);
	}

	static public function getOptions($options, $languageFlag)
	{
        $options['loadSpeechModules'] = true; // this loads js and css

        if ($languageFlag == LANGUAGE_ES)
        {
            //
            // get banner
            //
            $files = preg_grep('/^([^.])/', scandir(base_path() . '/public/img/spanish/banners')); // grep removes the hidden files
            $fileCount = count($files);
            $lastIx = $fileCount;
            if (isset($bannerIx))
            {
                $ix = ($bannerIx <= $fileCount && $bannerIx > 0) ? $bannerIx : $lastIx;
            }
            else
            {
                $ix = rand(1, $fileCount);
            }

            $options['banner'] = 'es-banner' . $ix . '.png';
        }

		//
		// get articles
		//
	    $options['articles'] = Entry::getArticles($languageFlag, 5);

        return $options;
	}

	static public function getOptionsLanguage($options)
	{
        $options['loadSpeechModules'] = true; // this loads js and css

		//
		// get articles
		//
	    $options['articles'] = Entry::getArticles(LANGUAGE_ALL, 5);

        return $options;
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
