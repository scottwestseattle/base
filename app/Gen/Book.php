<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

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

    static protected function getBook($entry)
	{
	    $record = null;

		if (isset($entry))
		{
            foreach($entry->tags as $tag)
            {
                if ($tag->type_flag == TAG_TYPE_BOOK)
                {
                    $record = $tag;
                }
            }
        }

        return $record;
    }

    static public function getTitle($entry)
	{
	    $rc = 'Not Set';

		if (isset($entry->tags))
		{
            foreach($entry->tags as $tag)
            {
                if ($tag->type_flag == TAG_TYPE_BOOK)
                {
                    $rc = $tag->name;
                    break;
                }
            }
        }

        return $rc;
    }

    static protected function getChapterCount()
	{
	}

    static protected function getNextChapter($entry, $next = true)
	{
		$record = null;
		if (isset($entry) && isset($entry->display_date))
		{
            foreach($entry->tags as $tag)
            {
                if ($tag->type_flag == TAG_TYPE_BOOK)
                {
                    $found = false;
                    $prev = null;
                    foreach($tag->books as $chapter)
                    {
                        if ($found)
                        {
                            // looking for next, return this chapter
                            $record = $chapter;

                            break;
                        }

                        // stop on the next loop
                        if ($chapter->id == $entry->id)
                        {
                            if ($next)
                            {
                                $found = true;
                            }
                            else
                            {
                                // looking for prev, we already have it so break
                                $record = $prev;
                                break;
                            }
                        }
                        else
                        {
                            $prev = $chapter;
                        }
                    }
                }
            }
		}

		return $record;
	}

}
