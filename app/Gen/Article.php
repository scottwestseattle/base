<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Entry;
use App\Site;
use App\Status;

class Article extends Model
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

	static public function search($string)
	{
		$string = alphanum($string);
		$search = '%' . $string . '%';

		$records = $record = Entry::select()
				->where('entries.site_id', Site::getId())
				->whereIn('type_flag', [ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK])
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

}
