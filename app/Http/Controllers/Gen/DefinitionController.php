<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Auth;
use Config;
use Cookie;
use DateTime;
use DB;
use Lang;
use Log;

use App\Entry;
use App\Gen\Definition;
use App\Gen\Exercise;
use App\Gen\History;
use App\Gen\Spanish;
use App\Gen\Stat;
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

            // dictionary
            'createQuick', 'stats', 'updateStats',

            // definitions
            'view', 'permalink',
			'confirmDelete', 'delete',
            'add', 'create',
            'edit', 'update', 'editOrShow',

            // snippets
            'snippets', 'indexSnippets', 'filterSnippets',
            'readSnippets', 'readSnippetsLatest', 'viewSnippet',

            // let these through to be caught below
			'createSnippet', 'editSnippet', 'updateSnippet',

            // copied
			'find', 'search', 'listTag',
			'conjugationsGen', 'conjugationsGenAjax', 'conjugationsComponentAjax', 'verbs',
			'getAjax', 'translateAjax', 'wordExistsAjax', 'searchAjax', 'getRandomWordAjax',
			'heartAjax', 'unheartAjax',
			'setFavoriteList', 'moveFavorites',

            // review
			'review',
			'reviewNewest', 'reviewNewestVerbs',
			'reviewRandomWords', 'reviewRandomVerbs',
			'reviewRankedVerbs',
			'reviewSnippets', 'snippetsFlashcards', 'snippetsQuiz', 'flashcardsNewest',
			'reviewDictionary',
			'readExamples',
			'favoritesFlashcards', 'favoritesQuiz',
			'favoritesReview',

            // favorites lists
			'favorites', 'favoritesRss', 'favoritesRssReader',
			'setSnippetCookie', 'readList', 'convertTextToFavorites',
			'convertQuestionsToSnippets',
        ]);

        $this->middleware('auth')->only([
			'add',
			'create',
			'createQuick',
			'createSnippet',
			'favoritesReview',
            'convertTextToFavorites',
			'convertQuestionsToSnippets',
		]);

        $this->middleware('owner')->only([
			'edit', 'update',
			'editSnippet', 'updateSnippet',
			'review', 'readList',
			'confirmDelete', 'delete',
			'unheartAjax', 'moveFavorites', 'convertTextToFavorites',
			'convertQuestionsToSnippets',
		]);

		parent::__construct();
	}

    //
    // NOT THE MAIN INDEX PAGE - See search() function
    // This is the Admin Index Page
    //
    public function index(Request $request)
    {
		$records = [];
		$parms = crackParms($request, ['orderBy' => 'definitions.id desc']);
        //dump($parms);

		try
		{
			$records = Definition::select('definitions.*', 'users.name')
                ->leftJoin('users', function($join) {
                    $join->on('users.id', 'definitions.user_id');
                })
				->where('definitions.language_flag', getLanguageId())
				->orderByRaw($parms['orderBy'])
				->limit($parms['count'])
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

    public function add(Request $request, $locale, $word = null)
    {
		if ($request->isMethod('post'))
        {
             $word = alpha($request->title);
        }
        else
        {
    		$word = alpha($word);
        }

		return view(VIEWS . '.add', [
				'word' => $word,
			]);
	}

    public function create(Request $request, $locale)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;

		$title = trim($request->title);
		$record = Definition::get($title);
		if (isset($record))
		{
			flash('danger', __('base.record already exists'));
			return redirect(route('definitions.edit', ['locale' => $locale, 'definition' => $record]));
		}

		$record = new Definition();

		$record->user_id 		= Auth::id();
		$record->title 			= $title;
		$record->forms 			= Spanish::formatForms($request->forms);
		$record->definition		= $request->definition;
		$record->translation_en	= $request->translation_en;
		$record->examples		= $request->examples;
		$record->permalink		= createPermalink($request->title);
		$record->wip_flag		= WIP_DEFAULT;
		$record->rank   		= $request->rank;

		$record->language_flag  = getLanguageId();
		$record->pos_flag   	= isset($request->pos_flag) ? $request->pos_flag : DEFINITIONS_POS_SNIPPET;
		$record->type_flag      = ($record->pos_flag == DEFINITIONS_POS_SNIPPET) ? DEFTYPE_SNIPPET : DEFTYPE_DICTIONARY;
		$record->release_flag  = isAdmin() ? RELEASEFLAG_PUBLIC : RELEASEFLAG_PRIVATE;

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

        $url = route('dictionary', ['locale' => app()->getLocale()]) . '/search/3';

		return redirect($url);
    }

    public function createQuick(Request $request, $locale, $title = null)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;

		if ($request->isMethod('post'))
        {
            $title = getOrSet($request->title);
        }

        // title can come from commandl like or from form
        $title = alphanum(trim($title));

        if (empty($title))
        {
            $msg = __('proj.text is blank');
            logException($f, $msg);
            return redirect('/');
        }

		$record = Definition::get($title);
		if (isset($record))
		{
			flash('danger', __('base.record already exists'));
			return redirect('/' . PREFIX . '/edit/' . $record->id);
		}
		$record = Definition::getSnippet($title);
		if (isset($record))
		{
			flash('danger', __('base.record already exists'));
			return redirect('/' . PREFIX . '/edit/' . $record->id);
		}

		$record = new Definition();

		$record->user_id 		= Auth::id();
		$record->language_flag 	= LANGUAGE_ES;
		$record->title 			= $title;
		$record->permalink		= createPermalink($title);
		$record->wip_flag		= WIP_DEFAULT;
		$record->release_flag   = RELEASEFLAG_PRIVATE;
		$record->pos_flag   	= DEFINITIONS_POS_SNIPPET;
		$record->type_flag      = DEFTYPE_SNIPPET;

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

    public function permalink(Request $request, $locale, $permalink)
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

            $record->view_count++;
            $record->save();

            $record->tagUser();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return $this->view($request, $locale, $record);
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
            flash('warning', $msg);
		    //logWarning(null, $msg, ['word' => $word]);
		    return back();
		}

	}

	public function view(Request $request, $locale, Definition $definition)
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

        Stat::addView($record->id);

		return view(VIEWS . '.view', [
			'record' => $record,
			'favoriteLists' => $lists,
			]);
    }

	public function editOrShow(Request $request, $locale, Definition $definition)
    {
		$record = $definition;

        if ($record->user_id == Auth::id()) // if it's mine
            //return redirect('/definitions/edit/' . $record->id);
            return redirect(route('definitions.edit', ['locale' => app()->getLocale(), 'definition' => $record->id]));
        else
            //return redirect('/definitions/show/' . $record->id);
            return redirect(route('definitions.show', ['locale' => app()->getLocale(), 'definition' => $record->id]));
    }

	public function edit(Request $request, $locale, Definition $definition)
    {
		$record = $definition;
		$forms = null;

		if (isset($record->forms))
		{
			// make it prettier
			$forms = Spanish::getFormsPretty($record->forms);
		}

        $wordNumbers = Quiz::getWordNumbers($record->title);

        Site::setReturnPathSession(); // save the return path for when update() is called

		return view(VIEWS . '.edit', [
			'record' => $record,
			'formsPretty' => $forms,
			'favoriteLists' => Definition::getUserFavoriteLists(),
			'wordNumbers' => $wordNumbers,
		]);
    }

    public function update(Request $request, $locale, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;
		$isDirty = false;
		$changes = '';
		$parent = null;
		$returnPath = null;

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);

		if ($isDirty) // only update the premalink and token if text has changed
		{
    		$record->permalink = copyDirty($record->permalink, createPermalink($request->title), $isDirty, $changes);
            $returnPath = Site::getReturnPathSession(route('definitions.show', ['locale' => $locale, 'definition' => $record->id]));
            if (strpos($returnPath, 'view') > 0) // if it's going to fail because it has the old permalink, fix it
            {
                // klugey way to fix the return path piece by piece
                $parts = explode('/', $returnPath);
                if (count($parts) >= 5)
                {
                    $returnPath = '';
                    $parts[4] = $record->permalink;
                    foreach($parts as $part)
                    {
                        if (!empty($part))
                            $returnPath .= '/' . $part;
                    }
                }
            }
		}

		$record->examples = copyDirty($record->examples, $request->examples, $isDirty, $changes);

		$record->pos_flag = copyDirty($record->pos_flag, intval($request->pos_flag), $isDirty, $changes);
		$type = ($record->pos_flag == DEFINITIONS_POS_SNIPPET) ? DEFTYPE_SNIPPET : DEFTYPE_DICTIONARY;
		$record->type_flag = copyDirty($record->type_flag, $type, $isDirty, $changes);

        // one time call to fix all records
        //Definition::fixAll();

		$record->definition = copyDirty($record->definition, $request->definition, $isDirty, $changes);
		$record->translation_en = copyDirty($record->translation_en, $request->translation_en, $isDirty, $changes);
		$record->examples = copyDirty($record->examples, $request->examples, $isDirty, $changes);
		$record->notes = copyDirty($record->notes, $request->notes, $isDirty, $changes);
		$record->rank = copyDirty(intval($record->rank), intval($request->rank), $isDirty, $changes);
		$record->language_flag = copyDirty($record->language_flag, $request->language_flag, $isDirty, $changes);

		if (isAdmin()) // only admin can change user_id and release status
		{
    		$record->release_flag = copyDirty($record->release_flag, isset($request->public) ? RELEASEFLAG_PUBLIC : RELEASEFLAG_NOTSET, $isDirty, $changes);
		    $record->user_id = copyDirty($record->user_id, intval($request->user_id), $isDirty, $changes);

		    if (isset($request->public))
		    {
		        // user 1 owns the public dictionary entries
		        $record->user_id = 1;
		    }
		}

		$forms 	= Spanish::formatForms($request->forms);
		$record->forms = copyDirty($record->forms, $forms, $isDirty, $changes);

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

                // apply any category tags that are set
                if ($record->isSnippet())
                {
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_GENDER, $request->cat1);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_PRETERITE, $request->cat2);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_PHRASING, $request->cat3);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_REFLEXIVE, $request->cat4);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_SUBJUNCTIVE, $request->cat5);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_OBJECT, $request->cat6);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_PREPOSITION, $request->cat7);
                    $record->tagCategory(SNIPPET_CATEGORY_ESP_GRAMMAR, $request->cat8);
                }
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

        if (empty($returnPath))
            $returnPath = Site::getReturnPathSession(route('definitions.show', ['locale' => $locale, 'definition' => $record->id]));

		return redirect($returnPath);
	}

	public function editSnippet(Definition $definition)
    {
		$record = $definition;
		$forms = null;
        Site::setReturnPathSession(); // save the return path for when update() is called

		return view(VIEWS . '.edit-snippets', [
			'record' => $record,
			'formsPretty' => isset($record->forms) ? Spanish::getFormsPretty($record->forms) : null,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
    }

    public function updateSnippet(Request $request, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;
		$isDirty = false;
		$changes = '';
		$parent = null;

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->translation_en = copyDirty($record->translation_en, $request->translation_en, $isDirty, $changes);
		$record->language_flag = copyDirty($record->language_flag, $request->language_flag, $isDirty, $changes);

		if ($isDirty)
		{
		    $saveError = false;
			try
			{
			    if (strlen($record->title) == 0)
			        throw new \Exception('text can\'t be blank');

                $saveError = true;
				$record->save();
				logInfo($f, __('proj.Practice Text has been updated'), ['title' => $record->title, 'id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
			    if ($saveError)
				    $msg = __('base.Error updating record');
                else
                    $msg = $e->getMessage();

				logException($f, $e->getMessage(), $msg);

    			return back();
			}
		}
		else
		{
			logFlash('info', $f, __('base.No changes were made'));
		}

        $returnPath = Site::getReturnPath();

		return redirect($returnPath);
	}

    public function confirmDelete(Request $request, $locale, Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, $locale, Definition $definition)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = $definition;

		try
		{
			if ($record->removeTags())
            {
    			logInfo($f, __('base.Tag(s) removed before delete'), ['record_id' => $record->id]);
            }

			$record->delete();
			logInfo($f, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException($f, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect('/');
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

    public function createSnippet(Request $request, $locale)
    {
        $f = __CLASS__ . ':' . __FUNCTION__;
        $msg = null;
        $raw = trim($request->textEdit); // save the before version so we can tell if it gets changed
        $snippet = alphanumHarsh($raw);
        $translation = alphanumHarsh(trim($request->textEditTranslation));
        $tag = "Text";

        try
        {
            // check for links
            $snippet = str_replace('http', '', $snippet);
            $snippet = str_replace('.com', '', $snippet);

            if (strlen($snippet) != strlen($raw))
            {
    			$msg = "Snippet with link not saved: " . str_replace("\r\n", ' ', $raw);
		        throw new \Exception($msg); // nope!
            }
        }
		catch (\Exception $e)
		{
		    // log and fail silently
            logException($f, $e->getMessage());
            return back();
		}

		try
		{
            if (strlen($snippet) != strlen($raw))
            {
                $msg = __("proj.$tag has invalid characters");
    			logError(__FUNCTION__, $msg, ['snippet' => $snippet, 'raw' => $raw]);
		        throw new \Exception($msg); // nope!
            }

            $msg = null;
            $translation = Str::limit($translation, 500);

            //
            // check it if exists already
            //
            $exists = false;
            $link = null;
            $record = Definition::getSnippet($snippet); // check snippets
            if (isset($record))
            {
                // if it already exists let user or visitor update it
                $exists = true;
                $record->visitor_id = getVisitorInfo()['hash'];
                $link = route('definitions.view', ['locale' => $locale, 'permalink' => $record->permalink]);
            }

            if (!$exists) // not found in snippets, check dictionary
            {
                $record = Definition::get($snippet); // check dictionary
                if (isset($record))
                {
                    $exists = true;
                    $link = route('definitions.view', ['locale' => $locale, 'permalink' => $record->permalink]);
                }
            }

            //
            // create it for the user as private
            //

            if ($exists)
            {
                //
                // check if only translation has been updated
                //
                if ($record->translation_en != $translation)
                {
                    $record->translation_en = $translation;
        			$record->save();

                    logInfo($f, __("proj.Translation has been updated"), ['title' => $record->title, 'id' => $record->id, 'translation' => $record->translation_en]);

                    return redirect($request->returnUrl);
                }
            }
            else
            {
                $record = new Definition();

                $record->title          = Str::limit($snippet, 500);
                $record->translation_en = Str::limit($translation, 500);
                $record->user_id        = Auth::check() ? Auth::id() : USER_ID_NOTSET;
                $record->type_flag 		= DEFTYPE_SNIPPET;
                $record->pos_flag 		= DEFINITIONS_POS_SNIPPET;
                $record->release_flag   = isAdmin() ? RELEASEFLAG_PRIVATE : RELEASEFLAG_PRIVATE;
                $record->visitor_id     = getVisitorInfo()['hash'];

                $text = getWords($record->title, DEF_PERMALINK_WORDS); // only use the first X words
                $record->permalink		= createPermalink($text);
            }

            $record->language_flag = getLanguageId();

            $siteLanguage = Site::getLanguage()['id'];
            if ($record->language_flag != $siteLanguage)
                $msg = __("Language does not match: " . $record->language_flag);

		    if (!isset($snippet) || strlen($snippet) === 0)
                $msg = __("proj.$tag is too short");

            if ($exists)
                $msg = __("proj.$tag already exists");

            if (isset($msg))
		        throw new \Exception($msg); // nope!

			$record->save();
			$this->setSnippetCookie($record->id);

			$msg = $exists ? __("proj.$tag has been updated") : __("proj.New $tag has been saved");
			logInfo($f, $msg, ['title' => $record->title, 'id' => $record->id]);

    		return redirect($request->returnUrl);
		}
		catch (\Exception $e)
		{
		    // if $msg from above doesn't show, then it's a programming error, check log events
			$msg = isset($msg) ? $msg : "Error adding new $tag";

			if (isset($link))
			{
    			$msg .= ': <a href="' . $link . '">' . (strlen($snippet) <= 15 ? $snippet : 'show') . '</a>'; //route()
			}

            logException($f, $e->getMessage(), $msg, ['msg' => $msg]);
		}

		return back();
    }

	public function setSnippetCookieNEW(Request $request, Definition $definition)
    {
        //dump($definition->id);
        Cookie::queue('snippetId', intval($definition->id), COOKIE_WEEK);
    }

	public function setSnippetCookie($id)
    {
        // set the cookie so it will be loaded in the big frontpage edit box
        Cookie::queue('snippetId', intval($id), COOKIE_WEEK);

        // touch the record to move to top of list
        Definition::touchId($id);

        if (Auth::check())
        {
            // update it's view and timestamp for the user
            Definition::tagDefinitionUser($id);
            Stat::addView($id);
        }
    }

	public function viewSnippet(Request $request, $locale, $permalink)
    {
		$permalink = alphanum($permalink, /* strict = */ true);
		$record = Definition::getPermalink($permalink);
		if (isset($record))
		{
		    return $this->snippets($record->id);
        }
        else
        {
            return back();
        }
    }

	public function showSnippet(Request $request, $locale, Definition $definition)
    {
		$record = $definition;

		if (isset($record))
		{
		    return $this->snippets($record->id);
        }
        else
        {
            return back();
        }
    }

	public function filterSnippets(Request $request, $parms)
    {
        // $request parameters are accessed as: $request['order']

		return $this->getSnippets($request);
    }

	public function indexSnippets(Request $request)
    {
        $request['showForm'] = true;
        return $this->getSnippets($request);
    }

	public function snippets($id = null)
    {
        $parms['id'] = isset($id) ? intval($id) : null;
        $parms['showForm'] = true;
        $parms['order'] = 'desc';

        return $this->getSnippets($parms);
    }

	public function getSnippets($parms)
    {
        $options = [];

        $count = isset($parms['count']) ? $parms['count'] : 50;
        $start = isset($parms['start']) ? $parms['start'] : 0;
        $showForm = isset($parms['showForm']) ? $parms['showForm'] : false;
        $id = isset($parms['id']) ? $parms['id'] : null;

        $order = isset($parms['order']) ? alphanum(strtolower($parms['order'])) : null;
        $orderBy = Definition::crackOrder($parms, null);

        $siteLanguage = Site::getLanguage()['id'];
		$languageFlagCondition = ($siteLanguage == LANGUAGE_ALL) ? '<=' : '=';

        $options['count']                   = $count;
        $options['start']                   = $start;
        $options['languageId']              = $siteLanguage;
        $options['languageFlagCondition']   = $languageFlagCondition;
        $options['orderBy']                 = $orderBy;
        $options['order']                    = $order;

        // get USER'S and PUBLIC snippets
        $options['userId'] = Auth::check() ? Auth::id() : 0;
        $options['userIdCondition'] = '=';
        $options['releaseFlag'] = RELEASEFLAG_PUBLIC;
        $options['releaseCondition'] = '>=';
        $options['type'] = DEFTYPE_SNIPPET;

        //
        // get the snippets for the appropriate langauge
        //
        $snippets = Definition::getWithStats($options);

        // the records
        $options['snippets'] = $snippets;

        // get all the stuff for the speak and record module
        $options['showForm'] = $showForm;
        $options['showAllButton'] = true;
        $options['loadReader'] = true;
        $options['siteLanguage'] = $siteLanguage;
        $options['snippetLanguages'] = getLanguageOptions();
        $options['languageCodes'] = getSpeechLanguage($siteLanguage);
        $options['returnUrl'] = route('practice', ['locale' => app()->getLocale()]);

        // command line supercedes cookie
        $id = isset($id) ? intval($id) : intval(Cookie::get('snippetId'));
        if ($id > 0)
            $options['snippet'] = Definition::getByType(DEFTYPE_SNIPPET, $id, 'id');

        $options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;

        // get the favorite lists so the entries can be favorited
        $options['favoriteLists'] = Definition::getUserFavoriteLists();

        // not used but needed for reader
        $history = History::getArrayShort(HISTORY_TYPE_SNIPPETS, HISTORY_SUBTYPE_SPECIFIC, LESSON_TYPE_READER, 1);

        // set focus on search box
        $options['autofocus'] = true;

        //dump($options);

		return view('gen.definitions.snippets', [
		    'options' => $options,
		    'history' => $history,
		]);
    }

	//
	// This handles the search form from the index/search page
	//
    public function searchAjax(Request $request, $resultsFormat, $text = null)
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

        $view = ($resultsFormat === 'light') ? '.component-search-results-light' : '.component-search-results';

		return view(VIEWS . $view, [
			'records' => $records,
			'favoriteLists' => Definition::getUserFavoriteLists(),
		]);
	}

	//
	// This is now the main index page
	//
    public function search(Request $request, $locale, $sort = null)
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
				$records = Definition::getIndex($sort, DEFAULT_LIST_LIMIT);
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

		$scraped = null; //Spanish::isIrregular($text);
		if (false && $scraped['irregular'])
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
                $forms = empty($records['forms']) ? $records['status'] : $records['forms'];
			}
		}
        //dd($forms);
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
		    $locale = app()->getLocale();
		    $hrefShow = route('definitions.show', ['locale' => $locale, 'definition' => $record->id]);
		    $hrefEdit = route('definitions.editOrShow', ['locale' => $locale, 'definition' => $record->id]);
			$rc = "<a href='" . $hrefShow . "'>" . $record->title . ": already in dictionary (show)</a>&nbsp;<a href=$hrefEdit>(edit)</a>";
		}

		return $rc;
    }

    public function getAjax(Request $request, $locale, $text, $entryId)
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

    public function translateAjax(Request $request, $locale, $text, $entryId = null)
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

  	public function setFavoriteList(Request $request, $locale, Definition $definition, $tagFromId, $tagToId)
    {
		$record = $definition;

        $rc = ($tagToId > 0) ? 'proj.Saved to favorite list' : 'proj.Removed from favorite list';

        if (Auth::check())
        {
			$record->removeTag($tagFromId);
			$record->addTag($tagToId);
        }
        else
        {
			$rc = 'proj.Favorite not saved - you must log in';
        }

        logInfo('setFavoriteList', __($rc), ['title' => $record->title, 'id' => $record->id,
            'tagFromId' => $tagFromId, 'tagToId' => $tagToId]);

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

	public function moveFavorites(Request $request, $locale, Tag $tag, $tagToId = null)
    {
        $rc = '';
		$records = []; // make this countable so view will always work
		try
		{
			$records = $tag->definitionsUser()->get();
            foreach($records as $record)
            {
                if (empty($tagToId))
                {
                	$record->removeTag($tag->id);
                }
                else
                {
                	$record->removeTag($tag->id);
        			$record->addTag($tagToId);
                }
            }

            $rc .= '"' . $tag->name . '": ';

            if (empty($tagToId))
            {
               	$rc .= count($records) > 0 ? __('proj.All favorites removed') : __('proj.Nothing to remove');
            }
            else
            {
               	$rc .= count($records) > 0 ? __('proj.All favorites moved') : __('proj.Nothing to move');
            }
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting list'));
		}

        logInfo('moveFavorites', $rc, null, ['tag' => $tag->name, 'id' => $tag->id, 'tagToId' => $tagToId]);

		return redirect(route('definitions.listTag', ['locale' => $locale, 'tag' => $tag->id]));
    }

    public function favoritesFlashcards(Request $request, Tag $tag)
    {
        return $this->review($request, $tag, 1);
    }

    public function favoritesQuiz(Request $request, Tag $tag)
    {
        return $this->review($request, $tag, 2);
    }

    public function review(Request $request, $locale, Tag $tag, $reviewType = null)
    {
		$reviewType = intval($reviewType);
		$record = $tag;
		$qna = Definition::makeQna(Definition::getUserFavorites(['tagId' => $tag->id])); // splits text into questions and answers
		$settings = Quiz::getSettings($reviewType);

		try
		{
		    // touch it so it will move to beginning of list
		    $tag->touch();
			$tag->save();
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error updating tag'));
		}

        $count = count($qna);
        $parms = crackParms();
        $parms['route'] = crackUri(2);
        $history = History::getArray($tag->name, $tag->id, HISTORY_TYPE_FAVORITES, $parms['source'], History::getReviewTypeInt($reviewType), $count, $parms);

		return view($settings['view'], [
			'sentenceCount' => $count,
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
            'returnPath' => Site::getReturnPath(),
			'parentTitle' => $tag->name,
			'settings' => $settings,
			// History
			'history' => $history,
			'touchPath' => '/stats/update-stats',
			'random' => true,
			]);
    }

    // update favorites list usage stats
    public function stats(Request $request, Tag $tag)
    {
        $tag->countUse();
    	return redirect('/history/add-public?programId=' . $tag->id);
    }

    // update favorites list usage stats
    public function updateStats(Request $request, Definition $definition)
    {
        // check if the definition id is set in the url parameters
        $request['definition_id'] = $definition->id;
        $msg = Stat::updateStats($request);
        return $msg;
    }

    // new: review all favorites not by list
    public function favoritesReview(Request $request)
    {
        // set up the parms
        $parms = crackParms($request);
        $parms = Definition::crackOrderNEW($parms, 'stats.qna_at, stats.viewed_at, definitions.id');

        // language parms
        $siteLanguage = Site::getLanguage()['id'];
        $parms['languageId'] = $siteLanguage;
        $parms['languageFlagCondition'] = ($siteLanguage == LANGUAGE_ALL) ? '<=' : '=';
        $parms['records'] = Definition::getUserFavorites($parms);
        //todo: $parms['recordsTotal'] = Definition::getUserFavoriteCount($parms);
        $parms['historyType'] = HISTORY_TYPE_FAVORITES;
        $record = isset($parms['records'][0]) ? $parms['records'][0] : null;
        $parms['title'] = (empty($parms['tagId']) || !isset($record->tag_name)) ? 'Review All' : $record->tag_name;

        $action = isset($parms['action']) ? alpha($parms['action']) : ''; // default to blank

        //dump('favoritesReview');
        //dump($parms);

		if ($action == 'flashcards')
		    return $this->doList($parms);
		else if ($action == 'read')
            return $this->readWords($parms);
        else // show the list
            return $this->list($parms);
    }

    public function doList($parms)
    {
        if (isset($parms['dump']))
        {
            dump($parms);
        }

        if (isset($parms['action']) && Quiz::isQuiz($parms['action']))
        {
            // flashcards or multiple choice
            return $this->doReview($parms);
        }
        else
        {
            // show the list
            return $this->list($parms);
        }
    }

    public function doReview($parms)
    {
		$qna = Definition::makeQna($parms['records']); // splits text into questions and answers
		$settings = Quiz::getSettings($parms['action']);
		$title = Exercise::makeTitle($parms);
        $count = count($qna);
        $tagId = isset($parms['tagId']) ? $parms['tagId'] : 0;
        $parms['route'] = crackUri(2);
        $history = History::getArray($title, $tagId, $parms['historyType'], $parms['source'], History::getReviewType($parms['action']), $count, $parms);

        // get user's favorites lists
        $parms['favoriteLists'] = Definition::getUserFavoriteLists();

        //dump($settings);

		return view($settings['view'], [
		    'parms' => $parms,
			'sentenceCount' => $count,
			'records' => $qna,
			'canEdit' => true,
			'isMc' => true,
            'returnPath' => Site::getReturnPath(),
			'parentTitle' => 'Title Note Used',
			'settings' => $settings,
			'history' => $history,
			'touchPath' => '/stats/update-stats',
			'random' => false,
			]);
    }

    private function list($parms)
    {
		$title = Exercise::makeTitle($parms);

		return view(VIEWS . '.list', [
		    'name' => $title, // todo: get from parms in the view
			'records' => $parms['records'], // todo: get from parms in the view
			'parms' => $parms,
			'lists' => Definition::getUserFavoriteLists(),
		]);
    }

    public function readExamples(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list']);
   		$parms['records'] = Definition::getIndex(DEFINITIONS_SEARCH_EXAMPLES, $parms['count']);
        $parms['title'] = __('proj.Dictionary Examples');
        $parms['examplesOnly'] = true;

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->list($parms);
    }

    public function reviewNewest(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list']);
        $parms['records'] = Definition::getNewest($parms['count'], /* $random = */ true);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['source'] = HISTORY_SUBTYPE_EXERCISE_NEWEST;
        $parms['title'] = __('proj.:count Newest Words', ['count' => $parms['count']]);

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->doList($parms);
    }

    public function reviewRankedVerbs(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list', 'count' => DEFAULT_REVIEW_LIMIT]);
        $parms['records'] = Definition::getRankedVerbs($parms['count']);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['title'] = __('proj.:count Most Common Verbs', ['count' => $parms['count']]);

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->doList($parms);
    }

    public function reviewNewestVerbs(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list', 'count' => DEFAULT_REVIEW_LIMIT]);
		$parms['records'] = Definition::getNewestVerbs($parms['count']);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['title'] = __('proj.:count Newest Verbs', ['count' => $parms['count']]);

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->doList($parms);
    }

    public function reviewRandomWords(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list', 'count' => DEFAULT_REVIEW_LIMIT]);
		$parms['records'] = Definition::getRandomWords($parms['count']);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['title'] = __('proj.:count Random Words', ['count' => $parms['count']]);

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->doList($parms);
    }

	public function reviewRandomVerbs(Request $request)
    {
   		$parms = crackParms($request, ['action' => 'list', 'count' => DEFAULT_REVIEW_LIMIT]);
		$parms['records'] = Definition::getRandomVerbs($parms['count']);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['title'] = __('proj.:count Random Verbs', ['count' => $parms['count']]);

		return ($parms['action'] == 'read')
		    ? $this->readWords($parms)
		    : $this->doList($parms);    }

	public function snippetsFlashcards(Request $request)
    {
        return $this->reviewSnippets($request);
    }

	public function snippetsQuiz(Request $request)
    {
        return $this->reviewSnippets($request);
    }

	public function reviewDictionary(Request $request)
    {
        $parms = crackParms($request, ['count' => 20, 'order' => 'attempts-asc', 'action' => 'flashcards']);
        $parms['type'] = DEFTYPE_DICTIONARY;
        $parms['records'] = Definition::getReview($parms);
        //todo: $parms['recordsTotal'] = Definition::getReviewCount($parms);
        $parms['historyType'] = HISTORY_TYPE_DICTIONARY;
        $parms['source'] = HISTORY_SUBTYPE_EXERCISE_LEAST_USED;

		return $this->doList($parms);
    }

	public function flashcardsNewest(Request $request)
    {
        $parms = crackParms($request);
        $parms = Definition::crackOrderNEW($parms, 'id DESC');
        $parms['type'] = DEFTYPE_SNIPPET;
        $parms['untagged'] = true;
        $parms['records'] = Definition::getReview($parms);
        $parms['historyType'] = HISTORY_TYPE_SNIPPETS;

        //dump($parms);//

		return $this->doList($parms);
    }

	public function reviewSnippets(Request $request)
    {
        $parms = crackParms($request);
        $parms = Definition::crackOrderNEW($parms, 'id DESC');
        $parms['type'] = DEFTYPE_SNIPPET;
        $parms['records'] = Definition::getReview($parms);
        //todo: $parms['recordsTotal'] = Definition::getReviewCount($parms);
        $parms['historyType'] = HISTORY_TYPE_SNIPPETS;

		return $this->doList($parms);
    }

    public function getRandomWordAjax(Request $request)
    {
		$record = Definition::getRandomWord();

		return view('components.random-word', [
			'record' => $record,
			]);
	}

    public function listTag(Request $request, $locale, Tag $tag)
    {
        $parms = crackParms($request, ['tagId' => $tag->id]);

		$records = []; // make this countable so view will always work
		try
		{
			//$records = $tag->definitionsUser()->get();

			//dump($parms);
            $records = Definition::getUserFavorites($parms);
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting list'));
		}

		return view(VIEWS . '.list', [
			'records' => $records, //todo: move to $parms in the view
			'parms' => $parms,
			'tag' => $tag,
			'lists' => Definition::getUserFavoriteLists(),
		]);
    }

	public function readSnippets(Request $request)
    {
        $parms = crackParms($request, ['title' => __('proj.Practice Text'), 'return' => 'practice']);
        $parms['languageId'] = Site::getLanguage()['id'];
		$parms['languageFlagCondition'] = ($parms['languageId'] == LANGUAGE_ALL) ? '<=' : '=';
        //dump($parms);

        $title = $parms['title'];
        $return = $parms['return'];
        $count = $parms['count'];
        $parms['type'] = DEFTYPE_SNIPPET;

        $records = Definition::getWithStats($parms);

        if (count($records) > 0)
        {
            $languageFlag = $records[0]->language_flag;
        }
        else
        {
            $languageFlag = LANGUAGE_EN;
        }

        $text = [];
        $translations = [];
        $ids = [];
        foreach($records as $record)
        {
            // new way: treat each snippet separately no matter what it contains (done to keep stats and translations in sync)
            $text[] = $record->title;
            $translations[] = $record->translation_en;
            $ids[] = $record->id;
        }
        $lines = ['text' => $text, 'translations' => $translations, 'ids' => $ids];

        $options['return'] = Site::getReturnPath();
        $options['randomOrder'] = true;
        $options['touchPath'] = '/stats/update-stats';

        if (isset($orderBy))
            $options['orderBy'] = $orderBy;

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];

        $history = History::getArrayShort(HISTORY_TYPE_SNIPPETS, $parms['source'], LESSON_TYPE_READER, count($lines['text']));

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $title,
    	    'options' => $options,
			'contentType' => 'Snippet',
			'languageCodes' => getSpeechLanguage($languageFlag),
			'labels' => $labels,
			'history' => $history,
		]);
    }

    public function readList(Request $request, Tag $tag)
    {
        $siteLanguage = Site::getLanguage()['id'];
		$languageFlagCondition = ($siteLanguage == LANGUAGE_ALL) ? '>=' : '=';
		$languageFlag = LANGUAGE_EN;
        $type = DEFTYPE_DICTIONARY;

		$records = []; // make this countable so view will always work
		try
		{
			$records = $tag->definitionsUser()->get();
            if (count($records) > 0)
            {
                // get the language from the first record
                $r = $records[0];
                $languageFlag = $r->language_flag;
            }
		}
		catch (\Exception $e)
		{
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error getting list'));
		}

        $sentences = [];
        foreach($records as $record)
        {
            $text = null;

            if ($record->type_flag == DEFTYPE_DICTIONARY)
            {
                $text = $record->title;

                if (!empty($record->definition))
                {
                    // add the first definition
                    $lines = Spanish::getSentences($record->definition);
                    dd($lines);
                    if (count($lines) > 0)
                    {
                        $text .= ': ' . trim(trim($lines[0], '1.'), '.');

                        // if there is a definition, say the word again at the end of it
                        $text .= ' (' . $record->title . ')';
                    }
                }
            }
            else
            {
                $text = $record->title;
            }

            $lines = Spanish::getSentences($text);
            $sentences = array_merge($sentences, $lines);
        }

		$lines['text'] = $sentences;

	    $options['return'] = '/favorites';

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];

        $parms = crackParms();
        $history = History::getArray($tag->name, $tag->id, HISTORY_TYPE_FAVORITES, $parms['source'], LESSON_TYPE_READER, count($lines['text']), $parms);

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $tag->name,
    	    'options' => $options,
			'contentType' => 'Snippet',
			'languageCodes' => getSpeechLanguage($languageFlag),
			'labels' => $labels,
			'history' => $history,
		]);
    }

    public function readWords($parms)
    {
        $examplesOnly = isset($parms['examplesOnly']) ? $parms['examplesOnly'] : false;
        $records = $parms['records'];
        $siteLanguage = Site::getLanguage()['id'];
		$languageFlagCondition = ($siteLanguage == LANGUAGE_ALL) ? '>=' : '=';

		$words = self::formatDefinitions($records, $examplesOnly);
		$lines['text'] = $words;
		$lines['ids'] = Definition::getIds($records);

        $languageFlag = count($records) > 0 ? $records[0]->language_flag : LANGUAGE_EN;

	    $options['return'] = Site::getReturnPath();

        $options['touchPath'] = '/stats/update-stats';

        $labels = [
            'start' => Lang::get('proj.Start Reading'),
            'startBeginning' => Lang::get('proj.Start reading from the beginning'),
            'continue' => Lang::get('proj.Continue reading from line'),
            'locationDifferent' => Lang::get('proj.location form a different session'),
            'line' => Lang::choice('ui.Line', 1),
            'of' => Lang::get('ui.of'),
            'readingTime' => Lang::get('proj.Reading Time'),
        ];

        $parms['route'] = crackUri(2);

        // make the title
        $title = Exercise::makeTitle($parms);

        $history = History::getArray($title, 0, HISTORY_TYPE_DICTIONARY, $parms['source'], LESSON_TYPE_READER, count($lines['text']), $parms);

    	return view('shared.reader', [
    	    'lines' => $lines,
    	    'title' => $title,
    	    'options' => $options,
			'contentType' => 'Snippet',
			'languageCodes' => getSpeechLanguage($languageFlag),
			'labels' => $labels,
			'history' => $history,
		]);
    }

    static public function formatDefinitions($records, $examplesOnly = false)
    {
        $labelDefinition = trans_choice('proj.Definition', 1);
        $labelExamples = trans_choice('proj.Example', 2);
        $label1 = ' ' . ucfirst(trans('ui.one')) . ':';
        $label2 = ' ' . ucfirst(trans('ui.two')) . ':';
        $label3 = ' ' . ucfirst(trans('ui.three')) . ':';
        $label4 = ' ' . ucfirst(trans('ui.four')) . ':';
        $label5 = ' ' . ucfirst(trans('ui.five')) . ':';
        $count = count($records);
        $end = ' '. trans_choice('proj.End of the list of :count items.', $count);

        $lines = [];
        foreach($records as $record)
        {
            $text = '';

            if ($examplesOnly)
            {
                if (isset($record->examples))
                {
                    $word = strtolower($record->title);
                    $pre = ucfirst($word) . ': ';
                    $post = ' (' . $word . ')';

                    $parts = explode("\r\n", $record->examples);
                    if (isset($parts) && count($parts) > 0)
                    {
                        foreach($parts as $line)
                        {
                            $lines[] = $pre . formatSentence($line) . $post;
                        }
                    }
                    else
                    {
                        $lines[] = $pre . formatSentence($record->examples) . $post;
                    }
                }
            }
            else
            {
                //
                // add the word
                //
                if (Definition::isSnippetStatic($record))
                {
                    $text = ucfirst($record->title);
                }
                else
                {
                    $text = ucfirst($record->title) . '; ';
                    //no $text .= ', ' . ucfirst($record->title) . '';


                    //
                    // add the examples
                    //
                    if (isset($record->examples))
                    {
                        if (!Str::endsWith($text, '; '))
                            $text .= '; ';

                        $text .= /* '  ' . $labelExamples . ': ' . */ ucfirst($record->examples);
                    }
                    else // if no examples, read the definition
                    {
                        //
                        // add the definition
                        //
                        $text .= $labelDefinition . ':  ';
                        $d = $record->definition;//sbw
                        $d = splitSentences($record->definition);
                        $d = (is_array($d) && count($d) >= 1) ? $d[0] : $record->definition;
                        $d = str_replace('1.', $label1, $d);
                        //no $d = str_replace('2.', $label2, $d);
                        //no $d = str_replace('3.', $label3, $d);
                        //no $d = str_replace('4.', $label4, $d);
                        //no $d = str_replace('5.', $label5, $d);

                        $text .= ucfirst($d);
                    }

                    //
                    // say the word again at the end
                    //
                    //if (!Str::endsWith($text, ';'))
                    //    $text .= ';';
                    //$text .= '  ' . ucfirst($record->title) . ';';
                }

                $lines[] = $text;
            }
        }

        // $lines[] = $end; // << removed because it doesn't work in random mode

        return $lines;
    }

    public function favorites(Request $request)
    {
		// definitions favorites
		$favorites = Definition::getUserFavoriteLists();

		$favoritesCnt = 0;
		foreach ($favorites as $record)
            $favoritesCnt += count($record->definitions);

		return view(VIEWS . '.favorites', [
			'favorites' => $favorites,
			'favoritesCnt' => $favoritesCnt,
			'newest' => true,
		]);
    }

    public function favoritesRss(Request $request)
    {
		// definitions favorites
		$records = Definition::getFavoriteLists();

		return view(VIEWS . '.favoritesRss', [
			'records' => $records,
		]);
    }

    public function favoritesRssReader(Request $request, Tag $tag)
    {
		// definitions favorites
		$records = Definition::getFavoriteLists($tag->id);
        foreach($records as $record)
        {
            $qna = [];
            $index = 0;
            if (count($record->definitions) == 0) // this is the flag to get all snippets with a translation
            {
                $favorites = Definition::getReview();
                foreach($favorites as $definition)
                {
                    if (isset($definition->translation_en))
                    {
                        $qna[$index]['q'] = $definition->translation_en;
                        $qna[$index]['questionLanguage'] = LANGUAGE_EN;

                        $qna[$index]['a'] = $definition->title;
                        $qna[$index]['answerLanguage'] = LANGUAGE_ES;

                        $index++;
                    }
                }

            }
            else
            {
                foreach($record->definitions as $definition)
                {
                    if (isset($definition->translation_en))
                    {
                        $qna[$index]['q'] = $definition->translation_en;
                        $qna[$index]['questionLanguage'] = LANGUAGE_EN;

                        $qna[$index]['a'] = $definition->title;
                        $qna[$index]['answerLanguage'] = LANGUAGE_ES;

                        $index++;
                    }
                }
            }
        }

        $record['qna'] = $qna;

		return view(VIEWS . '.favoritesRssReader', [
			'records' => $records,
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

	public function convertTextToFavorites(Request $request, $locale, Entry $entry)
    {
        $record = $entry;
        $parms = null;
        $records = null;

        //
        // split text into text and translations
        //
        try
        {
            $records = Quiz::makeFlashcards($record->description, $record->description_translation);
            $parms['translation'] = $records;
        }
        catch (\Exception $e)
        {
            $msg = 'Error making flashcards';
            logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
            return back();
        }

   		if ($request->isMethod('post'))
        {
            // do the conversion
            $parms = $this->doConvertTextToFavorites($request, alphanum($request->title), $records);
           	return redirect(route('definitions.listTag', ['locale' => $locale, 'tag' => $parms['tagId']]) . '?order=desc');
        }

		return view(VIEWS . '.convert-text-to-favorites', [
			'record' => $record,
			'parms' => $parms,
		]);
    }

    private function doConvertTextToFavorites($request, $title, $records)
    {
        if (!empty($records))
        {
            // create the favorites list tag
            $name = alphanum($title);
            $tag = Tag::createUserFavoriteList($name);

            if (!empty($tag))
            {
                // add the snippets to the favorites list
                foreach($records as $r)
                {
                    if (isset($r['choices']))
                    {
                        // then translation should also be set
                        if (!isset($r['translation_en']))
                        {
                            dd('stopping: translation not set');
                        }
                    }

                    $title = $r['q'];
                    $translation = !empty($r['translation_en']) ? $r['translation_en'] : $r['a'];
                    $choices = isset($r['choices']) ? $r['choices'] : null;
                    $exists = isset($r['exists']);
                    if ($exists)
                    {
                        $definition = Definition::getById($r['exists'][0]);
                        if (isset($definition))
                        {
                            $definition->translation_en = $translation;
                            $definition->notes = $choices;
                            //dd($definition->title);
                            try
                            {
                                $definition->save();
                            }
                            catch (\Exception $e)
                            {
                                $msg = $e->getMessage();
                                dd('Error updating existing definition ' . $exists . ': ' . $msg);
                            }
                        }
                        else
                        {
                            dd('definition that EXISTS - NOT FOUND');
                        }
                    }
                    else
                    {
                        //dd('about to create new record');
                        $definition = Definition::addDefinition([
                            'title' => $title,
                            'translation_en' => $translation,
                            'notes' => $choices,
                            'language_flag' => $request->language_flag,
                            ]);
                    }

                    if (!empty($definition))
                    {
                        $definition->addTag($tag->id);
                    }
                }
            }
        }

        return ['tagId' => $tag->id];
    }

	public function convertQuestionsToSnippets(Request $request, $locale, Entry $entry)
    {
        $record = $entry;
        $parms = null;
        $records = null;

        //
        // split text into text and translations
        //
        try
        {
            $records = Quiz::makeQnaFromText($record->description, $record->description_translation);
        }
        catch (\Exception $e)
        {
            $msg = 'Error making flashcards';
            //$m = $e->getMessage();
            //dd($m);
            logException(__FUNCTION__, $e->getMessage(), $msg, ['id' => $record->id]);
            return back();
        }

        foreach($records as $index => $rec)
        {
            $answer = explode(' ', $rec['a']);
            $answerIndex = null;
            $space = 0;
            $words = explode(' ', $rec['q']);
            foreach($words as $wordIx => $word)
            {
                if (Str::startsWith($word, '_'))
                {
                    $words[$wordIx] = ($rec['a'] === '(nada)') ? '' : $rec['a'];
                    $answerIndex = $wordIx + 1;
                    break;
                }
            }

            // put in the location
            $choices = $answerIndex;
            if (count($answer) > 1)
                $choices .= '-' . ($answerIndex + count($answer) - 1);
            $choices .= '|';

            // put in the choices
            $choices .= str_replace('|', ', ', $rec['choices']);
            $choices .= '|';

            // put in the right answer
            $choices .= $rec['a'];
            $records[$index]['choices'] = $choices;

            // put in the question with embedded choices removed
            $question = implode(' ', $words);
            $question = str_replace('  ', ' ', $question); // remove double spaces left from skipping '(nada)'.
            $records[$index]['q'] = $question;
            //dump($choices);

            //
            // now, check if the snippet already exists
            //
            $snippet = Definition::searchSnippets($question, ['exact' => true]);
            $count = count($snippet);
            if ($count > 0)
            {
                foreach($snippet as $s)
                {
                    $records[$index]['exists'][] = $s->id;
                }
            }
        }

   		if ($request->isMethod('post'))
        {
            // do the conversion
            $parms = $this->doConvertTextToFavorites($request, alphanum($request->title), $records);
           	return redirect(route('definitions.listTag', ['locale' => $locale, 'tag' => $parms['tagId']]) . '?order=desc');
        }

        $parms['translation'] = $records;

		return view(VIEWS . '.convert-questions-to-snippets', [
			'record' => $record,
			'parms' => $parms,
		]);
    }

}
