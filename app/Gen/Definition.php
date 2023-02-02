<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

use DB;
use Auth;

use App\Entry;
use App\Gen\Definition;
use App\Gen\Spanish;
use App\Site;
use App\Tag;
use App\User;

// parts of speech
define('DEFINITIONS_POS_NOTSET',        0);
define('DEFINITIONS_POS_VERB',          1);
define('DEFINITIONS_POS_NOUN',          2);
define('DEFINITIONS_POS_ADJECTIVE',     3);
define('DEFINITIONS_POS_ADVERB',        4);
define('DEFINITIONS_POS_ARTICLE',       5);
define('DEFINITIONS_POS_PREPOSITION',   6);
define('DEFINITIONS_POS_CONJUNCTION',   7);
define('DEFINITIONS_POS_PRONOUN',       8);
define('DEFINITIONS_POS_PHRASE',        50);
define('DEFINITIONS_POS_SNIPPET',       51);
define('DEFINITIONS_POS_OTHER',         60);

class Definition extends Model
{
	use SoftDeletes;

	const _typeFlags = [
        DEFTYPE_NOTSET      => 'Not Set',
        DEFTYPE_SNIPPET     => 'Snippet',
        DEFTYPE_DICTIONARY  => 'Definition',
        DEFTYPE_USER        => 'User',
        DEFTYPE_OTHER       => 'Other',
	];

    public function getTypeFlagName()
    {
        return self::_typeFlags[$this->type_flag];
    }

    static private $_pos = [
        DEFINITIONS_POS_NOTSET      => 'base.not set',
        DEFINITIONS_POS_ADJECTIVE   => 'proj.adjective',
        DEFINITIONS_POS_ADVERB      => 'proj.adverb',
        DEFINITIONS_POS_ARTICLE     => 'proj.article',
        DEFINITIONS_POS_CONJUNCTION => 'proj.conjunction',
        DEFINITIONS_POS_NOUN        => 'proj.noun',
        DEFINITIONS_POS_PREPOSITION => 'proj.preposition',
        DEFINITIONS_POS_PRONOUN     => 'proj.pronoun',
        DEFINITIONS_POS_VERB        => 'proj.verb',
        DEFINITIONS_POS_PHRASE      => 'proj.phrase',
        DEFINITIONS_POS_SNIPPET     => 'proj.snippet',
        DEFINITIONS_POS_OTHER       => 'base.other',
    ];

    static public function getPosOptions()
    {
        return self::$_pos;
    }

    public function getPos()
    {
        //dump($this->pos_flag);
        return self::getPosName($this->pos_flag);
    }

    static public function getPosName($pos)
    {
        return isset($pos) && $pos > DEFINITIONS_POS_NOTSET ? self::$_pos[$pos] : '';
    }

    static public function getIds($records)
    {
		$ids = [];

		if (isset($records) && count($records) > 0)
            foreach($records as $record)
                $ids[] = $record->id;

		return $ids;
    }

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

    public function users()
    {
		return $this->belongsToMany('App\User')->wherePivot('user_id', Auth::id()); //->orderBy('title');
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
        $rc = false;

		foreach($this->tags as $tag)
		{
			$this->removeTag($tag->id);
			$rc = true;
		}

		return $rc;
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

    //todo: not used, just counts the tags, no help
    static public function getUserFavoriteCount()
    {
        $count = DB::table('tags')
            ->leftJoin('definition_tag', function($join) {
                $join->on('definition_tag.tag_id', '=', 'tags.id');
                $join->where('definition_tag.user_id', Auth::id());
            })
            ->select(DB::raw('tags.id, tags.name, tags.user_id, count(definition_tag.tag_id) as wc'))
                ->whereNull('tags.deleted_at')
                ->where('tags.user_id', Auth::id())
                ->where('type_flag', TAG_TYPE_DEF_FAVORITE)
                ->groupBy('tags.id', 'tags.name', 'tags.user_id')
                ->count();

        return $count;
    }

    static public function getUserFavoriteLists()
    {
		$records = null;

		try
		{
            $records = Tag::select()
                ->where('tags.user_id', Auth::id())
   				->where('tags.language_flag', getLanguageId())
                ->where('type_flag', TAG_TYPE_DEF_FAVORITE)
                ->orderByRaw('updated_at DESC')
                ->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting favorite lists';
            logExceptionEx(__CLASS__, __FUNCTION__, $msg . ', ' . $e->getMessage());
		}

        //dump($records);

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

    static public function getFavoriteLists($id = 0)
    {
		$records = null;
        $idCondition = ($id > 0) ? '=' : '>=';

		try
		{
            $records = Tag::select()
                ->where('type_flag', TAG_TYPE_DEF_FAVORITE)
                ->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
                ->where('id', $idCondition, $id)
                ->get();
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting rss favorite lists';
            logExceptionEx(__CLASS__, __FUNCTION__, $msg . ', ' . $e->getMessage());
		}

		return $records;
    }

	//////////////////////////////////////////////////////////////////////
	//
	// Release status
	//
	//////////////////////////////////////////////////////////////////////

    static private $_releaseStatus = [
        RELEASEFLAG_NOTSET      => 'base.not set',
        RELEASEFLAG_PRIVATE     => 'ui.Private',
        RELEASEFLAG_APPROVED    => 'ui.Approved',
        RELEASEFLAG_PAID        => 'ui.Paid',
        RELEASEFLAG_MEMBER      => 'ui.Member',
        RELEASEFLAG_PUBLIC      => 'ui.Public',
    ];

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

    public function getReleaseStatusName()
    {
		return (self::$_releaseStatus[$this->release_flag]);
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

	public function isVerb()
	{
		return ($this->pos_flag == DEFINITIONS_POS_VERB);
    }

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

    static public function getPermalink($permalink)
    {
		$permalink = alphanum($permalink, /* strict = */ true);
		$record = null;

		try
		{
			$record = Definition::select()
				->where('permalink', $permalink)
				->first();

			//dd($record);
		}
		catch (\Exception $e)
		{
			//dd($e);
			$msg = 'Error getting record: ' . $permalink;
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['permalink' => $permalink]);
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

	static public function getNewest($limit, $random = false)
	{
		$records = self::getIndex(DEFINITIONS_SEARCH_NEWEST, $limit);
        $count = count($records);
		$recs = [];

        if ($count > 0)
        {
            if ($random)
            {
                // get random indexes
                $random = self::getRandomIndexes($limit, $count);

                // copy words using random indexes
                foreach($random as $a)
                    $recs[] = $records[$a];
            }
            else
            {
                $recs = $records;
            }
        }

        return $recs;
	}

	static public function getNewestVerbs($limit)
	{
		$records = self::getIndex(DEFINITIONS_SEARCH_NEWEST_VERBS, $limit);
        $count = count($records);
		$recs = [];

        if ($count > 0)
        {
            // get random indexes
            $random = self::getRandomIndexes($limit, $count);

            // copy words using random indexes
            foreach($random as $a)
                $recs[] = $records[$a];
        }

        return $recs;
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
        $count = count($records);
		$recs = [];

        if ($count > 0)
        {
            // get random indexes
            $random = self::getRandomIndexes($limit, $count);

            // copy words using random indexes
            foreach($random as $a)
                $recs[] = $records[$a];
        }

		// return the random words
		return $recs;
	}

	static public function getRandomVerbs($limit)
	{
		$records = self::getIndex(DEFINITIONS_SEARCH_RANDOM_VERBS);
        $count = count($records);
		$recs = [];

        if ($count > 0)
        {
            // get random indexes
            $random = self::getRandomIndexes($limit, $count);

            // copy words using random indexes
            foreach($random as $a)
                $recs[] = $records[$a];
        }

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
		$languageFlag = getLanguageId();
		$releaseFlag = RELEASEFLAG_PUBLIC;
		$releaseCondition = '>=';

		switch($sort)
		{
			case DEFINITIONS_SEARCH_REVERSE:
				$orderBy = 'title desc';
				break;
			case DEFINITIONS_SEARCH_NEWEST:
				$orderBy = 'id desc';
				break;
			case DEFINITIONS_SEARCH_OLDEST:
				$orderBy = 'id';
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
					->where('release_flag', $releaseCondition, $releaseFlag)
					->where('language_flag', $languageFlag)
    			    ->where('pos_flag', DEFINITIONS_POS_VERB)
					->where('rank', $rankCondition, $rankValue)
					->limit($limit)
					->orderByRaw($orderBy)
					->get();
			}
			else if ($sort === DEFINITIONS_SEARCH_MISSING_TRANSLATION)
			{
				$records = Definition::select()
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->where('language_flag', $languageFlag)
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
					->where('language_flag', $languageFlag)
					->where('wip_flag', '<', WIP_FINISHED)
					->where('pos_flag', DEFINITIONS_POS_VERB)
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
					->where('language_flag', $languageFlag)
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
					->where('language_flag', $languageFlag)
					->whereNotNull('definition')
					->whereNotNull('translation_en')
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else if ($sort == DEFINITIONS_SEARCH_EXAMPLES)
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->where('language_flag', $languageFlag)
					->whereNotNull('examples')
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
					->where('language_flag', $languageFlag)
					->orderByRaw($orderBy)
					->limit($limit)
					->get();
			}
			else
			{
				$records = Definition::select()
					->whereNull('deleted_at')
        			->where('type_flag', DEFTYPE_DICTIONARY)
					->where('language_flag', $languageFlag)
					->where(function ($query) use ($releaseCondition, $releaseFlag) {$query
						->where('release_flag', $releaseCondition, $releaseFlag)
						->orWhere('user_id', Auth::id())
						;})
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
		    $like = 'LIKE ' . CASE_INSENSITIVE;

            $languageFlag = getLanguageId();
            //dump($languageFlag);

			$records = Definition::select()
				->where('deleted_at', null)
    			->where('type_flag', DEFTYPE_DICTIONARY)
				->where('language_flag', $languageFlag)
				->where(function ($query) use ($like, $word){$query
					->where('title', $like, $word . '%')    // partial match of title
					->orWhere('translation_en', $like, $word . '%')				// partial match of translation
					->orWhere('forms', $like, '%;' . $word . ';%')					// exact match of ";word;"
					->orWhere('conjugations_search', $like, '%;' . $word . '%;%') 	// exact match of ";word;"
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
    static public function searchDictionary($string, $options = null)
    {
		$string = alpha($string);
		$records = null;
        $search = '%' . $string . '%';

        $startsWith = getOrSet($options['startsWith'], false);
        if ($startsWith)
        {
   	    	$search = '' . $string . '%';
        }

		if (isset($string))
		{
            try
            {
    		    $search = ' ' . CASE_INSENSITIVE . ' LIKE "' . $search . '"';

                $records = Definition::select()
                    ->where('type_flag', DEFTYPE_DICTIONARY)
                    ->where('language_flag', getLanguageId())
                    ->where(function ($query) use ($search){$query
                        ->whereRaw('title' . $search)
                        ->orWhereRaw('forms' . $search)
                        ->orWhereRaw('conjugations_search' . $search)
                        ->orWhereRaw('translation_en' . $search)
                        ;})
                    ->orderBy('title')
                    ->get();
                    //->toSql();
                    //dd($records);
            }
            catch (\Exception $e)
            {
                $msg = 'Search error: ' . $search;
                logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
            }
		}

		return $records;
	}

	// search checks title and forms
    static public function search($word)
    {
		$word = alphanum(/*strtolower*/($word), /* strict = */ true);
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

    public function isSnippet()
    {
        $rc = false;

		if ($this->type_flag == DEFTYPE_SNIPPET)
		{
            $rc = true;
		}

		return $rc;
	}

    static public function isSnippetStatic($record)
    {
		return ($record->type_flag == DEFTYPE_SNIPPET);
	}

	// search checks title and forms
    static public function searchSnippets($string, $options = null)
    {
		$string = alpha($string);
		$records = null;

		if (isset($string))
		{
            $search  = '%' . $string . '%';
            $startsWith = getOrSet($options['startsWith'], false);
            if ($startsWith)
            {
                $search = '' . $string . '%';
            }

            try
            {
                $records = Definition::select()
                    ->where('type_flag', DEFTYPE_SNIPPET)
                    ->where('language_flag', getLanguageId())
                    ->where(function ($query) use ($search) {
                        $query
                        ->whereRaw('title ' . CASE_INSENSITIVE . ' like "' . $search . '"')
                        ->orWhereRaw('translation_en ' . CASE_INSENSITIVE . ' like "' . $search . '"')
                        ;})
                    ->orderBy('title')
                    ->get();
            }
            catch (\Exception $e)
            {
                $msg = 'Error finding word: ' . $word;
                logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
            }
		}

		return $records;
	}

	static public function addDefinition($parms)
	{
        $f = __CLASS__ . ':' . __FUNCTION__;
		$record = new Definition();

		$record->title 			= isset($parms['title']) ? $parms['title'] : null; // let it throw if not set
		$record->user_id 		= Auth::id();
		$record->language_flag 	= isset($parms['language_flag']) ? $parms['language_flag'] : getLanguageId();
		$record->translation_en	= isset($parms['translation_en']) ? $parms['translation_en'] : null;
		$record->permalink		= createPermalink($record->title);
		$record->wip_flag		= WIP_DEFAULT;
		$record->pos_flag   	= isset($parms['pos_flag']) ? $parms['pos_flag'] : DEFINITIONS_POS_SNIPPET;
		$record->type_flag      = ($record->pos_flag == DEFINITIONS_POS_SNIPPET) ? DEFTYPE_SNIPPET : DEFTYPE_DICTIONARY;
        $record->release_flag   = RELEASEFLAG_PRIVATE;

        //dd($record);

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
		}

		return $record;
	}

	static public function getSnippet($value)
	{
		$record = null;

		try
		{
			$record = Definition::select()
				->where('title', $value)
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

	static public function getWithStats($parms = null)
	{
		$records = [];

		$limit = isset($parms['count']) ? $parms['count'] : DEFAULT_LIST_LIMIT;
		$start = isset($parms['start']) ? $parms['start'] : 0;
		$languageId = isset($parms['languageId']) ? $parms['languageId'] : 0;
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '>=';
		$userId = isset($parms['userId']) ? $parms['userId'] : 0;
		$userIdCondition = isset($parms['userIdCondition']) ? $parms['userIdCondition'] : '>=';
		$order = isset($parms['order']) ? alphanum($parms['order']) : 'owner';
		$orderBy = self::crackOrder($parms, 'desc');
		$userId = isset($parms['userId']) ? $parms['userId'] : getUserId();
		$userIdCondition = isset($parms['userIdCondition']) ? $parms['userIdCondition'] : '=';
		$releaseFlag = isset($parms['releaseFlag']) ? $parms['releaseFlag'] : RELEASEFLAG_PUBLIC;
		$releaseFlagCondition = isset($parms['releaseCondition']) ? $parms['releaseCondition'] : '>=';
        $typeFlag = isset($parms['type']) ? $parms['type'] : DEFTYPE_SNIPPET;

        // release_flag splits it by user's and public; TODO: add collation so it will sort right

        $orderBy = 'definitions.release_flag, ' . $orderBy;

        if ($order === 'public')
        {
            // only show public
            $userId = -1;
            $userIdCondition = '=';
        }

        //dump('release_flag ' . $releaseFlagCondition . ' '. $releaseFlag . ' OR user_id ' . $userIdCondition . ' ' . $userId . ' ORDER BY ' . $orderBy);

		try
		{
            $records = Definition::select(
                    'definitions.*',
                    'stats.qna_attempts', 'stats.qna_score', 'stats.qna_at', 'stats.views', 'stats.viewed_at', 'stats.reads', 'stats.read_at'
                )
                ->leftJoin('definition_user', function($join) {
                    $join->on('definition_user.definition_id', '=', 'definitions.id');
                    $join->on('definition_user.user_id', 'definitions.user_id'); // works for users not logged in
                })
                ->leftJoin('stats', function($join) use($userId) {
                    $join->on('stats.definition_id', 'definitions.id')->where('stats.user_id', $userId);
                })
                ->where('definitions.type_flag', $typeFlag)
                ->where('definitions.language_flag', $languageFlagCondition, $languageId)
                ->where(function ($query) use ($releaseFlagCondition, $releaseFlag, $userId, $userIdCondition) {$query
                	->orWhere('definitions.release_flag', $releaseFlagCondition, $releaseFlag)
                	->orWhere('definitions.user_id', $userIdCondition, $userId)
                	;})
                ->orderByRaw($orderBy)
                ->offset($start)
                ->limit($limit)
                ->get();
		}
		catch (\Exception $e)
		{
            $msg = $e->getMessage();
			$msg = 'Error getting practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return $records;
    }

	static public function getSnippets($parms = null)
	{
	    //dump('getSnippets ORIG');

		$records = [];

		$limit = isset($parms['count']) ? $parms['count'] : DEFAULT_LIST_LIMIT;
		$start = isset($parms['start']) ? $parms['start'] : 0;
		$languageId = isset($parms['languageId']) ? $parms['languageId'] : 0;
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '>=';
		$userId = isset($parms['userId']) ? $parms['userId'] : 0;
		$userIdCondition = isset($parms['userIdCondition']) ? $parms['userIdCondition'] : '>=';
		$order = isset($parms['order']) ? $parms['order'] : 'owner';
		$orderBy = self::crackOrder($parms, 'desc');
		$userId = isset($parms['userId']) ? $parms['userId'] : 0;
		$userIdCondition = isset($parms['userIdCondition']) ? $parms['userIdCondition'] : '>=';
		$releaseFlag = isset($parms['releaseFlag']) ? $parms['releaseFlag'] : RELEASEFLAG_PUBLIC;
		$releaseCondition = isset($parms['releaseCondition']) ? $parms['releaseCondition'] : '>=';

        if (isAdmin()) // show all records
        {
    		//$userId = 0;
	    	//$userIdCondition = '>=';
        }

        //dump($releaseFlag);
        //dump($releaseCondition);
        //dump($userId);
        //dump($userIdCondition);

		try
		{
		    if ($userId == 0 || $order != 'owner')
		    {
		        //
		        // non-logged-in users OR non-owner default sort:
		        // show the most recently viewed records (updated_at DESC) OR show the specified sort
		        // it's done this way so that if they view one then it moves to the top of their list
		        // otherwise the order would always be same (id DESC) until somebody adds a new one
		        //
                $records = Definition::select()
                    ->where('type_flag', DEFTYPE_SNIPPET)
                    ->where('language_flag', $languageFlagCondition, $languageId)
					->where('release_flag', $releaseCondition, $releaseFlag)
					->where('user_id', $userIdCondition, $userId)
					//->where(function ($query) use ($releaseCondition, $releaseFlag) {$query
					//	->where('release_flag', $releaseCondition, $releaseFlag)
					//	->orWhere('user_id', Auth::id())
					//	;})
                    ->orderByRaw($orderBy)
                    ->offset($start)
                    ->limit($limit)
                    ->get();

                //dump($records);
		    }
            else
            {
                //
                // logged-in users see their records only, most recently viewed (by them only) first
                //
                $orderBy = 'definition_user.updated_at DESC, definitions.id DESC';

				$records = Definition::select('definitions.*')
					->leftJoin('definition_user', function($join) {
    					$join->on('definition_user.definition_id', '=', 'definitions.id');
						$join->on('definition_user.user_id', 'definitions.user_id'); // works for users not logged in
					})
                    ->where('type_flag', DEFTYPE_SNIPPET)
                    ->where('language_flag', $languageFlagCondition, $languageId)
    				->where('definitions.user_id', $userId)
	    			->orderByRaw($orderBy)
	    			->offset($start)
                    ->limit($limit)
		    		->get();

                //dump($records);
            }
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return $records;
	}

	static public function crackOrder($parms, $default)
    {
        $order = isset($parms['order']) ? strtolower(alphanum($parms['order'], false, '-')) : $default;

        if ($order === 'help')
        {
            dump("order: asc|desc|atoz|ztoa|incomplete|attempts|attempts-asc|score|views|views-asc|reads|reads-asc");
        }

        $orderBy = 'id';
        switch($order)
        {
            case 'reads-asc':
                $orderBy = 'stats.reads, stats.read_at, id';
                break;
            case 'asc':
                $orderBy = 'id';
                break;
            case 'desc':
                $orderBy = 'id DESC';
                break;
            case 'atoz':
                $orderBy = 'title';
                break;
            case 'ztoa':
                $orderBy = 'title DESC';
                break;
            case 'incomplete':
                $orderBy = 'translation_en, id';
                break;
            case 'attempts':
                $orderBy = 'stats.qna_attempts DESC, id DESC';
                break;
            case 'attempts-asc':
                $orderBy = 'stats.qna_attempts, stats.qna_at, id';
                break;
            case 'score':
                $orderBy = 'stats.qna_score DESC, id DESC';
                break;
            case 'views':
                $orderBy = 'stats.views DESC, id DESC';
                break;
            case 'views-asc':
                $orderBy = 'stats.views, stats.viewed_at, id';
                break;
            case 'reads':
                $orderBy = 'stats.reads DESC, id DESC';
                break;
            case 'reads-asc':
                $orderBy = 'stats.reads, stats.read_at, id';
                break;
            case 'public':
                $orderBy = 'id DESC';
                break;
            default:
                break;
        }

        return $orderBy;
    }

	static public function getUserFavorites($parms = null)
	{
	    //dump($parms);

		$records = [];

		$count = isset($parms['count']) ? $parms['count'] : PHP_INT_MAX;
		$start = isset($parms['start']) ? $parms['start'] : 0;
		$languageId = isset($parms['languageId']) ? $parms['languageId'] : 0;
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '>=';
		$userIdCondition = isset($parms['userIdCondition']) ? $parms['userIdCondition'] : '>=';
        $orderBy = self::crackOrder($parms, 'stats.qna_at, stats.viewed_at, definitions.id');
		$tagId = isset($parms['tagId']) ? $parms['tagId'] : 0;
		$tagIdCondition = $tagId > 0 ? '=' : '>=';

        if (false)
        {
            // raw sql
            $q = '
                SELECT def.*, stats.qna_attempts, stats.viewed_at, stats.qna_at, stats.views_at, stats.reads_at
                FROM `tags`
                JOIN definition_tag as dt on dt.tag_id = tags.id
                JOIN definitions as def on def.id = dt.definition_id
                LEFT JOIN stats on stats.definition_id = def.id
                WHERE 1
                AND tags.user_id = ?
                AND tags.type_flag = ?
                AND tags.deleted_at IS NULL
                AND def.deleted_at IS NULL
                AND def.language_flag = ?
                ORDER by stats.qna_at, stats.viewed_at, def.id
                LIMIT ?
            ';

            $records = DB::select($q, [Auth::id(), TAG_TYPE_DEF_FAVORITE, $languageId, $count]);
        }
        else
        {
            $records = Tag::select(
                'tags.user_id as user_Id', 'tags.id as tag_id', 'tags.name as tag_name',
                'definitions.id as id', 'definitions.*',
                'stats.qna_attempts', 'stats.qna_score', 'stats.qna_at', 'stats.views', 'stats.viewed_at', 'stats.reads', 'stats.read_at'
                )
                ->join('definition_tag', function($join) {
                    $join->on('definition_tag.tag_id', 'tags.id');
                })
                ->join('definitions', function($join) {
                    $join->on('definitions.id', 'definition_tag.definition_id');
                })
                ->leftJoin('stats', function($join) {
                    $join->on('stats.definition_id', 'definitions.id')->where('stats.user_id', Auth::id());
                })
                ->where('tags.user_id', Auth::id())
                ->where('tags.type_flag', TAG_TYPE_DEF_FAVORITE)
                ->where('tags.id', $tagIdCondition, $tagId)
                ->where('tags.deleted_at', NULL)
                ->where('definitions.deleted_at', NULL)
                ->where('definitions.language_flag', $languageFlagCondition, $languageId)
                ->orderByRaw($orderBy)
                ->offset($start)
                ->limit($count)
                ->get();
                //->toSql();

            //dump(($orderBy));
            //dump(($records));
        }

		return $records;
	}

    static public function touchId($id)
    {
		try
		{
            $record = Definition::select()
                ->where('id', $id)
                ->first();

            if (isset($record))
            {
                $record->touch();
            }
            else
            {
    			$msg = 'Error touching practice text';
                logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
            }
		}
		catch (\Exception $e)
		{
			$msg = 'Error touching practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}
    }

	static public function getReview($parms = null)
	{
		$records = [];

		$languageId = isset($parms['languageId']) ? $parms['languageId'] : getLanguageId();
		$languageFlagCondition = isset($parms['languageFlagCondition']) ? $parms['languageFlagCondition'] : '=';
        $count = isset($parms['count']) ? $parms['count'] : DEFAULT_REVIEW_LIMIT;
        $orderBy = self::crackOrder($parms, 'id DESC');
        $getCountOnly = (isset($parms['getCount']) && $parms['getCount']); // get the record count from the db instead of the records
        $count = $getCountOnly ? PHP_MAX_INT : $count;
        $typeFlag = isset($parms['type']) ? $parms['type'] : DEFTYPE_SNIPPET;

		try
		{
            $userId = Auth::check() ? Auth::id() : 0;
            $records = Definition::select('definitions.*', 'stats.qna_attempts', 'stats.qna_score', 'stats.qna_at', 'stats.views', 'stats.viewed_at', 'stats.reads', 'stats.read_at')
                ->leftJoin('stats', function($join) use($userId) {
                    $join->on('stats.definition_id', 'definitions.id')->where('stats.user_id', $userId);
                })
                ->where('definitions.type_flag', $typeFlag)
                ->where('definitions.language_flag', $languageFlagCondition, $languageId)
                ->whereNotNull('translation_en')
                ->where(function ($query) use ($userId) {$query
                    ->orWhere('definitions.release_flag', '>=', RELEASEFLAG_PUBLIC)
                    ->orWhere('definitions.user_id', $userId)
                    ;})
                ->orderByRaw($orderBy)
                ->limit($count)
                ->get();
                //->toSql();dd($records);
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}

		return $getCountOnly ? count($records) : $records;
	}

	static public function getReviewCount($parms = null)
	{
	    $parms['getCount'] = true;
        $rc = self::getReview($parms);

		return $rc;
	}

	static public function makeQna($records)
    {
		$qna = [];
		$cnt = 0;
		foreach($records as $record)
		{
		    if ($record->type_flag == DEFTYPE_SNIPPET)
		    {
                $question = getOrSetString($record->title, 'snippet not set');
                $translation = getOrSetString($record->translation_en, 'translation not set');
                $definition = getOrSetString($record->definition, 'definition not set');
		    }
		    else
		    {
                $question = $record->title;
                $translation = getOrSetString($record->translation_en, $question . ': translation not set');
                $definition = getOrSetString($record->definition, $question . ': definition not set');
		    }

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

	static public function fixAll()
	{
		try
		{
			$records = Definition::select()
			    ->where('type_flag', '<>', DEFTYPE_SNIPPET)
			    ->where('wip_flag', WIP_FINISHED)
				->where(function ($query) {$query
					->where('pos_flag', DEFINITIONS_POS_NOTSET)
					->orWhereNull('pos_flag')
					;})
			    ->orderBy('id')
				->get();

            $index = 0;
			foreach($records as $record)
			{
			    //dd($record);

			    // reset permalink
			    //$record->permalink = createPermalink($record->title);

                // mark as not finished
                $record->wip_flag = WIP_DEV;

			    if ($record->isConjugated())
			    {
			        //$record->pos_flag = DEFINITIONS_POS_VERB;
    			    //dd($record);
			    }

			    $record->save();

                //if ($index++ > 10)
                //    dd('stop');
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error getting practice text';
            logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg);
		}
	}

	public function isConjugated()
	{
	    $rc = false;

	    if (isset($this->conjugations))
	    {
	        if (is_array($this->conjugations))
	        {
	            if (count($this->conjugations) > 0)
	                $rc = true;
	        }
	        else
	        {
                if (strlen($this->conjugations) > 0)
                    $rc = true;
	        }
	    }

	    return $rc;
	}

    static public function tagDefinitionUser($id, $stats = null)
    {
        $record = Definition::select()
            ->where('id', $id)
            ->get();

        if (isset($record) && count($record) > 0)
        {
            $record = $record[0];
            $record->tagUser($stats);
        }
    }

    public function tagUser($stats = null)
    {
        $correct = isset($stats['correct']) ? $stats['correct'] : 0;

        $record = DB::table('definition_user')
            ->select()
            ->where('user_id', Auth::id())
            ->where('definition_id', $this->id)
            ->get();

        if (isset($record) && count($record) > 0)
        {
            //
            // stats record already exists, update it
            //
            DB::table('definition_user')
                ->where('user_id', Auth::id())
                ->where('definition_id', $this->id)
                ->update([
                    'views' => $record[0]->views + 1,
                    'quiz_attempts' => $record[0]->quiz_attempts + 1,
                    'quiz_correct' => $record[0]->quiz_correct + $correct
                ]);
        }
        else
        {
            //
            // no stats record, create one
            //
            $this->users()->attach(Auth::id(), ['views' => 1, 'quiz_correct' => $correct, 'quiz_attempts' => 1]);
            $this->refresh();
        }
    }

	public function tagCategory($categoryId, $checked)
	{
	    if (isset($checked))
	    {
            dd($categoryId);
		    $this->tags()->attach($tag->id, ['user_id' => Auth::id()]);
	    }
	}
}
