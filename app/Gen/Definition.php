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

define('CONJ_PARTICIPLE', 'participle');
define('CONJ_IND_PRESENT', 'ind_pres');
define('CONJ_IND_PRETERITE', 'ind_pret');
define('CONJ_IND_IMPERFECT', 'ind_imp');
define('CONJ_IND_CONDITIONAL', 'ind_cond');
define('CONJ_IND_FUTURE', 'ind_fut');
define('CONJ_SUB_PRESENT', 'sub_pres');
define('CONJ_SUB_IMPERFECT', 'sub_imp');
define('CONJ_SUB_IMPERFECT2', 'sub_imp2');
define('CONJ_SUB_FUTURE', 'sub_fut');
define('CONJ_IMP_AFFIRMATIVE', 'imp_pos');
define('CONJ_IMP_NEGATIVE', 'imp_neg');

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
					->orderByRaw($orderBy)
					->limit($limit)
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
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), $msg, ['word' => $word]);
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
		$word = Tools::alpha($word);
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
		$word = Tools::alphanum($word, /* strict = */ true);
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

		$any = Tools::endsWithAnyIndex($word, $suffixes);
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

	//////////////////////////////////////////////////////////////////////
	//
	// Conjugations
	//
	//////////////////////////////////////////////////////////////////////

    static public function fixConjugations($record)
    {
		$rc = false;

		if (!isset($record))
		{
			$rc = true;
		}
		else if (isset($record->conjugations))
		{
			if (!isset($record->conjugations_search))
				$rc = true;
		}

		if (isset($record->conjugations_search))
		{
			if (!isset($record->conjugations))
				$rc = true;
			else if (!Str::startsWith($record->conjugations_search, ';'))
				$rc = true;
			else if (!Str::endsWith($record->conjugations_search, ';'))
				$rc = true;
		}

		return $rc;
	}

    static public function getConjugations($raw)
    {
		$rc['full'] = null;		// full conjugations
		$rc['search'] = null;	// conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')

		if (!isset($raw))
			return $rc; // nothing to do

		// quick check to see if it's raw or has already been formatted
		$parts = explode('|', $raw);
		if (count($parts) === 12 && Tools::startsWith($parts[11], ';no '))
		{
			// already cleaned and formatted
			$rc['full'] = $raw;
			$rc['search'] = self::getConjugationsSearch($raw);
		}
		else
		{
			// looks raw so attempt to clean it
			// returns both 'full' and 'search'
			$rc = self::cleanConjugationsPasted($raw);
		}

		return $rc;
	}

	// make the search string either from a word array or from a full conjugation
    static public function getConjugationsSearch($words)
    {
		$rc = null;

		if (!is_array($words))
		{
			// make the words array first
			// raw conjugation looks like: |;mato;mata;matas;|mate;mate;matamos;|
			$tenses = [];
			$lines = explode('|', $words);
			foreach($lines as $line)
			{
				$parts = explode(';', $line);
				if (count($parts) > 0)
				{
					foreach($parts as $part)
					{
						// fix the reflexives
						if (Tools::startsWithAny($part, ['me ', 'te ', 'se ', 'nos ', 'os ', 'no te ', 'no se ', 'no nos ', 'no os ', 'no se ']))
						{
							// chop off the reflexive prefix words, like 'me acuerdo', 'no se acuerden'
							$pieces = explode(' ', $part);
							if (count($pieces) > 2)
								$part = $pieces[2];
							else if (count($pieces) > 1)
								$part = $pieces[1];
							else if (count($pieces) > 0)
								$part = $pieces[0];
						}

						$tenses[] = $part;
					}
				}
			}

			$words = $tenses;
		}

		if (isset($words) && is_array($words))
		{
			$unique = [];
			foreach($words as $word)
			{
				if (strlen($word) > 0)
				{
					if (!in_array($word, $unique))
					{
						$unique[] = $word;
						$rc .= $word . ';';
					}
				}
			}

			$rc = ';' . $rc; // make it mysql searchable for exact match, like: ";voy;vea;veamos;ven;vamos;
		}

		return $rc;
	}

    static public function cleanConjugationsScraped($raw, $reflexive)
    {
		$rc['full'] = null;	  // full conjugations
		$rc['search'] = null; // conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')
		$conj = '';
		$search = '';

		if (!isset($raw))
			return null;

		$words = [];

		//$pos = strpos($raw, 'obtengo'); // 70574
		//dump($pos);
		//$pos = strpos($raw, 'play translation audio'); //
		//dump($pos);
		preg_match_all('/aria-label\=\"(.*?)\"/is', mb_substr($raw, 50000), $parts);

		// figure out where the start and end are
		$start = 0;
		$end = 0;

        //dd($parts); // scrapy
		$parts = $parts[1];

		$matches = count($parts);
		$participle = '';
		$progressivePrefix = $reflexive ? 'me estoy ' : 'estoy ';
		$participlePrefix = $reflexive ? 'me he ' : 'he ';
		if ($matches >= 150) // use the exact number so we can tell if we get unexpected results
		{
			// fix up the array first
			$words = [];
			$wordsPre = [];
			$word = '';
			foreach($parts as $part)
			{
			    $partialMatch = 'View the conjugation';
			    if (Str::startsWith($part, $partialMatch))
			    {
			        // fix the line that is specific to the verb looked up, looks like 'View the conjugation for to lose'
			        $part = $partialMatch;
			    }

				switch($part)
				{
					// get rid of all of the trash
					case 'Spanishdict Homepage':
					case 'SpanishDict Homepage':
					case 'SpanishDict logo':
					case 'more':
					case 'Menu':
					case 'Enter a Spanish verb':
					case 'Search':
					case 'play headword audio':
					case 'play translation audio':
					case 'Preterite':
					case 'Imperfect':
					case 'Present':
					case 'Subjunctive':
					case 'View the conjugation':
						break;
					default:
						$word = $part;
						$words[] = $word;
						break;
				}

				if (Tools::startsWith($word, $progressivePrefix))
				{
					// save  the progressive form
					$wordsPre[] = mb_substr($word, strlen($progressivePrefix));
				}
				else if (Tools::startsWith($word, $participlePrefix))
				{
					// save the past participle
					$wordsPre[] = mb_substr($word, strlen($participlePrefix));

					//  break because we've got everything we need
					break;
				}
			}

			// put the pre at the beginning
			$words = array_merge($wordsPre, $words);
			//dbg dump($words);

			// do a pass to create the search string
			$searchUnique = [];
			foreach($words as $word)
			{
				// remove the no from the imperatives
				if (Tools::startsWith($word, 'no '))
				{
					$word = substr($word, strlen('no ')); // remove the "no"
				}

				// check unique array to only add a word once to the search string
				if (!in_array($word, $searchUnique))
				{
					$searchUnique[] = $word;
					$search .= $word . ';';
				}
			}

			//
			// save the conjugations
			//

			// participles
			$participleStem = Tools::str_truncate($words[1], 1);
			$offset = 5;
			$index = 0;
			$participleStem = Tools::str_truncate($words[1], 1);
			$conjugations[CONJ_PARTICIPLE] = ';'
				. $words[$index++] 				// abarcando
				. ';' . $words[$index++] 		// abarcado
				. ';' . $participleStem . 'os' 	// abarcados
				. ';' . $participleStem . 'a' 	// abarcada
				. ';' . $participleStem . 'as' 	// abarcadas
				. ';';
			$conj .= $conjugations[CONJ_PARTICIPLE]; // save the conjugation string

			// indicative
			$factor = 1;
			$conjugations[CONJ_IND_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_PRETERITE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRETERITE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_CONDITIONAL] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_CONDITIONAL]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_FUTURE]; // save the conjugation string

			// subjunctive
			$offset = 4;
			$factor = 1;
			$index += 26;
			$conjugations[CONJ_SUB_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT2] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT2]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_FUTURE]; // save the conjugation string

			// imperatives
			$offset = 2;
			$factor = 1;
			$index += 21;
			$conjugations[CONJ_IMP_AFFIRMATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' ;
			$conj .= '|' . $conjugations[CONJ_IMP_AFFIRMATIVE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IMP_NEGATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IMP_NEGATIVE]; // save the conjugation string

			//dbg dd($conjugations);
		}
		else
		{
			$msg = 'Error cleaning scraped conjugation: total results: ' . count($words);
			//dd($words);
			throw new \Exception($msg);
		}

		$rc['full'] = $conj;
		$rc['search'] = $search;

		return $rc;
	}

    static public function cleanConjugationsPasted($raw)
    {
		$rc['full'] = null;		// full conjugations
		$rc['search'] = null;	// conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')

		if (!isset($raw))
			return null;

		$words = [];
		$v = str_replace(';', ' ', $raw); 	// replace all ';' with spaces
		$v = Tools::alpha($v, true);			// clean it up
		$v = preg_replace('/[ *]/i', '|', $v);	// replace all spaces with '|'
		$parts = explode('|', $v);
		//dd($parts);
		$prefix = null;
		$search = null;
		$searchUnique = [];
		foreach($parts as $part)
		{
			$word = mb_strtolower(trim($part));
			if (strlen($word) > 0)
			{
				// the clean is specific to the verb conjugator in SpanishDict.com
				switch($word)
				{
					case 'participles':
					case 'are':
					case 'present':
					case '1':
					case '2':
					case 'affirmative':
					case 'conditional':
					case 'ellosellasuds':
					case 'future':
					case 'imperfect':
					case 'imperative':
					case 'in':
					case 'indicative':
					case 'irregularities':
					case 'negative':
					case 'nosotros':
					case 'past':
					case 'preterite':
					case 'red':
					case 'subjunctive':
					case 'ud':
					case 'uds':
					case 'vosotros':
					case 'yo':
					case 'tú':
					case 'élellaud':
					/*
					// for wiki, not done because the conjugations are in a different order
					case 'vos':
					case 'usted':
					case 'nosotras':
					case 'vosotras':
					case 'ustedes':
					case 'ellosellas':
					case 'élellaello':
					*/
						break;
					case 'no': // non reflexives with two words
						$prefix = $word; // we need the 'no'
						break;
					default:
					{
						// do this before the 'no' is added
						// check unique array to only add a word once to the search string
						if (!in_array($word, $searchUnique))
						{
							switch($word)
							{
								case 'me': // skip reflexive prefixes
								case 'te':
								case 'se':
								case 'nos':
								case 'os':
									break;
								default:
									$searchUnique[] = $word;
									$search .= $word . ';';
									break;
							}
						}

						if (isset($prefix)) // save the 'no' and use it
						{
							$word = $prefix . ' ' . $word;
							$prefix = null;
						}

						$words[] = $word;
						break;
					}
				}
			}
		}

		//dd($words);
		$search = isset($search) ? ';' . $search : null;

		$count = count($words);
		if ($count == 125) // it's reflexive so need more touch up
		{
			$parts = [];
			foreach($words as $word)
			{
				switch($word)
				{
					case 'me': // reflexive prefixes
					case 'te':
					case 'se':
					case 'nos':
					case 'os':
					case 'no te':
					case 'no se':
					case 'no nos':
					case 'no os':
						$prefix = $word;
						break;
					default:
					{
						if (isset($prefix)) // save the 'no' and use it
						{
							$word = $prefix . ' ' . $word;
							$prefix = null;
						}

						$parts[] = $word;
						break;
					}
				}
			}

			$words = $parts;
			//dd($parts);
		}

		$conj = null;
		$count = count($words);
		//dd($words);
		if ($count == 66) // total verb conjugations
		{
			//
			// save the conjugations
			//
			$conj = '';

			// participles
			$offset = 5;
			$index = 0;
			$participleStem = Tools::str_truncate($words[1], 1);
			$conjugations[CONJ_PARTICIPLE] = ';'
				. $words[$index++] 				// abarcando
				. ';' . $words[$index++] 		// abarcado
				. ';' . $participleStem . 'os' 	// abarcados
				. ';' . $participleStem . 'a' 	// abarcada
				. ';' . $participleStem . 'as' 	// abarcadas
				. ';';
			$conj .= $conjugations[CONJ_PARTICIPLE]; // save the conjugation string

			// indicative
			$factor = 1;
			$conjugations[CONJ_IND_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_PRETERITE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRETERITE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_CONDITIONAL] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_CONDITIONAL]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_FUTURE]; // save the conjugation string

			// subjunctive
			$offset = 4;
			$factor = 1;
			$index += 26;
			$conjugations[CONJ_SUB_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT2] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT2]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_FUTURE]; // save the conjugation string

			// imperatives
			$offset = 2;
			$factor = 1;
			$index += 21;
			$conjugations[CONJ_IMP_AFFIRMATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' ;
			$conj .= '|' . $conjugations[CONJ_IMP_AFFIRMATIVE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IMP_NEGATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IMP_NEGATIVE]; // save the conjugation string

			//dd($conjugations);
		}
		else
		{
			$msg = 'Error cleaning pasted conjugation: total results: ' . count($words);
			//dd($words);
			throw new \Exception($msg);
		}

		$rc['full'] = $conj;
		$rc['search'] = $search;

		return $rc;
	}

    static public function getConjugationsFull($conj)
    {
		$conj = self::getConjugationsPretty($conj);

		if (isset($conj))
		{
		    $pronouns = ['yo', 'tu', 'usted', 'nosotros', 'vosotros', 'ustedes'];
		    $fullSize = count($pronouns);
		    foreach($conj as $record)
		    {
                // looks like: mato, mata, mata, matais, matamos, matan
                $tenses = [];
                $parts = explode(',', $record);
                if (count($parts) == $fullSize)
                {
                    foreach($parts as $key => $part)
                    {
                        $part = trim($part);
                        if (strlen($part) > 0)
                        {
                            $tenses[] = $pronouns[$key] . ' ' . $part;
                        }
                    }
                    $conj['tenses'][] = $tenses;
                }
                else
                {
                    // this will skip the first line which is the participle
                    if (array_key_exists('tenses', $conj))
                    {
                        $tenses = [];
                        if (count($parts) == ($fullSize - 1))
                        {
                            // the imperatives only have 5 tenses
                            foreach($parts as $key => $part)
                            {
                                $part = trim($part);
                                if (strlen($part) > 0)
                                {
                                    // start on 'tu'
                                    $tenses[] = $pronouns[$key + 1] . ' ' . $part;
                                }
                            }
                            $conj['tenses'][] = $tenses;
                        }
                    }
                }
			}
		}

		return $conj;
	}

    static public function getConjugationsPretty($conj)
    {
		$tenses = null;
		if (isset($conj))
		{
			// raw conjugation looks like: |;mato;mata;matas;|mate;mate;matamos;|
			$tenses = [];
			$parts = explode('|', $conj);
			foreach($parts as $part)
			{
				$part = trim($part);
				if (strlen($part) > 0)
				{
					$part = trim($part, ";");
					$part = str_replace(';', ', ', $part);
					$tenses[] = $part;
				}
			}
		}
		//dd($tenses);

/* output:
  0 => "siendo, sido"
  1 => "soy, eres, es, somos, sois, son"
  2 => "fui, fuiste, fue, fuimos, fuisteis, fueron"
  3 => "era, eras, era, éramos, erais, eran"
  4 => "sería, serías, sería, seríamos, seríais, serían"
  5 => "seré, serás, será, seremos, seréis, serán"
  6 => "sea, seas, sea, seamos, seáis, sean"
  7 => "fuera, fueras, fuera, fuéramos, fuerais, fueran"
  8 => "fuese, fueses, fuese, fuésemos, fueseis, fuesen"
  9 => "fuere, fueres, fuere, fuéremos, fuereis, fueren"
  10 => "sé, sea, seamos, sed, sean"
  11 => "no seas, no sea, no seamos, no seáis, no sean"
*/
		return $tenses;
	}

    static public function getFormsPretty($forms)
    {
		$v = preg_replace('/^;(.*);$/', "$1", $forms);
		$v = str_replace(';', ', ', $v);
		return $v;
	}

}
