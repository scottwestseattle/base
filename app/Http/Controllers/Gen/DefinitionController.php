<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Cookie;
use Log;

use App\Entry;
use App\Gen\Definition;
use App\Gen\Spanish;
use App\Quiz;
use App\Site;
use App\Status;
use App\Tag;
use App\User;

define('PREFIX', 'definitions');
define('VIEWS', 'gen.definitions');
define('LOG_CLASS', 'DefinitionController');

class DefinitionController extends Controller
{
	private $redirectTo = PREFIX;
    static private $_showLink = '/definitions/show/';
    static private $_displayLink = '/dictionary/definition/';

	public function __construct()
	{
        $this->middleware('admin')->except([
            //'index',
            'view', 'permalink',
            'snippets', 'createSnippet', 'readSnippets',

            // copied
			'find', 'search', 'list-tag',
			'conjugationsGen', 'conjugationsGenAjax', 'conjugationsComponentAjax', 'verbs',
			'getAjax', 'translateAjax', 'wordExistsAjax', 'searchAjax',	'getRandomWordAjax',
			'heartAjax', 'unheartAjax',
			'setFavoriteList',
			'reviewNewest', 'reviewNewestVerbs', 'reviewRandomWords', 'reviewRandomVerbs', 'reviewRankedVerbs',

			'favorites',
			'setSnippetCookie',
        ]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];

		try
		{
			$records = Definition::select()
				//->where('type_flag', DEFTYPE_DICTIONARY)
				->orderByRaw('type_flag, updated_at desc')
				->get();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add($word = null)
    {
		$word = alpha($word);

		return view(VIEWS . '.add', [
				'word' => $word,
			]);
	}

    public function create(Request $request)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;

		$title = trim($request->title);
		$record = Definition::get($title);
		if (isset($record))
		{
			flash('danger', __('base.record already exists'));
			return redirect('/' . PREFIX . '/edit/' . $record->id);
		}

		$record = new Definition();

		$record->user_id 		= Auth::id();
		$record->language_flag 	= LANGUAGE_ES;
		$record->type_flag      = DEFTYPE_DICTIONARY;
		$record->title 			= $request->title;
		$record->forms 			= Spanish::formatForms($request->forms);
		$record->definition		= $request->definition;
		$record->translation_en	= $request->translation_en;
		$record->examples		= $request->examples;
		$record->permalink		= createPermalink($request->title);
		$record->wip_flag		= WIP_DEFAULT;
		$record->rank   		= $request->rank;
		$record->pos_flag   	= $request->pos_flag;

		try
		{
			// format the forms and conjugations if it's a verb
			$conj = Spanish::getConjugations($request->conjugations);
			$record->conjugations = $conj['full'];
			$record->conjugations_search = $conj['search'];

			if ($record->isConjugated())
    		    $record->pos_flag = DEFINITIONS_POS_VERB;
		}
		catch (\Exception $e)
		{
			$msg = __('proj.Record not added: error getting conjugations');
			logException($f, $e->getMessage(), $msg);
			return back();
		}

		try
		{
			$record->save();

			$msg = __('base.New record has been added');
			logInfo($f, $msg, ['title' => $record->title, 'definition' => $record->definition, 'id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = isset($msg) ? $msg : __('proj.Error adding new definition');
			logException($f, $e->getMessage(), $msg, ['title' => $record->title]);

			return back();
		}

		return redirect('/definitions/view/' . $record->permalink);
    }

    public function permalink(Request $request, $permalink)
    {
		$record = null;
		$permalink = alphanum($permalink);

		try
		{
			$record = Definition::select()
				->where('permalink', $permalink)
				->first();

			if (blank($record))
			    throw new \Exception('permalink not found');
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return $this->view($record);
	}

    public function display(Request $request, $word)
    {
		$record = null;
		$word = alpha($word);

		try
		{
			$record = Definition::select()
				->where('title', $word)
				->first();

			if (blank($record))
			    throw new \Exception('definition not found');
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Definition not found'), ['word' => $word]);
    		return redirect($this->redirectTo);
		}

		return $this->view($record);
	}

	public function verbs(Request $request, $verb)
    {
        $verb = alpha($verb);
		$record = Definition::get($verb);

		if (isset($record->conjugations)) // if it's a verb
		{
			$record->conjugations = Spanish::getConjugationsFull($record->conjugations);
			$record->conjugationHeaders = Spanish::$_verbConjugations;
		}

		return view('gen.definitions.verb', [
			'record' => $record,
			'showTitle' => true,
			]);
    }

	public function find(Request $request, $text)
    {
		$record = Definition::search($text);
		if (isset($record))
		{
			// update the view timestamp so it will move to the back of the list
			//todo: $record->updateLastViewedTime();

    		return $this->view($record);
		}
		else
		{
			$word = alphanum($text, /* strict = */ true);
		    $msg = trans('proj.Definition not found') . ': ' . "<a target='_blank' href='https://www.spanishdict.com/translate/" . $word . "'>" . $word . "</a>";
		    logWarning(null, $msg, ['word' => $word]);
		    return back();
		}

	}

	public function view(Definition $definition)
    {
		$record = $definition;

		// format the examples to display as separate sentences
		$record->examples = splitSentences($record->examples);

        // format the conjugations
		if ($record->isConjugated())
		{
			//$record->conjugations = Spanish::getConjugationsPretty($record->conjugations);
			$record->conjugations = Spanish::getConjugationsFull($record->conjugations);
			$record->conjugationHeaders = Spanish::$_verbConjugations;
		}

        $lists = Definition::getUserFavoriteLists();

		return view(VIEWS . '.view', [
			'record' => $record,
			'favoriteLists' => $lists,
			]);
    }

	public function edit(Definition $definition)
    {
		$record = $definition;
		$forms = null;

		if (isset($record->forms))
		{
			// make it prettier
			$forms = Spanish::getFormsPretty($record->forms);
		}
		else
		{
			//$records = Definition::conjugationsGen($definition->title);
			//if (isset($records))
			//{
			//	$forms = $records['formsPretty'];
			//	$record->forms = $forms;
			//}
		}

		return view(VIEWS . '.edit', [
			'record' => $record,
			'formsPretty' => $forms,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
    }

    public function update(Request $request, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;
		$isDirty = false;
		$changes = '';
		$parent = null;

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		if ($isDirty)
		{
		    // only make new permalink if title changes
		    $record->permalink = copyDirty($record->permalink, createPermalink($request->title), $isDirty, $changes);
		}

        // one time call to fix all records
        //Definition::fixAll();

		$record->definition = copyDirty($record->definition, $request->definition, $isDirty, $changes);
		$record->translation_en = copyDirty($record->translation_en, $request->translation_en, $isDirty, $changes);
		$record->examples = copyDirty($record->examples, $request->examples, $isDirty, $changes);
		$record->rank = copyDirty($record->rank, intval($request->rank), $isDirty, $changes);
		$record->pos_flag = copyDirty($record->pos_flag, intval($request->pos_flag), $isDirty, $changes);

		$forms 	= Spanish::formatForms($request->forms);
		$record->forms = copyDirty($record->forms, $forms, $isDirty, $changes);

		$type = (Str::startsWith($record->title, 'snippet')) ? DEFTYPE_SNIPPET : DEFTYPE_DICTIONARY;
		$record->type_flag = copyDirty($record->type_flag, $type, $isDirty, $changes);

		try
		{
			// this will check if a raw conjugations has been entered and if so, clean it
			// if it's not raw, then it just sends it back
			$conj = Spanish::getConjugations($request->conjugations);
			$record->conjugations = copyDirty($record->conjugations, $conj['full'], $isDirty, $changes);
			$record->conjugations_search = copyDirty($record->conjugations, $conj['search'], $isDirty, $changes);
		}
		catch (\Exception $e)
		{
			$msg = __('proj.Changes not saved: error getting conjugations');
			logException($f, $msg, $e->getMessage());
			return back();
		}

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo($f, __('proj.Definition has been updated'), ['title' => $record->title, 'id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				$msg = __('base.Error updating record');
				logException($f, $e->getMessage(), $msg);
			}
		}
		else
		{
			logFlash('info', $f, __('base.No changes were made'));
		}

        $returnPath = '/definitions/view/' . $record->permalink;

		return redirect($returnPath);
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
			logInfo($f, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
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
			logInfo($f, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
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
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting deleted records'));
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
			logInfo($f, __('base.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('base.Error updating record status'), ['record_id' => $record->id]);
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
    			logError(__FUNCTION__, $msg, ['snippet' => $snippet, 'raw' => $raw]);
		        throw new \Exception($msg); // nope!
            }

            $msg = null;

            $exists = false;
            $record = Definition::getSnippet($snippet);
            if (isset($record))
            {
                // if it already exists let user or visitor update it
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
			self::setSnippetCookie($record->id);

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

	static public function setSnippetCookie($id)
    {
        Cookie::queue('snippetId', intval($id), MS_YEAR);
    }

	public function snippets($id = null)
    {
        //
        // all the stuff for the speak and record module
        //
        $siteLanguage = Site::getLanguage()['id'];

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

        // command line supercedes cookie
        $id = isset($id) ? intval($id) : intval(Cookie::get('snippetId'));
        if ($id > 0)
            $options['snippet'] = Definition::getByType(DEFTYPE_SNIPPET, $id, 'id');

        $options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;

		return view('gen.definitions.snippets', [
		    'options' => $options,
		]);
    }

	public function readSnippets()
    {
        $siteLanguage = Site::getLanguage()['id'];
		$languageFlagCondition = ($siteLanguage == LANGUAGE_ALL) ? '>=' : '=';

        $records = Definition::getSnippets(['languageId' => $siteLanguage, 'languageFlagCondition' => $languageFlagCondition]);

        $lines = [];
        $languageFlag = null;
        foreach($records as $record)
        {
    		$text = Spanish::getSentences($record->examples);
    		$lines = array_merge($lines, $text);
    		if (!isset($languageFlag))
    		    $languageFlag = $record->language_flag;
        }

        $options['return'] = '/practice';

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => 'Practice Text',
    	    'options' => $options,
			'contentType' => 'Snippet',
			'languageCodes' => getSpeechLanguage($languageFlag),
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

		return view(VIEWS . '.component-search-results', [
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

		return view(VIEWS . '.search', [
			'records' => $records,
			'search' => $search,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
    }

	// open the conjugations view
    public function conjugationsGen(Request $request, Definition $definition)
    {
		$record = $definition;
		$records = Spanish::conjugationsGen($record->title);
		$status = null;
		if (isset($records))
		{
			$status = $records['status'];
			$forms = $records['forms'];
			$records = $records['records'];
		}

		return view(VIEWS . '.conjugations', [
			'record' => $record,
			'records' => $records,
			'status' => $status,
		]);
    }

    public function conjugationsGenAjax(Request $request, $text)
    {
		$forms = null;

		$scraped = Spanish::isIrregular($text);
		if ($scraped['irregular'])
		{
		    if (isset($scraped['conj']['full']))
			    $forms = $scraped['conj']['full'];
			else
			    $forms = $scraped['error'];
		}
		else
		{
			$records = Spanish::conjugationsGen($text);
			if (isset($records))
			{
				$forms = $records['forms'];
			}
		}

		return $forms;
    }

    public function scrapeDefinitionAjax(Request $request, $word)
    {
		$rc = Spanish::scrapeDefinition($word);

		return $rc;
    }

	public function conjugationsComponentAjax(Request $request, Definition $definition)
    {
		$record = $definition;
		$record->conjugations = Spanish::getConjugationsPretty($record->conjugations);

		return view(VIEWS . '.component-conjugations', [
			'record' => $record,
			]);
    }

    public function wordExistsAjax(Request $request, $text)
    {
		$rc = '';

		$record = Definition::get($text);
		if (isset($record))
		{
			$rc = "<a href='" . self::$_showLink . $record->id . "'>" . $record->title . ": already in dictionary (show)</a>&nbsp;<a href='/definitions/edit/" . $record->id . "'>(edit)</a>";
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
				$rc = "<a target='_blank' href='self::$_displayLink$record->id'>$record->title</a>&nbsp;";
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

				$rc = "<a target='_blank' href='" . self::$_showLink . $record->id . "'>$record->title</a><div>$xlate</div>";
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
				$rc = "<a href='" . self::$_showLink . $def->id . "/' target='_blank'>$def->title</a><div class='green mt-1'>$translation</div>";
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
        $rc = 'proj.Saved to favorite list';

        if (Auth::check())
        {
			$record->removeTag($tagFromId);
			$record->addTag($tagToId);
        }
        else
        {
			$rc = 'proj.Favorite not saved - you must log in';
        }

        logInfo('setFavoriteList', __($rc), ['title' => $record->title, 'id' => $record->id]);

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
                $rc = 'proj.not favorited: update failed';
            }
        }
        else
        {
			$rc = 'proj.Favorite not saved - you must log in';
        }

        logInfo('heartAjax', $rc, null, ['title' => $record->title, 'id' => $record->id]);

        if (strlen($rc) > 0)
            $rc = __($rc);

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
			'returnPath' => '/favorites',
			'touchPath' => '',
			'parentTitle' => $tag->name,
			'settings' => $settings,
			]);
    }

    public function reviewNewest(Request $request, $reviewType = null)
    {
        $records = Definition::getNewest(20);
        return $this->doList('proj.20 Newest Words', $reviewType, $records);
    }

    public function doList($name, $reviewType, $records)
    {
        $reviewType = alphanum($reviewType);

        if (Quiz::isQuiz($reviewType))
        {
            return $this->doReview($records, $reviewType);
        }
        else
        {
            return $this->list(trans($name), $records);
        }
    }

    public function doReview($records, $reviewType)
    {
		$qna = Definition::makeQna($records); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		return view($settings['view'], [
			'sentenceCount' => count($qna),
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
			'returnPath' => '/favorites',
			'touchPath' => '',
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			]);
    }

    private function list($name, $records)
    {
		return view(VIEWS . '.list', [
		    'name' => $name,
			'records' => $records,
		]);
    }

    public function reviewRankedVerbs(Request $request, $reviewType = null)
    {
    //dd($_SERVER);
        $records = Definition::getRankedVerbs(20);
        return $this->doList('proj.20 Most Common Verbs', $reviewType, $records);
    }

    public function reviewNewestVerbs(Request $request, $reviewType = null)
    {
		$records = Definition::getNewestVerbs(20);
        return $this->doList('proj.20 Newest Verbs', $reviewType, $records);
    }

    public function reviewRandomWords(Request $request, $reviewType = null)
    {
		$records = Definition::getRandomWords(20);
        return $this->doList('proj.20 Random Words', $reviewType, $records);
    }

	public function reviewRandomVerbs(Request $request, $reviewType = null)
    {
		$records = Definition::getRandomVerbs(20);
        return $this->doList('proj.20 Random Verbs', $reviewType, $records);
    }

    public function getRandomWordAjax(Request $request)
    {
		$record = Definition::getRandomWord();

		return view('components.random-word', [
			'record' => $record,
			]);
	}

    public function listTag(Request $request, Tag $tag)
    {
		$records = []; // make this countable so view will always work
		try
		{
			$records = $tag->definitionsUser()->get();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting list'));
		}

		return view(VIEWS . '.list', [
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

        // user's words and phrases: DEFTYPE_USER
        //todo $userWords = Definition::???

		// articles/books look ups
		//todo: $entries = Entry::getDefinitionsUser();

        // newest words
        //$newest = Defintions::getNewest(20);

		return view(VIEWS . '.favorites', [
			'favorites' => $favorites,
			'newest' => true,
		]);
    }

	public function toggleWipAjax(Request $request, Definition $definition, $done = true)
    {
		$record = $definition;
        $msg = null;

		try
		{
    		$msg = 'Set to ' . ($record->toggleWip() ? 'finished' : 'unfinished');
	    	logInfo($msg, null, ['id' => $record->id]);
            //throw new \Exception('test exception');
		}
		catch (\Exception $e)
		{
			$msg = 'Error toggling status';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
		}

		return __('base.' . $msg);
    }

}
