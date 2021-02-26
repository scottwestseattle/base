<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use DB;
use Auth;

use App\Gen\Definition;
use App\Entry;
use App\User;

class Definition extends Model
{
	use SoftDeletes;

	//////////////////////////////////////////////////////////////////////
	//
	// Relationships
	//
	//////////////////////////////////////////////////////////////////////

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function entries()
    {
		return $this->belongsToMany('App\Entry');
    }

	public function removeEntries()
	{
		foreach($this->entries as $entry)
		{
			$this->entries()->detach($entry->id);
		}
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Tags - User Definition Favorite lists
	//
	//////////////////////////////////////////////////////////////////////

    public function tags()
    {
		return $this->belongsToMany('App\Tag')->wherePivot('user_id', Auth::id());
    }

    public function addTagFavorite()
    {
		$tag = false;
		$name = 'Favorites';

		if (Auth::check())
		{
			$tag = Tag::getOrCreate($name, TAG_TYPE_DEF_FAVORITE, Auth::id());
			if (isset($tag))
			{
				$this->tags()->detach($tag->id); // if it's already tagged, remove it so it will by updated
				$this->tags()->attach($tag->id, ['user_id' => Auth::id()]);
				$this->refresh();
				//dump($tag);
				//dd($this->tags);
			}
		}

		return $tag;
    }

    public function removeTagFavorite()
    {
		$rc = false;

		if (Auth::check())
		{
			$name = 'Favorites';
			$tag = Tag::get($name, TAG_TYPE_DEF_FAVORITE, Auth::id());
			if (isset($tag))
			{
				$this->tags()->detach($tag->id);
				$rc = true;
			}
		}

		return $rc;
    }

    public function addTag($tagId)
    {
		if (Auth::check())
		{
			// 0 is okay so we can use the same flow when removing a tag
			if ($tagId > 0)
			{
				$this->tags()->detach($tagId); // if it's already tagged, remove it so it will by updated
				$this->tags()->attach($tagId, ['user_id' => Auth::id()]);
			}
		}
    }

    public function removeTags()
    {
		foreach($this->tags as $tag)
		{
			$this->removeTag($tag->id);
		}
	}

    public function removeTag($tagId)
    {
		if (Auth::check())
		{
			// 0 is okay so we can use the same flow for adding first tag
			if ($tagId > 0)
				$this->tags()->detach($tagId);
		}
    }

    static public function getUserFavoriteLists()
    {
		$records = null;

		try
		{
			$records = DB::table('tags')
				->leftJoin('definition_tag', function($join) {
					$join->on('definition_tag.tag_id', '=', 'tags.id');
					$join->where('definition_tag.user_id', Auth::id());
				})
				->select(DB::raw('tags.id, tags.name, tags.user_id, count(definition_tag.tag_id) as wc'))
				->where('tags.deleted_at', null)
				->where('tags.user_id', Auth::id())
				->where('type_flag', TAG_TYPE_DEF_FAVORITE)
				->groupBy('tags.id', 'tags.name', 'tags.user_id')
				->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting favorite lists';
            logExceptionEx(__CLASS__, __FUNCTION__, $msg . ', ' . $e->getMessage());
		}

		return $records;
    }

    static public function getUserFavoriteListsOptions()
    {
		$options = [];

		$records = self::getUserFavoriteLists();
		if (isset($records))
		{
			foreach($records as $record)
			{
				$options[$record->id] = $record->name;
			}
		}

		return $options;
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Release status
	//
	//////////////////////////////////////////////////////////////////////

    public function isFinished()
    {
		return ($this->wip_flag >= getConstant('wip_flag.finished'));
    }

    public function isPublic()
    {
		return ($this->release_flag >= getConstant('release_flag.public'));
    }

    public function getStatus()
    {
		return ($this->release_flag);
    }

    public function toggleWip()
    {
        if ($this->isFinished())
            $this->wip_flag = WIP_DEFAULT;
        else
            $this->wip_flag = WIP_FINISHED;

            $this->save();

		return $this->isFinished();
    }

	//////////////////////////////////////////////////////////////////////
	//
	// General Access
	//
	//////////////////////////////////////////////////////////////////////

	static public function getByType($type, $value, $column = 'id')
	{
		$record = null;

		try
		{
			$record = Definition::select()
				->where($column, $value)
				->where('type_flag', $type)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting record';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['value' => $value]);
		}

		return $record;
	}

    static public function get($word)
    {
		$word = alphanum($word, /* strict = */ true);
		$record = null;

		try
		{
			$record = Definition::select()
				//->whereRaw("`title` = '$word' collate utf8mb4_bin") // to distinguish between accent chars
        		->where('type_flag', DEFTYPE_DICTIONARY)
				->where('title', $word)
				->where('deleted_at', null)
				->first();

			//dd($record);
		}
		catch (\Exception $e)
		{
			//dd($e);
			$msg = 'Error getting word: ' . $word;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['word' => $word]);
		}

		return $record;
	}

    static public function exists($word)
    {
		$word = alphanum($word, /* strict = */ true);
		$rc = 0;

		try
		{
			$rc = Definition::select()
        		->where('type_flag', DEFTYPE_DICTIONARY)
				->where('title', $word)
				->where('deleted_at', null)
				->count();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting word: ' . $word;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['word' => $word]);
		}

		return $rc > 0;
	}

	static public function getNewest($limit)
	{
		return self::getIndex(DEFINITIONS_SEARCH_NEWEST, $limit);
	}

	static public function getNewestVerbs($limit)
	{
		return self::getIndex(DEFINITIONS_SEARCH_NEWEST_VERBS, $limit);
	}

	static public function getRanked($limit)
	{
		return self::getIndex(DEFINITIONS_SEARCH_RANKED, $limit);
	}

	static public function getRankedVerbs($limit)
	{
		return self::getIndex(DEFINITIONS_SEARCH_RANKED_VERBS, $limit);
	}

	static public function getRandomWords($limit)
	{
		$records = self::getIndex(DEFINITIONS_SEARCH_RANDOM_WORDS);

		// get random indexes
		$random = self::getRandomIndexes(20, count($records));
		$recs = [];

		// copy words using random indexes
		foreach($random as $a)
			$recs[] = $records[$a];

		// return the random words
		return $recs;
	}

	static public function getRandomVerbs($limit)
	{
		$records = self::getIndex(DEFINITIONS_SEARCH_RANDOM_VERBS);

		// get random indexes
		$random = self::getRandomIndexes(20, count($records));
		$recs = [];

		// copy words using random indexes
		foreach($random as $a)
			$recs[] = $records[$a];

		// return the random words
		return $recs;
	}

    static public function getRandomIndexes($count, $range)
    {
		$rc = [];
		for ($i = 0; $i < $count; $i++)
		{
			$rnd = rand(0, $range - 1);  // using the 0-based index
			if (in_array($rnd, $rc))
			{
				// find the next open spot
				for ($j = 0; $j < $count; $j++)
				{
					$rnd++;
					if ($rnd >= $range)
						$rnd = 0; // roll over

					if (!in_array($rnd, $rc))
					{
						// stop looping and it will be added below
						break;
					}
				}
			}

			// add it
			$rc[] = $rnd; // use counts so we can easily see if the logic is bad
		}

		return $rc;
	}

    static public function getIndex($sort = null, $limit = PHP_INT_MAX)
	{
		$sort = intval($sort);
		$limit = intval($limit);
		$records = [];
		$orderBy = 'title';
		$verbs = false;
		switch($sort)
		{
			case DEFINITIONS_SEARCH_REVERSE:
				$orderBy = 'title desc';
				break;
			case DEFINITIONS_SEARCH_NEWEST:
				$orderBy = 'id desc';
				break;
			case DEFINITIONS_SEARCH_RECENT:
				$orderBy = 'updated_at desc';
				break;
			case DEFINITIONS_SEARCH_NEWEST_VERBS:
				$orderBy = 'id desc';
				$verbs = true;
				break;
			case DEFINITIONS_SEARCH_RANDOM_VERBS:
				$verbs = true;
				break;
			case DEFINITIONS_SEARCH_RANKED:
                $orderBy = '`rank`';
				break;
			case DEFINITIONS_SEARCH_RANKED_VERBS:
                $orderBy = '`rank`';
				$verbs = true;
				break;
			case DEFINITIONS_SEARCH_VERBS:
				$limit = PHP_INT_MAX;
				$verbs = true;
				break;
			case DEFINITIONS_SEARCH_ALL:
			case DEFINITIONS_SEARCH_MISSING_TRANSLATION:
			case DEFINITIONS_SEARCH_MISSING_DEFINITION:
			case DEFINITIONS_SEARCH_MISSING_CONJUGATION:
			case DEFINITIONS_SEARCH_WIP_NOTFINISHED:
				$limit = PHP_INT_MAX;
				break;
			default:
				break;
		}

		try
		{
			if ($verbs)
			{
			    $rankCondition = '>=';
			    $rankValue = 0;
    			if ($sort == DEFINITIONS_SEARCH_RANKED_VERBS)
    			    $rankCondition = '>';

				$records = Definition::select()
					->whereNull('deleted_at')
    			    ->where('type_flag', DEFTYPE_DICTIONARY)
					->where(function ($query) {$query
						->where('title', 'like', '%ar')
						->orWhere('title', 'like', '%er')
						->orWhere('title', 'like', '%ir')
						;})
					->whereNotNull('conjugations_search')
					->whereNotNull('conjugations')
					->where('rank', $rankCondition, $rankValue)
					->limit($limit)
					->orderByRaw($orderBy)
					->get();
			}
			else if ($sort === DEFINITIONS_SEARCH_MISSING_TRANSLATION)
			{
				$records = Definition::select()
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->whereNull('deleted_at')
					->whereNull('translation_en')
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_MISSING_DEFINITION)
			{
				$records = Definition::select()
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->whereNull('deleted_at')
					->whereNull('definition')
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_MISSING_CONJUGATION)
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->where('wip_flag', '<', WIP_FINISHED)
					->where(function ($query) {$query
						->where('title', 'like', '%ar')
						->orWhere('title', 'like', '%er')
						->orWhere('title', 'like', '%ir')
						;})
					->where(function ($query) {$query
						->whereNull('conjugations_search')
						->orWhereRaw('LENGTH(conjugations) < 50')
						;})
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_WIP_NOTFINISHED)
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->where('wip_flag', '<', WIP_FINISHED)
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_RANDOM_WORDS)
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->whereNotNull('definition')
					->whereNotNull('translation_en')
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_RANKED)
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('rank', '>', 0)
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting index';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['sort' => $sort]);
		}

		return $records;
    }

    static public function getRandomWord()
    {
		$record = null;

		try
		{
			$count = Definition::select()
				->where('deleted_at', null)
        		->where('type_flag', DEFTYPE_DICTIONARY)
				->whereNotNull('definition')
				->whereNotNull('translation_en')
				->where('wip_flag', '>=', WIP_FINISHED)
				->count();

			$rnd = rand(1, $count - 1);

			$record = Definition::select()
				->where('deleted_at', null)
        		->where('type_flag', DEFTYPE_DICTIONARY)
				->whereNotNull('definition')
				->whereNotNull('translation_en')
				->where('wip_flag', '>=', WIP_FINISHED)
				->orderBy('id')
				->skip($rnd)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting random word';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return $record;
	}

    static public function getById($id)
    {
		$id = intval($id);
		$record = null;

		try
		{
			$record = Definition::select()
				->where('id', $id)
				->where('deleted_at', null)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting word: ' . $id;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['id' => $id]);
		}

		return $record;
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Search
	//
	//////////////////////////////////////////////////////////////////////

	// search checks title and forms
    static public function searchPartial($word)
    {
		$word = alpha($word);
		$records = null;

		if (!isset($word))
		{
			// show full list
			return self::getIndex();
		}

		try
		{
			$records = Definition::select()
				->where('deleted_at', null)
    			->where('type_flag', DEFTYPE_DICTIONARY)
				->where(function ($query) use ($word){$query
					->where('title', 'LIKE', $word . '%')							// exact match of title
					->orWhere('forms', 'LIKE', '%;' . $word . ';%')					// exact match of ";word;"
					->orWhere('conjugations_search', 'LIKE', '%;' . $word . '%;%') 	// exact match of ";word;"
					;})
				->orderBy('title')
				->get();

			if (false && !isset($record)) // not yet
			{
				$record = self::searchDeeper($word);
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting word: ' . $word;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		//dd($records);

		return $records;
	}

	// search checks title and forms
    static public function searchGeneral($word)
    {
		$word = alpha($word);
		$records = null;

		if (!isset($word))
		{
			// show full list
			return self::getIndex();
		}

		try
		{
			$records = Definition::select()
				->where('deleted_at', null)
    			->where('type_flag', DEFTYPE_DICTIONARY)
				->where(function ($query) use ($word){$query
					->where('title', 'LIKE', $word . '%')
					->orWhere('forms', 'LIKE', '%' . $word . '%')
					->orWhere('conjugations_search', 'LIKE', '%' . $word . '%')
					->orWhere('translation_en', 'LIKE', '%' . $word . '%')
					;})
				->orderBy('title')
				->get();

			if (false && !isset($record)) // not yet
			{
				$record = self::searchDeeper($word);
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting word: ' . $word;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		//dd($records);

		return $records;
	}

	// search checks title and forms
    static public function search($word)
    {
		$word = alphanum($word, /* strict = */ true);
		$record = null;

		try
		{
			$record = Definition::select()
				->where('deleted_at', null)
    			->where('type_flag', DEFTYPE_DICTIONARY)
				->where(function ($query) use ($word){$query
					->where('title', $word)											// exact match of title
					->orWhere('forms', 'LIKE', '%;' . $word . ';%')					// exact match of ";word;"
					->orWhere('conjugations_search', 'LIKE', '%;' . $word . ';%') 	// exact match of ";word;"
					;})
				->first();

			if (!isset($record))
			{
				$record = self::searchDeeper($word);
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting word: ' . $word;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		//dd($record);

		return $record;
	}

	// handle special cases:
	// imperative: haz, haga, hagamos, hagan + me, melo, se, selo, nos, noslo
	// hazme, hazmelo, haznos, haznoslo
	// hagame, hágamelo, hagase, hágaselo, haganos, háganoslo
	// hagámoslo, hagámosle
	// haganme, haganmelo, haganse, haganselo
	// hacerme, hacermelo, hacernos, hacernoslo, hacerse, hacerselo
	static public function searchDeeper($word)
	{
		$wordRaw = $word;
		$record = null;

		$suffixes = [
			'me',    'te',    'se',    'nos',
			'melo',  'telo',  'selo',  'noslo',
			'mela',  'tela',  'sela',  'nosla',
			'melos', 'telos', 'selos', 'noslos',
			'melas', 'telas', 'selas', 'noslas',
		];

		$any = endsWithAnyIndex($word, $suffixes);
		if ($any !== false)
		{
			// trim off the suffix and search for the stem which should be the imperative
			$word = rtrim($word, $suffixes[$any]);
			$wordReflexive = $word . 'se';

			//dump($any . ': ' . $suffixes[$any] . ', word: ' . $word);

			// we're only looking for verbs at this point
			$record = Definition::select()
				->where('deleted_at', null)
    			->where('type_flag', DEFTYPE_DICTIONARY)
				->where(function ($query) use ($word, $wordReflexive){$query
					->where('title', $word)											// exact match of title
					->orWhere('title', $wordReflexive)								// exact match of reflexive
					->orWhere('forms', 'LIKE', '%;' . $word . ';%')					// exact match of ";word;"
					->orWhere('conjugations_search', 'LIKE', '%;' . $word . ';%') 	// exact match of ";word;"
					;})
				->first();

			if (!isset($record))
                logInfo('searchDeeper', 'text not found', ['wordRaw' => $wordRaw, 'word' => $word]);
		}

		return $record;
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Snippets
	//
	//////////////////////////////////////////////////////////////////////

	static public function getSnippet($value)
	{
		$record = null;

		try
		{
			$record = Definition::select()
				->where('examples', $value)
				->where('type_flag', DEFTYPE_SNIPPET)
				->first();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting record';
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['value' => $value]);
		}

		return $record;
	}

	static public function getSnippets($parms = null)
	{
		$records = [];

		$limit = isset($parms['limit']) ? $parms['limit'] : PHP_INT_MAX;
		$orderBy = isset($parms['orderBy']) ? $parms['orderBy'] : 'updated_at DESC';
		$languageId = isset($parms['languageId']) ? $parms['languageId'] : 0;
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '>=';

		try
		{
			$records = Definition::select()
				->where('type_flag', DEFTYPE_SNIPPET)
				->where('language_flag', $languageFlagCondition, $languageId)
				->orderByRaw($orderBy)
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return $records;
	}

	static public function makeQna($records)
    {
		$qna = [];
		$cnt = 0;
		foreach($records as $record)
		{
			$question = $record->title;
			$translation = getOrSetString($record->translation_en, $question . ': translation not set');
			$definition = getOrSetString($record->definition, $question . ': definition not set');

            $qna[$cnt]['q'] = $question;
            $qna[$cnt]['a'] = $translation;
            $qna[$cnt]['definition'] = $definition;
            $qna[$cnt]['translation'] = $translation;
            $qna[$cnt]['extra'] = '';
            $qna[$cnt]['id'] = $record->id;
            $qna[$cnt]['ix'] = $cnt; // this will be the button id, just needs to be unique
            $qna[$cnt]['options'] = '';

			$cnt++;
		}

		//dd($qna);

		return $qna;
	}
}
