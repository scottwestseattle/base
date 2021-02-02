<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use Cookie;
use Log;

use App\Entry;
use App\Event;
use App\Home;
use App\Site;
use App\Tag;
use App\User;

use App\Gen\Definition;

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
	    $siteLanguage = LANGUAGE_ALL;
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
                $siteLanguage = $record->language_flag;

            $options['title'] = $record->description;

            $view = 'home.' . $record->frontpage;
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error loading site'), ['domain' => $dn]);
		}

        $options['loadSpeechModules'] = false; // this loads js and css
        $options['languageCodes'] = getSpeechLanguage($siteLanguage);
        $options['showAllButton'] = true;
        $options['snippetLanguages'] = getLanguageOptions();
        $options['returnUrl'] = '/';

        if ($siteLanguage == LANGUAGE_ALL)
            $options = self::getOptionsLanguage($options);
        else
            $options = self::getOptions($options, $siteLanguage);

        //
        // get the snippets for the appropriate langauge
        //
    	$languageFlagCondition = '=';
        $snippetsLimit = 5;
        if ($siteLanguage == LANGUAGE_ALL)
        {
    		$languageFlagCondition = '>=';
            $snippetsLimit = 10;
        }

        $snippets = Definition::getSnippets([
            'limit' => $snippetsLimit,
            'languageId' => $siteLanguage,
            'languageFlagCondition' => $languageFlagCondition
        ]);
        $options['records'] = $snippets;

        $snippetId = intval(Cookie::get('snippetId'));
        if (isset($snippetId) && $snippetId > 0)
        {
            $options['snippet'] = Definition::getByType(DEFTYPE_SNIPPET, $snippetId, 'id');
        }

        $options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;

        //
        // Get WOTD
        //
        $tag = Tag::get(TAG_NAME_WOTD, TAG_TYPE_DEF_FAVORITE);
        if (isset($tag))
        {
            $record = $tag->definitions()->orderBy('updated_at', 'desc')->first();
            if (isset($record))
            {
                // only show the first sentence in the examples
                if (isset($record->examples))
                {
                    $s = splitSentences($record->examples);
                    if (isset($s) && count($s) > 0)
                        $record->examples = $s[0];
                }

                $options['wotd'] = $record;
            }
        }

        //
        // Get POTD
        //
        $tag = Tag::get(TAG_NAME_POTD, TAG_TYPE_DEF_FAVORITE);
        if (isset($tag))
        {
            $record = $tag->definitions()->orderBy('updated_at', 'desc')->first();
            if (isset($record))
            {
                $options['potd'] = $record->examples;
            }
        }

        $options['loadSpeechModules'] = true; // this loads js and css

		return view($view, [
		    'options' => $options,
		]);
	}

	static public function getOptions($options, $languageFlag)
	{
        $options['showWidgets'] = true;

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
        $site = null;

		if (isAdmin())
		{
			$users = User::count();
            $language = $this->getSiteLanguage();
			$events = Event::get();
			if ($events['emergency'] > 0)
				flash('danger', trans_choice('base.emergency events found', $events['emergency'], ['count' => $events['emergency']]));
		}

		return view('home.dashboard', [
		    'events' => $events,
		    'users' => $users,
		    'language' => $language,
		]);
	}

	public function hash(Request $request)
	{
	    $hash = null;
	    $hashed = null;

	    if (isset($request->hash))
	    {
            $hash = trim($request->get('hash'));
            $hashed = self::getHash($hash);

            if (Str::startsWith($hash, 'Fi') || Str::startsWith($hash, 'Go') || Str::startsWith($hash, 'Ya'))
                $hashed .= '!';
            else
                $hashed .= '#';
	    }


		return view('home.hash', [
			'hash' => $hash,
			'hashed' => $hashed,
		]);
	}

    static private function getHash($text)
	{
		$s = sha1(trim($text));
		$s = str_ireplace('-', '', $s);
		$s = strtolower($s);
		$s = substr($s, 0, 8);
		$final = '';

		for ($i = 0; $i < 6; $i++)
		{
			$c = substr($s, $i, 1);

			if ($i % 2 != 0)
			{
				if (ctype_digit($c))
				{
                    if ($i == 1)
                    {
                        $final .= "Q";
                    }
                    else if ($i == 3)
                    {
                        $final .= "Z";
                    }
                    else
                    {
                        $final .= $c;
                    }
				}
				else
				{
					$final .= strtoupper($c);
				}
			}
			else
			{
				$final .= $c;
			}
		}

		// add last 2 chars
		$final .= substr($s, 6, 2);

		//echo $final;

		return $final;
	}

}
