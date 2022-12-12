<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use Auth;
use Cookie;
use Log;

use App\DateTimeEx;
use App\Entry;
use App\Event;
use App\Home;
use App\Gen\Article;
use App\Gen\Definition;
use App\Gen\History;
use App\Gen\Lesson;
use App\Site;
use App\Tag;
use App\User;

define('LOG_CLASS', 'HomeController');

define('SEARCHTYPE_DICTIONARY', 1);
define('SEARCHTYPE_ARTICLES', 2);

class HomeController extends Controller
{
	public function __construct ()
	{
        $this->middleware('admin')->except([
			'frontpage', 'about', 'contact',
			'privacy', 'terms', 'sitemap',
			'search', 'searchAjax',
			'dashboard',
	    ]);

        $this->middleware('auth')->only([
			'dashboard',
	    ]);

		parent::__construct();
	}

	public function frontpage(Request $request)
	{
	    $view = 'home.frontpage';

	    //
	    // Get the site info for the current domain
	    //
	    $siteLanguage = getLanguageId();
        $options = [];
		try
		{
			$record = Site::site();

            if (!isset($record))
                throw new \Exception('Site not found');

            if (blank($record->frontpage))
                throw new \Exception('Site frontpage not set');

            //
            // get the frontpage from the site record
            //
            $viewFile = resource_path() . '/views/home/' . $record->frontpage . '.blade.php';
            if (!file_exists($viewFile))
                throw new \Exception('Site frontpage file not found: ' . $viewFile);

            // don't set the site language by default
            //if (isset($record->language_flag))
            //    $siteLanguage = $record->language_flag;

            //$options['site'] = $record;

            $view = 'home.' . $record->frontpage;

            //dump($record);
		}
		catch (\Exception $e)
		{
    	    $dn = domainName();
			logException(LOG_CLASS, $e->getMessage(), __('base.Error loading site'), ['domain' => $dn]);
		}

        $options['loadReader'] = false; // this loads js and css
        $options['languageCodes'] = getSpeechLanguage($siteLanguage);
        $options['showAllButton'] = true;
        $options['snippetLanguages'] = getLanguageOptions(isAdmin());
        $options['returnUrl'] = '/';
        $options['articlesPublic'] = [];
        $options['articlesPrivate'] = [];

        //
        // get articles, banner and other options according to the language
        //
        if ($siteLanguage == LANGUAGE_ALL)
        {
            $options = self::getOptionsLanguage($options);
        }
        else
        {
            $options = self::getOptions($options, $siteLanguage);
        }

        //
        // get the snippets for the appropriate langauge
        //
    	$languageFlagCondition = '=';
        $snippetsLimit = 5;
        if ($siteLanguage == LANGUAGE_ALL)
        {
    		$languageFlagCondition = '<=';
            $snippetsLimit = 10;
        }

        //
        // get the latest snippets
        //
        $snippets = Definition::getSnippets([
            'count' => $snippetsLimit,
            'languageId' => $siteLanguage,
            'languageFlagCondition' => $languageFlagCondition,
            'userId' => Auth::check() ? Auth::id() : 0,
            'userIdCondition' => Auth::check() ? '=' : '>=',
        ]);
        $options['records'] = $snippets;
        $options['showForm'] = true;

        //
        // get the favorite lists so the entries can be favorited
        //
        $options['favoriteLists'] = Definition::getUserFavoriteLists();

        //
        // set up the reader
        //
        //$options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;
        $options['language'] = $siteLanguage;
        $options['loadReader'] = true; // this loads js and css

        //
        // get the active snippet
        //
        $options['snippet'] = null;
        $snippetId = intval(Cookie::get('snippetId'));
        if (isset($snippetId) && $snippetId > 0)
        {
            $snippet = Definition::getByType(DEFTYPE_SNIPPET, $snippetId, 'id');
            if (isset($snippet) && $snippet->language_flag == $siteLanguage)
                $options['snippet'] = $snippet;
        }

        // not used but needed for reader
        $history = History::getArrayShort(HISTORY_TYPE_SNIPPETS, LESSON_TYPE_READER, 1);

        $options['languageDetails'] = Site::getLanguage();
        $options['autofocus'] = false;

		return view($view, [
		    'options' => $options,
		    'history' => $history,
		]);
	}

	static public function getOptions($options, $languageFlag)
	{
        $options['showWidgets'] = true;
        $showTopBoxes = false;

        // show aotd, wotd, potd if they haven't been shown recently
        if (false && (!Auth::check() || null === Cookie::get('showTopBoxes'))) // TURNED OFF
        {
            $showTopBoxes = true;
            Cookie::queue('showTopBoxes', 1, COOKIE_HOUR * 6); // only show every six hours
        }

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

            //
            // Get WOTD
            //
            if ($showTopBoxes)
            {
                $tag = Tag::get(TAG_NAME_WOTD, TAG_TYPE_DEF_FAVORITE);
                if (isset($tag))
                {
                    // get the last record added to the tag
                    $record = $tag->definitions()->orderBy('definition_tag.created_at', 'desc')->first();
                    if (isset($record))
                    {
                        // only show the first part of the definition
                        $record->definition = getSentences($record->definition, 1);

                        // only show the first sentence in the examples
                        $record->title = getSentences($record->title, 1);

                        $options['wotd'] = $record;
                    }
                }
            }

            //
            // Get POTD
            //
            if ($showTopBoxes)
            {
                $tag = Tag::get(TAG_NAME_POTD, TAG_TYPE_DEF_FAVORITE);
                if (isset($tag))
                {
                    $record = $tag->definitions()->orderBy('definition_tag.created_at', 'desc')->first();
                    if (isset($record))
                    {
                        $options['potd'] = $record->title;
                    }
                }
            }

            //
            // get word lists to show
            //
            $options['randomWords'] = Definition::getRandomWords(5);
            $options['newestWords'] = Definition::getNewest(5);
        }

		//
		// get articles and aotd
		//
		try
		{
		    $parms = Site::getLanguage();
		    $parms['type'] = ENTRY_TYPE_ARTICLE;
            $parms['limit'] = 5;

            // get public articles
            $parms['release'] = 'public';
    		$options['articlesPublic'] = Entry::getRecentList($parms)['records'];

            // get private articles
            $parms['release'] = 'private';
            $options['articlesPrivate'] = Entry::getRecentList($parms)['records'];

            // get other peoples articles
            $parms['release'] = 'other';
            $options['articlesOther'] = isAdmin() ? Entry::getRecentList($parms)['records'] : null;

            // show aotd if it hasn't been shown recently
            if ($showTopBoxes)
            {
                $parms['orderBy'] = Auth::check() ? 'id DESC' : 'id ASC';
                $options['aotd'] = Article::getFirst($parms);
            }
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('proj.Error getting articles'));
		}

        return $options;
	}

	static public function getOptionsLanguage($options)
	{
		//
		// get articles
		//
	    $options['articlesPublic'] = Entry::getArticles(LANGUAGE_ALL, 5);

        return $options;
	}

	public function about(Request $request)
	{
		return view('home.about');
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
		$userNewest = null;
        $site = null;
        $language = Site::getLanguage();

		if (isAdmin())
		{
			$users = User::count();
			$userNewest = User::get(1);
			if (isset($userNewest) && count($userNewest) > 0)
			{
			    $userNewest = $userNewest[0];
			}
            $language = Site::getLanguage();
			$events = Event::get();
			if ($events['emergency'] > 0)
				flash('danger', trans_choice('base.emergency events found', $events['emergency'], ['count' => $events['emergency']]));
		}

		$history = History::get(100);
        $history['maxDays'] = 5;

		return view('home.dashboard', [
		    'events' => $events,
		    'users' => $users,
		    'history' => $history,
		    'userNewest' => $userNewest,
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

    public function search(Request $request)
    {
		$isPost = $request->isMethod('post');

        // turn these on by default
		$options['articles'] = true;
		$options['dictionary'] = true;
		$options['snippets'] = true;
		$options['word'] = false;

		$results = [];

		if ($isPost)
		{
			// do the search
			$options['word'] = isset($request->word_flag) ? true : false;
			$options['articles'] = isset($request->articles_flag) ? true : false;
			$options['dictionary'] = isset($request->dictionary_flag) ? true : false;
			$options['snippets'] = isset($request->snippets_flag) ? true : false;

            $results = self::searchAll($request->searchText, $options);
		}

		return view('home.search', [
			'isPost' => $isPost,
		    'options' => $options,
		    'results' => $results,
		]);
	}


	//
	// This handles the search form from the index/search page
	//
    public function searchAjaxORIG(Request $request, $text = null)
    {
		$text = getOrSetString(alpha($text), null);
        $records = [];

		try
		{
		    if ($resultsFormat != 'heavy' && $resultsFormat != 'light')
		        throw new \Exception('bad searchAjax results format parm');

			session(['definitionSearch' => $text]);
			$records = Definition::searchPartial($text);
		}
		catch (\Exception $e)
		{
			$msg = 'Error finding text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), null, ['text' => $text]);
		}

		return view(VIEWS . '.component-search-results-light', [
			'records' => $records,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
	}

    public function searchAjax(Request $request, $searchText, $searchType = SEARCHTYPE_DICTIONARY)
    {
		$isPost = $request->isMethod('post');

        // turn these on by default
		$options['dictionary'] = ($searchType == SEARCHTYPE_DICTIONARY);
		$options['snippets'] = ($searchType == SEARCHTYPE_DICTIONARY);
		$options['articles'] = ($searchType == SEARCHTYPE_ARTICLES);
		$options['word'] = false;
		$options['lessons'] = false;

		$results = [];

		if ($isPost)
		{
			// do the search
			$options['word'] = isset($request->word_flag) ? true : false;
			$options['articles'] = isset($request->articles_flag) ? true : false;
			$options['dictionary'] = isset($request->dictionary_flag) ? true : false;
			$options['snippets'] = isset($request->snippets_flag) ? true : false;
		}

        $searchText = alphanum($searchText);
        $options['startsWith'] = (strlen($searchText) <= 3);

        $results = self::searchAll($searchText, $options);

		return view('shared.search-results-light', [
			'isPost' => $isPost,
		    'options' => $options,
		    'results' => $results,
		]);
	}

	static private function searchAll($searchText, $options)
	{
		$results['definitions'] = null;
		$results['snippets'] = null;
		$results['entries'] = null;
		$results['search'] = null;

        $search = alphanum($searchText);
		$count = 0;

        try
        {
            if ($search != $searchText)
            {
                throw new \Exception("dangerous search characters");
            }

            if ($options['articles'])
            {
                $results['entries'] = Article::search($search, $options);
                $count += (isset($results['entries']) ? count($results['entries']) : 0);
            }

            if ($options['dictionary'])
            {
                $results['definitions'] = Definition::searchDictionary($search, $options);
                $count += (isset($results['definitions']) ? count($results['definitions']) : 0);
            }

            if ($options['snippets'])
            {
                $results['snippets'] = Definition::searchSnippets($search, $options);
                $count += (isset($results['snippets']) ? count($results['snippets']) : 0);
            }

            $results['search'] = $search;
        }
        catch (\Exception $e)
        {
            $msg = 'Search Internal Error';
            $exc = $e->getMessage();
            logException('global search', $exc, $msg, ['searchCleaned' => $search]);
        }

        $results['count'] = $count;

        return $results;
    }

    public function sitemap(Request $request)
    {
		return view('home.sitemap', [
		]);
	}

    public function test(Request $request)
    {
		return view('home.test', [
		]);
	}
}
