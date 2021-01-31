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
use App\Status;
use App\Tools;
use App\User;

define('PREFIX', 'definitions');
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
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error getting record list'));
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
		$record = new Definition();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->description	= trimNull($request->description);
        $record->permalink      = createPermalink($record->title);

		try
		{
			$record->save();

			logInfo(LOG_CLASS, __('msgs.New record has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error adding new record'));
			return back();
		}

		return redirect($this->redirectTo . '/view/' . $record->id);
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
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Record not found'), ['permalink' => $permalink]);
    		return redirect($this->redirectTo);
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Definition $definition)
    {
		$record = $definition;

		return view(VIEWS . '.edit', [
			'record' => $record,
			]);
    }

    public function update(Request $request, Definition $definition)
    {
		$record = $definition;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
        $record->permalink = copyDirty($record->permalink, createPermalink($request->title, $record->created_at), $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo(LOG_CLASS, __('msgs.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException(LOG_CLASS, $e->getMessage(), __('msgs.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo(LOG_CLASS, __('msgs.No changes made'), ['record_id' => $record->id]);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
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
		$record = $definition;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('msgs.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $id)
    {
		$id = intval($id);

		try
		{
			$record = Definition::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('msgs.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error undeleting record'), ['record_id' => $record->id]);
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
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error getting deleted records'));
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
			logInfo(LOG_CLASS, __('msgs.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function createSnippet(Request $request)
    {
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
			logInfo($msg, $msg, ['title' => $record->title, 'id' => $record->id]);

    		return redirect($request->returnUrl);
		}
		catch (\Exception $e)
		{
		    //dump($record);
            //dd($e->getMessage());
			$msg = isset($msg) ? $msg : "Error adding new $tag";
			logException(__FUNCTION__, $e->getMessage(), $msg);
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
            $options['snippet'] = Definition::get(DEFTYPE_SNIPPET, $snippetId, 'id');
        }

        $options['language'] = isset($options['snippet']) ? $options['snippet']->language_flag : $siteLanguage;

		return view('gen.definitions.snippets', [
		    'options' => $options,
		]);
    }
}
