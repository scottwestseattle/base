<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Cookie;
use Log;

use App\Gen\Definition;
use App\Tag;
use App\User;

define('PREFIX', 'gen.definitions');
define('VIEWS', 'gen.definitions');
define('LOG_CLASS', 'DefinitionController');

class DefinitionController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct()
	{
        $this->middleware('admin')->except([
            'index', 'view', 'permalink',
            'snippets', 'createSnippet',

            // copied
			'find', 'search', 'list',
			'conjugationsGen', 'conjugationsGenAjax', 'conjugationsComponentAjax', 'verbs',
			'getAjax', 'translateAjax', 'wordExistsAjax', 'searchAjax',	'getRandomWordAjax',
			'heartAjax', 'unheartAjax',
			'setFavoriteList',
			'reviewNewest', 'reviewNewestVerbs', 'reviewRandomWords', 'reviewRandomVerbs',

			'favorites',
        ]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];

		try
		{
			$records = Definition::select()
				->where('type_flag', DEFTYPE_DICTIONARY)
				->orderByRaw('id DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('msgs.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = new Definition();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->definition	    = trimNull($request->definition);
        $record->permalink      = createPermalink($record->title);
        $record->type_flag      = isAdmin() ? DEFTYPE_DICTIONARY : DEFTYPE_USER;

		try
		{
			$record->save();

			$rc = __('msgs.New record has been added');
            logInfo($f, $rc, ['id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('msgs.Error adding new record'));
			return back();
		}

		return redirect('/definitions/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
    {
		$record = null;
		$permalink = alphanum($permalink);
        $releaseFlag = getReleaseFlagForUserLevel();
        $releaseFlagCondition = getConditionForUserLevel();

		try
		{
			$record = Definition::select()
				//->where('site_id', SITE_ID)
				->where('release_flag', $releaseFlagCondition, $releaseFlag)
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('permalink not found');
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('msgs.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Definition $definition)
    {
		$record = $definition;

		return view(PREFIX . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
    }

    public function update(Request $request, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->definition = copyDirty($record->definition, $request->definition, $isDirty, $changes);
		$record->examples = copyDirty($record->examples, $request->examples, $isDirty, $changes);
        $record->permalink = copyDirty($record->permalink, createPermalink($request->title, $record->created_at), $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo($f, __('msgs.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException($f, $e->getMessage(), __('msgs.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo($f, __('msgs.No changes made'), ['record_id' => $record->id]);
		}

		return redirect('/definitions/view/' . $record->id);
	}

    public function confirmDelete(Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;

		try
		{
			$record->delete();
			logInfo($f, __('msgs.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('msgs.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $id)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$id = intval($id);

		try
		{
			$record = Definition::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo($f, __('msgs.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('msgs.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Definition::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('msgs.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;

        if ($request->isMethod('get'))
        {
            // quick publish, set to toggle public / private
            $record->wip_flag = $record->isFinished() ? getConstant('wip_flag.dev') : getConstant('wip_flag.finished');
            $record->release_flag = $record->isPublic() ? RELEASEFLAG_PRIVATE : RELEASEFLAG_PUBLIC;
        }
        else
        {
            $record->wip_flag = $request->wip_flag;
            $record->release_flag = $request->release_flag;
        }

		try
		{
			$record->save();
			logInfo($f, __('msgs.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('msgs.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function createSnippet(Request $request)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
        $msg = null;
        $raw = trim($request->textEdit); // save the before version so we can tell if it gets changed
        $snippet = alphanumHarsh($raw);
        $tag = "Text";

		try
		{
            if (strlen($snippet) != strlen($raw))
            {
                $msg = __("proj.$tag has invalid characters");
    			logError(__FUNCTION__, $msg);
		        throw new \Exception($msg); // nope!
            }

            $msg = null;

            $exists = false;
            $record = Definition::getSnippet($snippet);
            if (isset($record))
            {
                // if it already exists let usere or visitor update it
                $exists = true;
                $record->visitor_id     = getVisitorInfo()['hash'];
            }
            else
            {
                $record = new Definition();
                $record->title 			= 'snippet-' . timestamp();
                $record->permalink		= createPermalink('snippet');
                $record->user_id        = Auth::check() ? Auth::id() : USER_ID_NOTSET;
                $record->type_flag 		= DEFTYPE_SNIPPET;
                $record->release_flag   = RELEASEFLAG_PUBLIC;
                $record->examples	    = Str::limit($snippet, 500);
                $record->visitor_id     = getVisitorInfo()['hash'];
            }

            $record->language_flag  = $request->language_flag;

		    if (strlen($snippet) < 10)
                $msg = __("proj.$tag is too short");

            if ($exists && !isAdmin())
                $msg = __("proj.$tag already exists");

            if (isset($msg))
		        throw new \Exception($msg); // nope!

			$record->save();
            Cookie::queue('snippetId', $record->id, 525600);

			$msg = $exists ? __("proj.$tag has been updated") : __("proj.New $tag has been saved");
			logInfo($f, $msg, ['title' => $record->title, 'id' => $record->id]);

    		return redirect($request->returnUrl);
		}
		catch (\Exception $e)
		{
		    //dump($record);
            //dd($e->getMessage());
			$msg = isset($msg) ? $msg : "Error adding new $tag";
            logException($f, $e->getMessage(), null, ['msg' => $msg]);
		}

		return back();
    }

	public function snippets()
    {
        //
        // all the stuff for the speak and record module
        //
        $siteLanguage = $this->getSiteLanguage()['id'];

        $options = [];
        $options['showAllButton'] = false;
        $options['loadSpeechModules'] = true;
        $options['siteLanguage'] = $siteLanguage;
        $options['records'] = Definition::getSnippets();
        $options['snippetLanguages'] = getLanguageOptions();
        $options['languageCodes'] = getSpeechLanguage($siteLanguage);
        $options['returnUrl'] = '/practice';

        // get the snippets for the appropriate langauge
		$languageFlagCondition = ($siteLanguage == LANGUAGE_ALL) ? '>=' : '=';
        $snippets = Definition::getSnippets(['languageId' => $siteLanguage, 'languageFlagCondition' => $languageFlagCondition]);
        $options['records'] = $snippets;

        // not implemented yet
        $snippetId = intval(Cookie::get('snippetId'));
        if (isset($snippetId) && $snippetId > 0)
        {
            $options['snippet'] = Definition::getByType(DEFTYPE_SNIPPET, $snippetId, 'id');
        }

        $options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;

		return view('gen.definitions.snippets', [
		    'options' => $options,
		]);
    }


	//
	// This handles the search form from the index/search page
	//
    public function searchAjax(Request $request, $text = null)
    {
		$text = getOrSetString(alpha($text), null);
        $records = [];

		try
		{
			session(['definitionSearch' => $text]);
			$records = Definition::searchPartial($text);
		}
		catch (\Exception $e)
		{
			$msg = 'Error finding text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), null, ['text' => $text]);
		}

		return view(PREFIX . '.component-search-results', [
			'records' => $records,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
	}

	//
	// This is now the main index/search page
	//
    public function search(Request $request, $sort = null)
    {
		$sort = intval($sort);
		$records = [];
		$search = '';

		if ($sort == DEFINITIONS_SEARCH_NOTSET)
		{
			// check if a previous sort was used
			$sort = session('definitionSort', 0);

			// check if a previous search word was used
			$search = session('definitionSearch', '');
		}
		else
		{
			// save current sort value for next time
			session(['definitionSort' => $sort]);

			// clear any previous search word if any kind of sort is set
			session(['definitionSearch' => null]);
		}

		try
		{
			if (isset($search) && strlen($search) > 0)
				$records = Definition::searchPartial($search);
			else
				$records = Definition::getIndex($sort, 20);
		}
		catch (\Exception $e)
		{
			$msg = 'Search dictionary error';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return view(PREFIX . '.search', [
			'records' => $records,
			'search' => $search,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
    }

	// open the conjugations view
    public function conjugationsGen(Request $request, Definition $definition)
    {
		$record = $definition;
		$records = Definition::conjugationsGen($record->title);
		$status = null;
		if (isset($records))
		{
			$status = $records['status'];
			$forms = $records['forms'];
			$records = $records['records'];
		}

		return view(PREFIX . '.conjugations', [
			'record' => $record,
			'records' => $records,
			'status' => $status,
		]);
    }

    public function conjugationsGenAjax(Request $request, $text)
    {
		$forms = null;

		$scraped = Definition::isIrregular($text);
		if ($scraped['irregular'])
		{
			$forms = $scraped['conj']['full'];
			//dump('scraped');
			//dd($forms);
		}
		else
		{
			$records = Definition::conjugationsGen($text);
			if (isset($records))
			{
				$forms = $records['forms'];
				//dump('gened');
				//dd($forms);
			}
		}

		return $forms;
    }

    public function scrapeDefinitionAjax(Request $request, $word)
    {
		$rc = Definition::scrapeDefinition($word);

		return $rc;
    }

	public function conjugationsComponentAjax(Request $request, Definition $definition)
    {
		$record = $definition;
		$record->conjugations = Definition::getConjugationsPretty($record->conjugations);

		return view(PREFIX . '.component-conjugations', [
			'record' => $record,
			]);
    }


    public function wordExistsAjax(Request $request, $text)
    {
		$rc = '';

		$record = Definition::get($text);
		if (isset($record))
		{
			$rc = "<a href='/definitions/view/" . $record->id . "'>" . $record->title . ": already in dictionary (show)</a>&nbsp;<a href='/definitions/edit/" . $record->id . "'>(edit)</a>";
		}

		return $rc;
    }

    public function getAjax(Request $request, $text, $entryId)
    {
		$entryId = intval($entryId);

		// 1. see if we already have it in the dictionary
		$record = Definition::search($text);
		if (isset($record))
		{
			// when a user looks up a word, add it to his def list for the entry being read
			Entry::addDefinitionUserStatic($entryId, $record);

			$xlate = null;
			if (!isset($record->translation_en))
			{
				$rc = "<a target='_blank' href='/definitions/view/$record->id'>$record->title</a>&nbsp;";
				if (isAdmin())
					$rc .= "<a target='_blank' href='/definitions/edit/$record->id'>(edit)</a>";

				$rc .= "<div class='mt-2'>found but translation not set</div>";
			}
			else
			{
				if ($record->title == $text)
				{
					// exact match of title
				}
				else
				{
					// matched either the forms or conjugations
				}

				$xlate = nl2br($record->translation_en);

				$rc = "<a target='_blank' href='/definitions/view/$record->id'>$record->title</a><div>$xlate</div>";
			}
		}
		else
		{
			// 2. not in our list, show link to MS Translate ajax
			$rc = "<a href='' onclick='event.preventDefault(); xlate(\"" . $text . "\");'>Translate</a>";

			if (isAdmin())
				$rc .= "<a class='ml-3' target='_blank' href='/definitions/add/" . $text . "'>Add</a>";

		}

		return $rc;
	}

    public function translateAjax(Request $request, $text, $entryId = null)
    {
		$entryId = intval($entryId);
		$rc = self::translateMicrosoft($text);

		if (strlen($rc['error']) == 0) // no errors
		{
			// add the translation to our dictionary for next time
			$translation = strtolower($rc['data']);
			$def = Definition::add($text, /* definition = */ null, $translation);

			// when a user translates a word, add it to his def list for the entry being read
			Entry::addDefinitionUserStatic($entryId, $def);

			if (isset($def))
			{
				$rc = "<a href='/definitions/view/$def->id/' target='_blank'>$def->title</a><div class='green mt-1'>$translation</div>";
			}
			else
			{
				$rc = $translation;
			}
		}
		else
		{
			$rc = '<div>' . $rc['error'] . '</div>';

			if (isAdmin())
				$rc .= "<div><a class='ml-3' target='_blank' href='/definitions/add/" . $text . "'>Add</a></div>";
		}

		return $rc;
	}

    static public function translateMicrosoft($text)
    {
		$rc = ['error' => '', 'data' => ''];
		$text = trim($text);
		if (strlen($text) == 0)
		{
			$rc['error'] = 'empty string';
			return $rc;
		}

		// NOTE: Be sure to uncomment the following line in your php.ini file.
		// ;extension=php_openssl.dll
		// You might need to set the full path, for example:
		// extension="C:\Program Files\Php\ext\php_openssl.dll"

		// Prepare variables
		//$text = 'comulgar';
		$path = "/translate?api-version=3.0";
		$params = "&to=en";

		// Prepare cURL command
		$key = env('MICROSOFT_API_KEY', '');
		$host = 'api-apc.cognitive.microsofttranslator.com';
		$region = 'australiaeast';

		$guid = sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
			mt_rand( 0, 0xffff ),
			mt_rand( 0, 0x0fff ) | 0x4000,
			mt_rand( 0, 0x3fff ) | 0x8000,
			mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
		);

		$requestBody = array (
			array (
				'Text' => $text,
			),
		);

		$content = json_encode($requestBody);
		//dd($content);

		$headers = "Content-type: application/json\r\n" .
			"Content-length: " . strlen($content) . "\r\n" .
			"Ocp-Apim-Subscription-Key: $key\r\n" .
			"Ocp-Apim-Subscription-Region: " . $region . "\r\n" .
			"X-ClientTraceId: " . $guid . "\r\n";
		//dd($headers);

		// NOTE: Use the key 'http' even if you are making an HTTPS request. See:
		// http://php.net/manual/en/function.stream-context-create.php
		$options = array (
			'http' => array (
				'header' => $headers,
				'method' => 'POST',
				'content' => $content
			)
		);
		//dd($options);

		$context  = stream_context_create($options);

		$url = 'https://' . $host . $path . $params;
		//dd($url);

		try {
			$json = file_get_contents($url, false, $context);
		}
		catch (\Exception $e)
		{
			$msg = 'Error Translating: ' . $text;

			if (strpos($e->getMessage(), '401') !== FALSE)
			{
				$msg .= ' - 401 Unauthorized';
			}

            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), null, ['msg' => $msg]);
			$result = $msg;
			$rc['error'] = $msg;
			return $rc;
		}
		//dd($result);

		$json = json_decode($json);
		//dd($json);

		if (count($json) > 0)
		{
			$json = $json[0]->translations;
			if (count($json) > 0)
			{
				//dd($json[0]);

				$xlate = strtolower($json[0]->text);
				if ($text == $xlate)
				{
					// if translation is same as the word, then it probably wasn't found
					$rc['error'] = 'translation not found';
				}
				else
				{
					$rc['data'] = $xlate;
				}
			}
			//dd($rc);
		}

		return $rc;
	}

  	public function setFavoriteList(Request $request, Definition $definition, $tagFromId, $tagToId)
    {
		$record = $definition;
        $rc = '';

        if (Auth::check())
        {
			$record->removeTag($tagFromId);
			$record->addTag($tagToId);
        }
        else
        {
			$rc = 'favorite not saved - you must log in';
        }

        logInfo('setFavoriteList', $rc, ['title' => $record->title, 'id' => $record->id]);
		return back();
    }

	public function heartAjax(Request $request, Definition $definition)
    {
		$record = $definition;
        $rc = '';

        if (Auth::check())
        {
			$tag = $record->addTagFavorite();
            if (isset($tag))
            {
                $rc = '';
            }
            else
            {
                $rc = 'not favorited: update failed';
            }
        }
        else
        {
			$rc = 'favorite not saved - you must log in';
        }

        logInfo('heartAjax', $rc, null, ['title' => $record->title, 'id' => $record->id]);
		return $rc;
    }

	public function unheartAjax(Request $request, Definition $definition)
    {
		$record = $definition;
        $rc = '';

        if (Auth::check())
        {
			if ($record->removeTagFavorite())
            {
                $rc = ''; // no msg means, no error
            }
            else
            {
                $rc = 'not unfavorited: update failed';
            }
        }
        else
        {
			$rc = 'favorite not removed - you must log in';
        }

        logInfo('unheartAjax', $rc, null, ['title' => $record->title, 'id' => $record->id]);

		return $rc;
    }

    public function review(Request $request, Tag $tag, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$record = $tag;
		$qna = Definition::makeQna($record->definitionsUser); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/definitions/list/' . $record->id . '',
			'touchPath' => '',
			'parentTitle' => $tag->name,
			'settings' => $settings,
			]);
    }

    public function reviewNewest(Request $request, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$records = Definition::getNewest(20);
		$qna = Definition::makeQna($records); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/vocabulary',
			'touchPath' => '',
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			]);
    }

    public function reviewNewestVerbs(Request $request, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$records = Definition::getNewestVerbs(20);
		$qna = Definition::makeQna($records); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/vocabulary',
			'touchPath' => '',
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			]);
    }

    public function reviewRandomWords(Request $request, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$records = Definition::getRandomWords(20);
		$qna = Definition::makeQna($records); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/vocabulary',
			'touchPath' => '',
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			]);
    }

	public function reviewRandomVerbs(Request $request, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$records = Definition::getRandomVerbs(20);
		$qna = Definition::makeQna($records); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/vocabulary',
			'touchPath' => '',
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			]);
    }

    public function getRandomWordAjax(Request $request)
    {
		$record = Definition::getRandomWord();

		return view('components.random-word', [
			'record' => $record,
			]);
	}

	public function verbs(Request $request, $verb)
    {
		$record = Definition::get($verb);

		if (isset($record->conjugations))
			$record->conjugations = Definition::getConjugationsFull($record->conjugations);

		return view('definitions.verb', [
			'record' => $record,
			'headers' => Definition::$_verbConjugations,
			]);
    }

    public function list(Request $request, Tag $tag)
    {
		$records = []; // make this countable so view will always work
		try
		{
			$records = $tag->definitionsUser()->orderBy('title', 'asc')->get();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('msgs.Error getting list'));
		}

		return view(PREFIX . '.list', [
			'records' => $records,
			'tag' => $tag,
			'lists' => Definition::getUserFavoriteLists(),
//			'favoriteListsOptions' => Definition::getUserFavoriteListsOptions(),
		]);
    }

    public function favorites(Request $request)
    {
		// definitions favorites
		$favorites = Definition::getUserFavoriteLists();

		// articles/books look ups
		//todo: $entries = Entry::getDefinitionsUser();

		return view(PREFIX . '.favorites', [
			'favorites' => $favorites,
			//'newest' => true, // show the option for "New Dictionary Entries" review and flashcards
		]);
    }
}
