<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use DB;

use App\Gen\Definition;
use App\Tag;

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

    const _releaseFlags = [
		RELEASEFLAG_NOTSET => 'Not Set',
		RELEASEFLAG_PRIVATE => 'Private',
		RELEASEFLAG_APPROVED => 'Approved',
		RELEASEFLAG_PAID => 'Premium',
		RELEASEFLAG_MEMBER => 'Member',
		RELEASEFLAG_PUBLIC => 'Public',
    ];

	const _wipFlags = [
		WIP_NOTSET => 'Not Set',
		WIP_INACTIVE => 'Inactive',
		WIP_DEV => 'Dev',
		WIP_TEST => 'Test',
		WIP_FINISHED => 'Finished',
	];

    static public function getReleaseFlags()
    {
		return self::_releaseFlags;
	}

    static public function getWipFlags()
    {
		return self::_wipFlags;
	}

	static public function getEntryTypes()
	{
		return self::$entryTypes;
	}

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

	static public function getTypeFlagName($type)
	{
		return self::$entryTypes[$type];
	}

	public function isBook()
	{
		return($this->type_flag == ENTRY_TYPE_BOOK);
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

	static public function getReleaseFlag()
	{
		$rc = RELEASEFLAG_PUBLIC;

		if (isAdmin()) // admin sees all
		{
			$rc = RELEASEFLAG_NOTSET;
		}
		//todo: else if (isPaid()) // paid member
		//{
		//	$rc = RELEASEFLAG_PAID;
		//}
		else if (Auth::check()) // member logged in_array
		{
			$rc = RELEASEFLAG_MEMBER;
		}

		return $rc;
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
        $tags = Tag::getByType(TAG_TYPE_BOOK);

        // figure out which ones to show
        $records = [];
        $userLevel = self::getReleaseFlag(); // get the user's level to see which books can be shown
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

        $languageCondition = ($languageFlag == LANGUAGE_ALL) ? '>=' : '=';
        $releaseCondition = isAdmin() ? '>=' : '=';
        $releaseFlag = isAdmin() ? RELEASEFLAG_NOTSET : RELEASEFLAG_PUBLIC;
		try
		{
			$records = Entry::select()
			    ->where('language_flag', $languageCondition, $languageFlag)
			    ->where('release_flag', $releaseCondition, $releaseFlag)
				->orderByRaw('created_at DESC')
				->limit($limit)
				->get();
		}
		catch (\Exception $e)
		{
			logException(__FUNCTION__, $e->getMessage(), __('msgs.Error getting articles'));
		}

        return $records;
    }


	static public function getRecentList($parms, $limit = PHP_INT_MAX)
	{
		$type = intval($parms['type']);
		$languageFlag = $parms['id'];
		$records = [];
		$tag = self::getRecentTag();

		if (isset($tag)) // should always exist
		{
			try
			{
				$records = DB::table('entries')
					->leftJoin('entry_tag', function($join) use ($tag) {
						$join->on('entry_tag.entry_id', '=', 'entries.id');
						$join->where('entry_tag.user_id', Auth::id()); // works for users not logged in
						$join->where('entry_tag.tag_id', $tag->id);
					})
					->select('entries.*')
					->whereNull('entries.deleted_at')
					->where('entries.language_flag', $parms['id'], $parms['condition'])
					->where('entries.type_flag', $type)
					->where('entries.release_flag', '>=', self::getReleaseFlag())
					->orderByRaw('entry_tag.created_at DESC, entries.display_date DESC, entries.id DESC')
					->limit($limit)
					->get();

				//dd($records);
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

		return $records;
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
            logException('countView', $e->getMessage(), $msg);
        }
	}
}
