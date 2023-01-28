<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use DB;
use App\DateTimeEx;
use App\Status;
use App\Site;

class Exercise extends Model
{
	use SoftDeletes;

	const _typeFlags = [
        HISTORY_TYPE_ARTICLE            => 'Article',
        HISTORY_TYPE_BOOK               => 'Book',
        HISTORY_TYPE_DICTIONARY         => 'Dictionary Words',
        HISTORY_TYPE_DICTIONARY_VERBS   => 'Dictionary Verbs',
        HISTORY_TYPE_SNIPPETS           => 'Practice Text',
        HISTORY_TYPE_FAVORITES          => 'Favorites List',
        HISTORY_TYPE_LESSON             => 'Lesson Exercise',
	];

	const _subTypeFlags = [
        HISTORY_SUBTYPE_OTD            => 'Of The Day',
        HISTORY_SUBTYPE_RANDOM         => 'Random',
        HISTORY_SUBTYPE_NEWEST         => 'Newest',
        HISTORY_SUBTYPE_LEAST_USED     => 'Least Used',
        HISTORY_SUBTYPE_MOST_COMMON    => 'Most Common',
        HISTORY_SUBTYPE_SPECIFIC       => 'Specific',
        HISTORY_SUBTYPE_EXERCISE_OTD            => 'Of The Day',
        HISTORY_SUBTYPE_EXERCISE_RANDOM         => 'Random',
        HISTORY_SUBTYPE_EXERCISE_NEWEST         => 'Newest',
        HISTORY_SUBTYPE_EXERCISE_LEAST_USED     => 'Least Used',
        HISTORY_SUBTYPE_EXERCISE_MOST_COMMON    => 'Most Common',
        HISTORY_SUBTYPE_EXERCISE_SPECIFIC       => 'Specific',
	];

	const _actionFlags = [
        LESSON_TYPE_READER          => 'Read',
        LESSON_TYPE_QUIZ_FLASHCARDS => 'Flashcards',
        LESSON_TYPE_OTHER           => 'Inherit',
	];

    static public function makeTitle($parms)
    {
        $title = isset($parms['title']) ? $parms['title'] : null;

        if (empty($title))
        {
            $typeFlag = isset($parms['historyType']) ? $parms['historyType'] : null;
            $subTypeFlag = isset($parms['source']) ? $parms['source'] : null;

            if (isset($typeFlag) && isset($subTypeFlag))
            {
                $title = self::_typeFlags[$typeFlag];
                if ($subTypeFlag == HISTORY_SUBTYPE_SPECIFIC ||
                    $subTypeFlag == HISTORY_SUBTYPE_EXERCISE_SPECIFIC)
                {
                    // don't show this because it was just a general query
                }
                else
                {
                    // show type of query suchs as 'Random', 'Newest', 'Least Used'
                    $title .= ' - ' . self::_subTypeFlags[$subTypeFlag];
                }
            }
            else
            {
                $title = 'Title - ';
                if (empty($typeFlag))
                    $title .= '(no type)';
                if (empty($subTypeFlag))
                    $title .= '(no subtype)';
            }
        }

        return $title;
    }

    static public function getTypes()
    {
        return self::_typeFlags;
    }

    static public function getSubtypes()
    {
        return self::_subTypeFlags;
    }

    static public function getActions()
    {
        return self::_actionFlags;
    }

    static public function getTypeFlagName($typeFlag)
    {
        return self::_typeFlags[$typeFlag];
    }

    static public function getSubTypeFlagName($subTypeFlag)
    {
        return self::_subTypeFlags[$subTypeFlag];
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function getByType($type)
    {
        $type = intval($type);

        try
        {
            $records = Exercise::select()
                ->where('type_flag', $type)
                ->get();
        }
        catch (\Exception $e)
        {
            logException(LOG_CLASS, $e->getMessage(), __('base.Error setting exercise'));
        }

        return $records;
    }


    static public function getTemplateList()
    {
        try
        {
            $records = Exercise::select()
   				->where('template_flag', true)
   				->orderByRaw('type_flag, subtype_flag')
                ->get();

            //dd($records);
        }
        catch (\Exception $e)
        {
            dd($e);
            logException(LOG_CLASS, $e->getMessage(), __('base.Error setting exercise'));
        }

        return $records;
    }

    static public function getUserList()
    {
        try
        {
            $records = Exercise::select()
   				->where('user_id', Auth::id())
   				->where('active_flag', true)
   				->orderByRaw('type_flag, subtype_flag')
                ->get();

            //dd($records);
        }
        catch (\Exception $e)
        {
            dd($e);
            logException(LOG_CLASS, $e->getMessage(), __('base.Error setting exercise'));
        }

        return $records;
    }

    static public function set($id, $title = null, $type_flag = null)
    {
        $id = intval($id);

        try
        {
            $record = null;

            //
            // get the user's copy of this record and activate it
            //
            switch($type_flag)
            {
                case HISTORY_TYPE_FAVORITES:
                    $record = Exercise::select()
                        ->where('program_id', $id)
                        ->where('type_flag', $type_flag)
                        ->first();
                    break;
                case HISTORY_TYPE_LESSON:
                    $record = Exercise::select()
                        ->where('program_id', $id)
                        ->first();
                    break;
                default:
                    $record = Exercise::select()
                        ->where('template_id', $id)
                        ->first();
            }


            if (isset($record))
            {
                // it already exists so reactivate it
                $record->active_flag = true;
                $record->save();
            }
            else
            {
                //
                // doesn't exists so create it
                //
                if (isset($type_flag))
                {
                    //
                    // this means it's a specific favorites list or lesson exercise
                    //
                    $new = new Exercise();
                    $new->title = $title;
                    $new->user_id = Auth::id();
                    $new->type_flag = $type_flag;
                    $new->subtype_flag = HISTORY_SUBTYPE_EXERCISE_SPECIFIC;
                    $new->program_id = $id;
                    $new->template_flag = false;

                    // all favorites lists are flashcards / lesson exercises actions are inherited
                    $new->action_flag = ($type_flag === HISTORY_TYPE_LESSON) ? LESSON_TYPE_OTHER : LESSON_TYPE_QUIZ_FLASHCARDS;

                    $new->save();
                }
                else
                {
                    //
                    // this means it's from an exercise template, so make the user his own copy of the template record
                    //
                    $record = Exercise::find($id);
                    $new = $record->replicate();
                    $new->created_at = DateTimeEx::now();
                    $new->template_flag = false;
                    $new->user_id = Auth::id();
                    $new->template_id = $id;
                    $new->save();
                }
            }
        }
        catch (\Exception $e)
        {
            logException(LOG_CLASS, $e->getMessage(), __('base.Error setting exercise'));
        }
    }

    static public function getDailyExercises()
    {
        $parms = [];

        //
        // get the todo list of daily exercises
        //
        // TODO: FIX because it's all hardcoded
        $languageFlag = getLanguageId();

        if ($languageFlag == LANGUAGE_ES && !isAdmin() && Auth::check() && Site::hasOption('fpTodo'))
        {
            $iconText = 'file-text';
            $iconFlashcards = 'lightning';
            $iconCourses = 'book';
            $count = 20;
            $iconDone = 'check-circle';
            $icon = '';
            $doneCount = 0;
            $todo = [];
            $title = 'Lesson Name Not Set';
            $titleSnippets = __('proj.Practice Text');
            $titleDictionary = __('proj.Dictionary');
            $titleArticle = trans_choice('proj.Article', 1);
            $titleFavorites = trans_choice('proj.Favorites List', 1);

            // get history so we can see what has been done today
            $histories = History::getToday();

            // get the list of daily exercises for the user
            $exercises = Exercise::getUserList();

            $loops = 0;
            foreach($exercises as $exercise)
            {
                $done = false;
                $id = 0;
                $name = 'not set';
                foreach($histories as $history)
                {
                    $loops++;

                    if ($history->type_flag == $exercise->type_flag
                        && $history->subtype_flag == $exercise->subtype_flag
                        && ($history->action_flag == $exercise->action_flag || $exercise->action_flag == LESSON_TYPE_OTHER)
                        )
                    {
                        switch($exercise->type_flag)
                        {
                            case HISTORY_TYPE_ARTICLE:
                                $done = true;
                                $id = $history->program_id;
                                $name = $history->program_name;
                                $doneCount++;
                                break;
                            case HISTORY_TYPE_LESSON:
                                if ($exercise->subtype_flag == HISTORY_SUBTYPE_EXERCISE_SPECIFIC)
                                {
                                    // it was a specific lesson exercise, so check the id
                                    if ($history->program_id == $exercise->program_id)
                                    {
                                        $id = $history->program_id;
                                        $name = $history->program_name;
                                        $done = true;
                                        $doneCount++;
                                    }
                                }
                                else
                                {
                                    // not a specific lesson exercise
                                    $done = true;
                                    $doneCount++;
                                }
                                break;
                            case HISTORY_TYPE_FAVORITES:
                                if ($history->program_id == $exercise->program_id)
                                {
                                    $id = $history->program_id;
                                    $name = $history->program_name;
                                    $done = true;
                                    $doneCount++;
                                }
                                break;
                            default:
                                $done = true;
                                $doneCount++;
                                break;
                        }

                    }

                    if ($done)
                        break;
                }

                if ($exercise->type_flag == HISTORY_TYPE_ARTICLE)
                {
                    $article = null;
                    if ($done)
                    {
                        // we've already got the id and name from the history record
                        $icon = $iconDone;
                    }
                    else
                    {
                        $icon = $iconText;
                        if ($exercise->subtype_flag == HISTORY_SUBTYPE_EXERCISE_RANDOM)
                        {
                            $article = Article::getRandom();
                        }
                        else if ($exercise->subtype_flag == HISTORY_SUBTYPE_EXERCISE_OTD)
                        {
                            $article = Article::getRandom();
                        }

                        if (isset($article))
                        {
                            $name = $article->title;
                            $id = $article->id;
                        }
                    }

                    $todo[] = ['done' => $done, 'title' => $exercise->title, 'icon' => $icon, 'linkTitle' => $name,
                        'linkUrl' => '/articles/read/' . $id . "?source=$exercise->subtype_flag" ];
                }
                else if ($exercise->type_flag == HISTORY_TYPE_LESSON) // lesson exercise
                {
                    $icon = $done ? $iconDone : $iconCourses;

                    $record = Lesson::getByHistorySubType($exercise->subtype_flag);

                    $title = 'Lesson Title Not Set';
                    $courseTitle = 'Course Title Not Set';
                    if (isset($record))
                    {
                        $title = $record->title;
                        $action = ($record->isFlashcards()) ? 1 : 2;

                        if (isset($record))
                        {
                            if (isset($record->course))
                                $courseTitle = $record->course->title;

                            $title = $courseTitle . ': ' . $title;
                        }

                        $url = "/lessons/review/$record->id/$action/20?source=$exercise->subtype_flag";
                        $todo[] = ['done' => $done, 'title' => $exercise->title, 'icon' => $icon, 'linkTitle' => $title, 'linkUrl' => $url];
                    }
                }
                elseif ($exercise->type_flag == HISTORY_TYPE_SNIPPETS)
                {
                    $icon = $done ? $iconDone : $iconFlashcards;

                    switch($exercise->subtype_flag)
                    {
                        case HISTORY_SUBTYPE_EXERCISE_OTD:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_RANDOM:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_NEWEST:
                            $todo[] = ['done' => $done, 'title' => $titleSnippets, 'icon' => $icon, 'linkTitle' => $exercise->title, 'linkUrl' => "/daily/flashcards-newest?action=flashcards&count=$count&order=desc&source=$exercise->subtype_flag"];
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_LEAST_USED:
                            $todo[] = ['done' => $done, 'title' => $titleSnippets, 'icon' => $icon, 'linkTitle' => $exercise->title, 'linkUrl' => "/daily/flashcards-attempts?action=flashcards&count=$count&order=attempts-asc&source=$exercise->subtype_flag"];
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_MOST_COMMON:
                            break;
                        default:
                            break;
                    }
                }
                elseif ($exercise->type_flag == HISTORY_TYPE_DICTIONARY)
                {
                    $icon = $done ? $iconDone : $iconFlashcards;
                    switch($exercise->subtype_flag)
                    {
                        case HISTORY_SUBTYPE_EXERCISE_OTD:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_RANDOM:
                            $todo[] = ['done' => $done, 'title' => $titleDictionary, 'icon' => $icon, 'linkTitle' => $exercise->title, 'linkUrl' => "/definitions/review-random-words?action=flashcards&count=$count"];
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_NEWEST:
                            $todo[] = ['done' => $done, 'title' => $titleDictionary, 'icon' => $icon, 'linkTitle' => $exercise->title, 'linkUrl' => "/daily/dictionary-newest?action=flashcards&count=$count"];
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_LEAST_USED:
                            $todo[] = ['done' => $done, 'title' => $titleDictionary, 'icon' => $icon, 'linkTitle' => $exercise->title, 'linkUrl' => "/daily/dictionary-attempts"];
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_MOST_COMMON:
                            break;
                        default:
                            break;
                    }
               }
                elseif ($exercise->type_flag == HISTORY_TYPE_DICTIONARY_VERBS)
                {
                    $icon = $done ? $iconDone : $iconFlashcards;

                    switch($exercise->subtype_flag)
                    {
                        case HISTORY_SUBTYPE_EXERCISE_OTD:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_RANDOM:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_NEWEST:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_LEAST_USED:
                            break;
                        case HISTORY_SUBTYPE_EXERCISE_MOST_COMMON:
                            break;
                        default:
                            break;
                    }
                }
                elseif ($exercise->type_flag == HISTORY_TYPE_FAVORITES)
                {
                    $icon = $done ? $iconDone : $iconFlashcards;

                    $todo[] = ['done' => $done, 'title' => $titleFavorites, 'icon' => $icon, 'linkTitle' => $exercise->title,
                        'linkUrl' => "/definitions/favorites-review?tagId=$exercise->program_id&action=flashcards&count=20&order=attempts-asc&source=$exercise->subtype_flag"];
                }
            }

            $parms['doneCount'] = $doneCount;
            $parms['records'] = $todo;
            $parms['loops'] = $loops;
        }

        return $parms;
    }
}
