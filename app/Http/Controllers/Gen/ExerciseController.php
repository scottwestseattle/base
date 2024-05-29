<?php

namespace App\Http\Controllers\Gen;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use Config;
use Log;

use App\DateTimeEx;
use App\Gen\Definition;
use App\Gen\Exercise;
use App\Site;
use App\Status;
use App\User;

define('PREFIX', '/exercises/');
define('SHOW', '/exercises/show/');
define('VIEWS', 'gen.exercises');
define('LOG_CLASS', 'ExerciseController');

class ExerciseController extends Controller
{
	private $redirectTo = null;

	public function __construct()
	{
        $this->redirectTo = route('exercises', ['locale' => app()->getLocale()]);

        $this->middleware('admin')->except([
            'choose', 'set'
        ]);

        $this->middleware('auth')->only([
            'choose', 'set'
        ]);

		parent::__construct();
	}

    public function admin(Request $request, $locale)
    {
		$records = [];

		try
		{
			$records = Exercise::select()
			    ->where('language_flag', getLanguageId())
				->orderBy('id', 'DESC')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function index(Request $request, $locale)
    {
		$records = [];

		try
		{
			$records = Exercise::select()
			    ->where('language_flag', getLanguageId())
				->orderByRaw('user_id, type_flag, subtype_flag')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add(Request $request, $locale)
    {
		return view(VIEWS . '.add', [
			]);
	}

    public function create(Request $request, $locale)
    {
		$record = new Exercise();

        // set by user
		$record->title              = trimNull(alphanum($request->title));
		$record->url                = trimNull($request->url);
		$record->type_flag          = intval($request->type_flag);
		$record->subtype_flag       = intval($request->subtype_flag);
		$record->action_flag        = intval($request->action_flag);

        // set by system
		$record->user_id 		    = Auth::id();
		$record->template_flag      = 1; // template created by admin
        $record->level_flag         = LEVEL_C1; // not used yet
		$record->frequency_period   = FREQUENCY_DAILY;
		$record->frequency_reps     = DEFAULT_REVIEW_LIMIT;
		$record->language_flag      = getLanguageId();

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('base.New record has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'));
			return back();
		}

		return redirect($this->redirectTo);
    }

	public function edit(Request $request, $locale, Exercise $exercise)
    {
		$record = $exercise;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languageOptions' => getLanguageOptions(),
			]);
    }

    public function update(Request $request, $locale, Exercise $exercise)
    {
		$record = $exercise;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->url = copyDirty($record->url, $request->url, $isDirty, $changes);
        $record->type_flag = copyDirty($record->type_flag, $request->type_flag, $isDirty, $changes);
        $record->subtype_flag = copyDirty($record->subtype_flag, $request->subtype_flag, $isDirty, $changes);
        $record->action_flag = copyDirty($record->action_flag, $request->action_flag, $isDirty, $changes);
        $record->language_flag = copyDirty($record->language_flag, $request->language_flag, $isDirty, $changes);
        //$record->template_flag = copyDirty($record->template_flag, isAdmin() ? 1 : 0, $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo(LOG_CLASS, __('base.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo(LOG_CLASS, __('base.No changes made'), ['record_id' => $record->id]);
		}

		return redirect($this->redirectTo);
	}

    public function choose(Request $request, $locale)
    {
        $enabled = Exercise::isEnabled();
        if ($enabled)
        {
            $parms['templates'] = Exercise::getTemplateList();
            $parms['favorites'] = Definition::getUserFavoriteLists();
            $parms['lessons'] = null; //Lesson::getLessons($user->level());
            //dump($parms);

            // create a lookup table so we can check the current selected templates items
            $ids = null;
            $exercises = Exercise::getUserList();
            foreach($exercises as $record)
            {
                if ($record->active_flag)
                {
                    if ($record->template_id > 0) // it's from a template
                    {
                        $ids['template_' . $record->template_id] = $record->template_id;
                    }
                    else // it's a specific exercise like a favorites list or a lesson exercise
                    {
                        $ids['favorite_' . $record->program_id] = $record->program_id;
                    }
                }
            }

            $parms['activeIds'] = $ids;
            //dump($parms);
        }

        $parms['enabled'] = $enabled;

		return view(VIEWS . '.choose', ['parms' => $parms]);
	}

    public function set(Request $request, $locale)
    {
        $record = null;

        //
        // disactivate all user records because we don't what has been unchecked
        //
        Exercise::where('user_id', Auth::id())->update(['active_flag' => false]);

        //
        // now set each one that's checked by looping through the list of possible checkbox request variable names
        //
        $exercises = Exercise::getTemplateList();

        //
        // loop through each possible exercise template and see if it has been checked
        //
        foreach($exercises as $record)
        {
            $key = 'template_' . $record->id;
            if (isset($request[$key]))
            {
                // it was checked so make it active it if it exists or add it if it doesn't
                Exercise::set($record->id);
            }
        }

        //
        // loop through each possible favorites list and see if it has been checked
        //
        $favorites = Definition::getUserFavorites();
        foreach($favorites as $record)
        {
            $key = 'favorites_' . $record->tag_id;
            if (isset($request[$key]))
            {
                // it was checked so make it active it if it exists or add it if it doesn't
                Exercise::set($record->tag_id, $record->tag_name, HISTORY_TYPE_FAVORITES);
            }
        }
		return redirect('/');
    }

	public function view(Request $request, $locale, Exercise $exercise)
    {
		$record = $exercise;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

    public function confirmDelete(Request $request, $locale, Exercise $exercise)
    {
		$record = $exercise;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, $locale, Exercise $exercise)
    {
		$record = $exercise;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $locale, $id)
    {
		$id = intval($id);

		try
		{
			$record = Exercise::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted(Request $request, $locale)
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Exercise::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }
}
