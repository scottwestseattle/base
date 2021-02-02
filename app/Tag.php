<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use DB;

use App\Entry;

class Tag extends Model
{
	use SoftDeletes;

	const _typeFlags = [
        TAG_TYPE_NOTSET         => 'Not Set',
        TAG_TYPE_SYSTEM         => 'system',
        TAG_TYPE_BOOK           => 'book',
        TAG_TYPE_DEF_FAVORITE   => 'Favorites',
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
        return $this->belongsToMany('App\Entry')->orderBy('display_date');
    }

    public function definitions()
    {
		// many to many
        return $this->belongsToMany('App\Gen\Definition')->orderBy('title');
    }

    public function definitionsUser()
    {
		return $this->belongsToMany('App\Gen\Definition')->wherePivot('user_id', Auth::id()); //->orderBy('title');
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

		if (isset($userId))
		{
			$record = $record = Tag::select()
					->where('deleted_at', null)
					->where('name', $name)
					->where('type_flag', $type)
					->where('user_id', $userId)
					->first();
		}
		else
		{
			$record = $record = Tag::select()
					->where('deleted_at', null)
					->where('name', $name)
					->where('type_flag', $type)
					->first();
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
			$record->user_id 	= intOrNull($userId);
			$record->type_flag 	= $type;
			$record->name 	 	= $name;

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

    static public function getByType($type)
    {
		$type = intval($type);

		$records = $record = Tag::select()
				->where('deleted_at', null)
				->where('type_flag', $type)
				->get();

		return $record;
    }
}
