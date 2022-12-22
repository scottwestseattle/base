<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use App\Gen\Definition;
use App\Gen\Spanish;
use App\Status;
use App\Tag;
use DB;

class Entry extends Model
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

    public function tags()
    {
		return $this->belongsToMany('App\Tag');
    }

	//////////////////////////////////////////////////////////////////////
	//
	// Types
	//
	//////////////////////////////////////////////////////////////////////

	static private $entryTypes = [
		ENTRY_TYPE_NOTSET => 'Not Set',
		ENTRY_TYPE_NOTUSED => 'Not Used',
		ENTRY_TYPE_ARTICLE => 'Article',
		ENTRY_TYPE_BOOK => 'Book',
		ENTRY_TYPE_ENTRY => 'Entry',
	];

	static private $_redirects = [
		ENTRY_TYPE_NOTSET   => 'entries',
		ENTRY_TYPE_ARTICLE  => 'articles',
		ENTRY_TYPE_BOOK     => 'books',
		ENTRY_TYPE_ENTRY    => 'entries',
	];

	public function getRedirect()
	{
        $root = self::$_redirects[$this->type_flag];
        $url['index'] = '/' . $root;


        if ($this->type_flag == ENTRY_TYPE_ARTICLE)
            $url['view'] = $root . '/' . $this->permalink;
        else
            $url['view'] = $root . '/view/' . $this->id;

		return $url;
	}

	public function getTypeName()
	{
		return self::$entryTypes[$this->type_flag];
	}

	static public function getEntryTypes()
	{
		return self::$entryTypes;
	}

	static public function getTypeFlagName($type)
	{
		return self::$entryTypes[$type];
	}

	public function isBook()
	{
		return($this->type_flag == ENTRY_TYPE_BOOK);
	}

	public function isArticle()
	{
		return($this->type_flag == ENTRY_TYPE_ARTICLE);
	}

	public function getHistoryType()
	{
	    $rc = HISTORY_TYPE_OTHER;

	    if ($this->isArticle())
	    {
	        $rc = HISTORY_TYPE_ARTICLE;
	    }
	    else if ($this->isBook())
	    {
    	    $rc = HISTORY_TYPE_BOOK;
	    }

	    return $rc;
	}

	public function getViewLink()
	{
	    $rc = '';

	    if ($this->isArticle())
		    $rc = '/articles/view/' . $this->permalink;
	    else if ($this->isBook())
		    $rc = '/books/view/' . $this->id;
        else
		    $rc = '/entries/view/' . $this->id;

		return $rc;
	}

    public function hasTranslation()
    {
		return (isset($this->description_translation) && strlen($this->description_translation) > 0);
    }

    static public function hasTranslationStatic($record)
    {
		return (isset($record->description_translation) && strlen($record->description_translation) > 0);
    }

	//////////////////////////////////////////////////////////////////////
	//
	// Release status
	//
	//////////////////////////////////////////////////////////////////////

    public function isFinished()
    {
		return Status::isFinished($this->wip_flag);
    }

    public function isPublic()
    {
		return Status::isPublic($this->release_flag);
    }

    public function isPrivate()
    {
		return Status::isPrivate($this->release_flag);
    }

    public function getStatus()
    {
		return ($this->release_flag);
    }

	//////////////////////////////////////////////////////////////////
	// Definitions - many to many
	//////////////////////////////////////////////////////////////////

    public function definitions()
    {
		return $this->belongsToMany('App\Gen\Definition')->wherePivot('user_id', Auth::id())->orderBy('title');
    }

	public function getDefinitions($userId)
	{
		$userId = intval($userId);
		$entryId = $this->id;
		$records = DB::table('entries')
			->join('definition_entry', function($join) use ($entryId, $userId) {
				$join->on('definition_entry.entry_id', '=', 'entries.id');
				$join->where('definition_entry.entry_id', $entryId);
				$join->where('definition_entry.user_id', $userId);
			})
			->join('definitions', function($join) use ($entryId) {
				$join->on('definitions.id', '=', 'definition_entry.definition_id');
				$join->whereNull('definitions.deleted_at');
			})
			->select('definitions.*')
			->where('entries.deleted_flag', 0)
			->orderBy('definitions.title')
			->get();

		return $records;
	}

	static public function getDefinitionsUser()
	{
		$records = DB::table('entries')
			->join('definition_entry', function($join) {
				$join->on('definition_entry.entry_id', '=', 'entries.id');
				$join->where('definition_entry.user_id', Auth::id());
			})
			->select(DB::raw('entries.id, entries.title, count(definition_entry.entry_id) as wc'))
			->where('entries.deleted_flag', 0)
			->whereIn('entries.type_flag', array(ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK))
			->groupBy('entries.id', 'entries.title')
			->orderBy('entries.title')
			->get();

		//dd($records);

		return $records;
	}

    static public function addDefinitionUserStatic($entryId, $def)
    {
		$entryId = intval($entryId);
		$userId = Auth::id();
		$f = __FUNCTION__;

		if ($entryId > 0)
		{
			if (isset($def))
			{
				$record = $record = Entry::select()
					->where('deleted_flag', 0)
					->where('id', $entryId)
					->first();

				if (isset($record))
				{
					$record->addDefinitionUser($def);
				}
				else
				{
					logError($f . ': entry not found', null, ['entryId' => $entryId]);
				}
			}
			else
			{
				logError($f . ': def not set');
			}
		}
		else
		{
			logError($f . ': entry id not set');
		}
	}

    public function addDefinitionUser(Definition $def)
    {
		if (Auth::check())
		{
			$this->addDefinition($def, Auth::id());
		}
	}

    public function addDefinition($def, $userId = null)
    {
		if (isset($def) && isset($userId))
		{
			$this->definitions()->detach($def->id); // if it's already tagged, remove it so it will by updated
			$this->definitions()->attach($def->id, ['user_id' => $userId]);
			logInfo('added: ' . $def->title, null, ['id' => $def->id]);
		}
		else
		{
			logError(__FUNCTION__ . ': ' . 'error adding definition for user', null, ['def' => '$def', 'userId' => $userId]);
		}
    }

    public function removeDefinitionUser($defId)
    {
		$rc = '';

		if (Auth::check())
		{
			$def = Definition::getById($defId);
			if (isset($def))
			{
				$this->definitions()->detach($def->id, ['user_id' => Auth::id()]);
				$rc = 'success';
			}
			else
			{
				$rc = 'definition not found';
			}
		}
		else
		{
			$rc = 'not logged in';
		}

		return $rc;
	}

    public function removeDefinition(Definition $def)
    {
		$this->definitions()->detach($def->id);
    }

    public function removeDefinitions()
    {
		try
		{
			$cnt = 0;
			foreach($this->definitions as $record)
			{
				$this->definitions()->detach($record->id);
				$cnt++;
			}

			Event::logDelete(LOG_MODEL_ENTRIES, 'removeDefinitions - removed ' . $cnt . ' definitions from entry', $this->id);
		}
		catch (\Exception $e)
		{
			$msg = 'Error removing words from vocabulary list';
			Event::logException(LOG_MODEL, LOG_ACTION_DELETE, 'removeDefinitions()', $msg, $e->getMessage());
			Tools::flash('danger', $msg);
		}
    }

	//////////////////////////////////////////////////////////////////////
	//
	// Tag functions: recent tag and read location
	//
	//////////////////////////////////////////////////////////////////////

	// add or update system 'recent' tag for entries.
	// this lets us order by most recent entries
    public function tagRecent()
    {
		$readLocation = 0;

		if (Auth::check()) // only for logged in users
		{
			$recent = self::getRecentTag();
			if (isset($recent)) // replace old one if exists
			{
				$readLocation = $this->getReadLocation($recent->id);
				$this->tags()->detach($recent->id, ['user_id' => Auth::id()]);
			}

			$this->tags()->attach($recent->id, ['user_id' => Auth::id(), 'read_location' => $readLocation]);
			$this->refresh();
		}

		return $readLocation;
    }

    static public function getRecentTag()
    {
		return Tag::getOrCreate('recent', TAG_TYPE_SYSTEM);
	}

    public function removeTags()
    {
		try
		{
			$cnt = 0;
			foreach($this->tags as $record)
			{
				$this->tags()->detach($record->id);
				$cnt++;
			}

			logInfo(LOG_MODEL_ENTRIES, '' . $cnt . ' tags removed before deleting: ' . $this->title, ['id' => $this->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error removing tags from entry';
			logException('removeTags', $e->getMessage(), $msg, ['id' => $record->id]);
		}
    }

    // keeps track of line number in the article during reading
    // so we can resume reading at the same location
    public function setReadLocation($readLocation)
    {
		$rc = false;

		if (Auth::check())
		{
			$recent = self::getRecentTag();
			if (isset($recent)) // replace old one if exists
				$this->tags()->detach($recent->id, ['user_id' => Auth::id()]);

			$this->tags()->attach($recent->id, ['user_id' => Auth::id(), 'read_location' => $readLocation]);
			$this->refresh();
			$rc = true;
		}

		return $rc;
	}

    // keeps track of line number in the article during reading
    // so we can resume reading at the same location
    private function getReadLocation($tagId)
    {
		$readLocation = 0;

		if (Auth::check())
		{
			$record = DB::table('entry_tag')
					->where('tag_id', $tagId)
					->where('entry_id', $this->id)
					->where('user_id', Auth::id())
					->first();

			if (isset($record))
			{
				$readLocation = $record->read_location;
			}
		}

		return intval($readLocation);
	}

	// add or update system 'book' tag for entries.
	// this is how book chapters are linked together
	// update is the only public one so all calls come through here where type is checked
    public function updateBookTag()
    {
		if (intval($this->type_flag) === ENTRY_TYPE_BOOK)
        {
			// non-book may have been changed to book
			$this->addBookTag();
		}
		else
		{
			// in case book was changed to non-book
			$this->removeBookTag();
		}
	}

    private function addBookTag()
    {
		if (isAdmin()) // for now only admin can update a book
		{
			$tag = $this->getBookTag();
			if (isset($tag)) // replace old one if exists
			{
				$this->tags()->detach($tag->id);
				$this->tags()->attach($tag->id);
			}
		}
    }

    private function removeBookTag()
    {
		if (isAdmin()) // for now only admin can update a book
		{
			// don't use getOrCreate() because it doesn't have to exist
			$name = $this->source;
			$tag = Tag::get($name, TAG_TYPE_BOOK);
			if (isset($tag))
			{
				$this->tags()->detach($tag->id);
			}
		}
    }

    static public function getBookTags()
    {
        $tags = Tag::getByType(TAG_TYPE_BOOK, 'id DESC');

        // figure out which ones to show
        $records = [];
        $userLevel = Status::getReleaseFlag(); // get the user's level to see which books can be shown
        foreach($tags as $record)
        {
            foreach($record->books as $r)
            {
                if ($r->release_flag >= $userLevel)
                {
                    $records[] = $record;
                    break;
                }
            }
        }

		return $records;
	}

    private function getBookTag()
    {
        //dd($this->source);
		$name = $this->source;
		return Tag::getOrCreate($name, TAG_TYPE_BOOK);
	}

	//////////////////////////////////////////////////////////////////////
	//
	// Articles and Books
	//
	//////////////////////////////////////////////////////////////////////

    static public function getArticles($languageFlag = LANGUAGE_ALL, $limit = PHP_INT_MAX)
    {
        $records = null;

        if ($languageFlag == LANGUAGE_ALL)
        {
            $languageCondition = '<=';
        }
        else
        {
            $languageCondition = '=';
        }

        $releaseCondition = isAdmin() ? '>=' : '=';
        $releaseFlag = isAdmin() ? RELEASEFLAG_NOTSET : RELEASEFLAG_PUBLIC;

		try
		{
			$records = Entry::select()
			    ->where('language_flag', $languageCondition, $languageFlag)
			    ->where('release_flag', $releaseCondition, $releaseFlag)
			    ->where('type_flag', ENTRY_TYPE_ARTICLE)
				->orderByRaw('created_at DESC')
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			logException(__FUNCTION__, $e->getMessage(), __('proj.Error getting articles'));
		}

        return $records;
    }

	static public function getRecentList($parms)
	{
	    $start = isset($parms['start']) ? intval($parms['start']) : 0;
	    $limit = isset($parms['limit']) ? intval($parms['limit']) : LIST_LIMIT_DEFAULT;

		$type = intval($parms['type']);
		$languageFlag = $parms['id'];
		$languageCondition = ($languageFlag == LANGUAGE_ALL) ? '<=' : '=';
		$records = [];
		$tag = self::getRecentTag();

		// release
        $releaseFlag = Status::getReleaseFlag();
        $releaseCondition = '>=';

        // user
        $ownerId = Auth::id();
        $ownerCondition = '=';

        //
        // orderBy
        //
		$parms['orderBy'] = (isset($parms['orderBy'])) ? $parms['orderBy'] : null; // make sure it exists

        switch ($parms['orderBy'])
        {
            case 'date-asc':
                $orderBy = 'entries.display_date ASC';
                break;
            case 'date-desc':
                $orderBy = 'entries.display_date DESC';
                break;
            case 'title-asc':
                $orderBy = 'entries.title ASC';
                break;
            case 'title-desc':
                $orderBy = 'entries.title DESC';
                break;
            default:
                $orderBy = Auth::check()
                    ? 'entry_tag.created_at DESC, entries.display_date DESC, entries.id DESC'
                    : 'entries.updated_at DESC';
                break;
        }

        if (isset($parms['release']))
        {
            // all users
            $ownerId = 0;
            $ownerCondition = '>=';

            if (Auth::check())
            {
                if ($parms['release'] == 'private')
                {
                    $releaseFlag = RELEASEFLAG_APPROVED;
                    $releaseCondition = '<=';

                    // current user
                    $ownerId = Auth::id();
                    $ownerCondition = '=';
                }
                else if ($parms['release'] == 'other')
                {
                    $releaseFlag = RELEASEFLAG_APPROVED;
                    $releaseCondition = '<=';

                    // current user
                    $ownerId = Auth::id();
                    $ownerCondition = '<>';
                }
                else if ($parms['release'] == 'public')
                {
                    $releaseFlag = RELEASEFLAG_PAID;
                    $releaseCondition = '>=';
                }
            }
            else
            {
                if ($parms['release'] == 'private')
                {
                    // make sure none match
                    $releaseFlag = RELEASEFLAG_NOTSET;
                    $releaseCondition = '<';

                    // make sure none match
                    $ownerId = -1;
                    $ownerCondition = '=';
                }
                else if ($parms['release'] == 'public')
                {
                    // public
                    $releaseFlag = RELEASEFLAG_PUBLIC;
                    $releaseCondition = '>=';

                    // make sure All match
                    $ownerId = -1;
                    $ownerCondition = '>=';
                }
            }
        }

        //dump($orderBy);
        //dump($parms);
        //dump($languageFlag);
        //dump($languageCondition);

		if (isset($tag)) // should always exist
		{
			try
			{
				$count = DB::table('entries')
					->leftJoin('entry_tag', function($join) use ($tag) {
						$join->on('entry_tag.entry_id', '=', 'entries.id');
						$join->where('entry_tag.user_id', Auth::id()); // works for users not logged in
						$join->where('entry_tag.tag_id', $tag->id);
					})
					->select('entries.*')
					->whereNull('entries.deleted_at')
					->where('entries.language_flag', $languageCondition, $languageFlag)
					->where('entries.type_flag', $type)
					->where('entries.release_flag', $releaseCondition, $releaseFlag)
					->where('entries.user_id', $ownerCondition, $ownerId)
					->count();

				$records = DB::table('entries')
					->leftJoin('entry_tag', function($join) use ($tag) {
						$join->on('entry_tag.entry_id', '=', 'entries.id');
						$join->where('entry_tag.user_id', Auth::id()); // works for users not logged in
						$join->where('entry_tag.tag_id', $tag->id);
					})
					->select('entries.*')
					->whereNull('entries.deleted_at')
					->where('entries.language_flag', $languageCondition, $languageFlag)
					->where('entries.type_flag', $type)
					->where('entries.release_flag', $releaseCondition, $releaseFlag)
					->where('entries.user_id', $ownerCondition, $ownerId)
					->orderByRaw($orderBy)
					->offset($start)
					->limit($limit)
					->get();
			}
			catch (\Exception $e)
			{
				$msg = 'Error getting recent list';
    			logException('getRecentList', $e->getMessage(), $msg);
			}
		}
		else
		{
			logException('removeTags', $e->getMessage(), $msg, ['type_flag' => $type]);
		}

		return ['records' => $records, 'count' => $count];
	}

    protected function countView(Entry $entry)
    {
        try
        {
            $entry->view_count++;
            $entry->save();
        }
        catch (\Exception $e)
        {
            $msg = 'Error updating count';
            logException($msg, $e->getMessage());
        }
	}

	static public function search($string)
	{
		$string = alphanum($string);
		$search = '%' . $string . '%';

		$records = $record = Entry::select()
				->where('entries.site_id', Site::getId())
				//->whereIn('type_flag', [ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK])
				->where('language_flag', getLanguageId())
				->where('release_flag', '>=', Status::getReleaseFlag())
				->where(function ($query) use($search) {$query
					->where('title', 'like', $search)
					->orWhere('description_short', 'like', $search)
					->orWhere('description', 'like', $search)
					;})
				->orderByRaw('type_flag, title')
				->get();

		return $records;
	}

    public function getSentences()
    {
        $lines = [];

		$lines = array_merge($lines, Spanish::getSentences($this->title));
		$lines = array_merge($lines, Spanish::getSentences($this->description_short));
		$lines = array_merge($lines, Spanish::getSentences($this->description));

		return $lines;
    }

}
