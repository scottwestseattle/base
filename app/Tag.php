<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use DB;

use App\Entry;
use App\Site;

// system Tag names (made unique)
define('TAG_NAME_WOTD', 'WOTD-4f96d5');
define('TAG_NAME_POTD', 'POTD-4f96d5');

class Tag extends Model
{
	use SoftDeletes;

	const _typeFlags = [
        TAG_TYPE_NOTSET         => 'Not Set',
        TAG_TYPE_SYSTEM         => 'System Tag',
        TAG_TYPE_BOOK           => 'Book',
        TAG_TYPE_DEF_FAVORITE   => 'Favorite',
        TAG_TYPE_DEF_CATEGORY   => 'Category',
        TAG_TYPE_OTHER          => 'Other',
	];

    static public function getTypeFlags()
	{
		return self::_typeFlags;
	}

    public function getTypeFlagName()
	{
		$typeFlag = intval($this->type_flag); // in case it's null
		return self::_typeFlags[$typeFlag];
	}

    public function isTypeFlagSet()
	{
		$typeFlag = intval($this->type_flag); // in case it's null
		return ($typeFlag > TAG_TYPE_NOTSET);
	}

    public function getTypeButtonColor()
	{
		// if type flag is set use the default color
		return ($this->isTypeFlagSet() ? '' : 'type-not-set-color');
	}

    public function entries()
    {
		// many to many
        return $this->belongsToMany('App\Entry')->orderBy('created_at');
    }

    public function books()
    {
		// many to many
        return $this->belongsToMany('App\Entry')->orderBy('display_order');
    }

    public function definitions()
    {
		// many to many
        return $this->belongsToMany('App\Gen\Definition');
    }

    public function definitionsUser()
    {
		return $this->belongsToMany('App\Gen\Definition')->wherePivot('user_id', Auth::id());
    }

	//////////////////////////////////////////////////////////////////////
	//
	// the basic CRUD functions
	//
	//////////////////////////////////////////////////////////////////////

    static public function getOrCreate($name, $type, $userId = null)
	{
		$name = alphanum($name);
		$record = null;

		if (isset($name) && strlen($name) > 0)
		{
			$record = self::get($name, $type, $userId);

			// if not found, add it
			if (!isset($record))
			{
				$record = self::add($name, $type, $userId);
			}
		}
		else
		{
			$msg = 'getOrCreate: error getting tag: invalid name filtered to nothing';
			logError($msg);
		}

		return $record;
	}

    static public function get($name, $type, $userId = null)
    {
		$record = null;
		$name = alphanum($name, true);

        try
        {
            if (isset($userId))
            {
                $record = $record = Tag::select()
                        ->where('name', $name)
                        ->where('type_flag', $type)
                        ->where('user_id', $userId)
                        ->first();
            }
            else
            {
                $record = $record = Tag::select()
                        ->where('name', $name)
                        ->where('type_flag', $type)
                        ->first();
            }
        }
        catch(\Exception $e)
        {
 			$msg = 'Error getting tag: ' . $record->name . ', userId: ' . intval($userId);
			logException($msg, $e->getMessage());
       }

		return $record;
    }

    static public function getPivot($name, $type, $userId = null)
    {
		$record = null;
		$name = alphanum($name, true);

		$tag = DB::table('tags')
			->join('definition_tag', function($join) use($userId) {
				$join->on('definition_tag.tag_id', '=', 'tags.id');
				$join->where('definition_tag.user_id', $userId);
			})
			->select('tags.*')
			->where('deleted_at', null)
			->where('tags.name', $name)
			->where('tags.type_flag', $type)
			->where('tags.user_id', $userId)
			->first();

		if (isset($tag))
		{
			// get it the laravel way so it will include the definitions list for the user
			$record = Tag::select()
					->where('id', $tag->id)
					->first();
		}

		return $record;
	}

    static public function add($name, $type, $userId = null)
    {
		$record = null;
		$type = intval($type);
		$name = alphanum($name, true);
		if (!isset($name) || strlen($name) === 0) // if nothing is left
		{
			$msg = 'Error adding tag: invalid name, user_id: ' . intval($userId) . '';
			logError($msg);
		}
		else if ($type <= 0) // type_flag is required
		{
			$msg = 'Error adding tag: invalid type_flag: ' . $type . ', tag name: . ' . $name . ', user_id: . ' . intval($userId) . '';
			logError($msg);
		}
		else
		{
			$record = new Tag();

			$record->name = $name;
			$record->user_id 	= intOrNull($userId);
			$record->type_flag 	= $type;
			$record->language_flag = getLanguageId();

			try
			{
				$record->save();
				logInfo('Tag Added', null, ['record' => $record->name, 'user_id' => $record->user_id, 'id' => $record->id]);
			}
			catch (\Exception $e)
			{
				$msg = 'Error adding tag: ' . $record->name . ', userId: ' . intval($userId);
				logException($msg, $e->getMessage());
			}
		}

		return $record;
    }

    static public function getById($id)
    {
		$id = intval($id);

		$record = $record = Tag::select()
				->where('deleted_at', null)
				->where('id', $id)
				->first();

		return $record;
    }

    static public function getByType($type, $orderBy = null)
    {
		$type = intval($type);
		$orderBy = ($orderBy != null) ? $orderBy : 'id ASC';
	    $language = Site::getLanguage();

		$records = $record = Tag::select()
				->where('deleted_at', null)
				->where('type_flag', $type)
				->where('language_flag', $language['id'])
				->orderByRaw($orderBy)
				->get();

		return $record;
    }

    static public function countUse(Tag $tag)
    {
        try
        {
            $tag->use_count++;
            $tag->save();
        }
        catch (\Exception $e)
        {
            $msg = 'Error updating usage count';
            logException($msg, $e->getMessage());
        }
	}

    static public function createUserFavoriteList($name)
    {
        $record = null;

		try
		{
            $record = self::getOrCreate($name, TAG_TYPE_DEF_FAVORITE, Auth::id());

            if (false)
            {
			$record = new Tag();
			$record->name = $name;
			$record->type_flag = TAG_TYPE_DEF_FAVORITE;
			$record->user_id = Auth::id();
			$record->save();
            }

			logInfo(__FUNCTION__, __('proj.New list has been added'), ['name' => $record->name, 'id' => $record->id]);
		}
		catch (\Exception $e)
		{
			$msg = 'Error adding new list';
			logException(__FUNCTION__, $msg, $e->getMessage());
		}

		return $record;
    }
}
