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

    static public function getFirst($parms)
    {
		$languageFlag = $parms['id'];
		$languageCondition = ($languageFlag == LANGUAGE_ALL) ? '<=' : '=';

		$record = Entry::select()
				->where('site_id', Site::getId())
				->where('type_flag', ENTRY_TYPE_ARTICLE)
				->where('language_flag', $languageCondition, $languageFlag)
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->orderByRaw('id DESC')
				->first();

        return $record;
    }

	static public function search($string, $matchWholeWord = false)
	{
		$string = strtolower(alphanum($string));
   		$search = '%' . $string . '%';

		$records = $record = Entry::select()
//				->where('entries.site_id', Site::getId())
				->whereIn('type_flag', [ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK])
				->where('release_flag', '>=', Status::getReleaseFlag())
				->where(function ($query) use($search) {$query
					->where('title', 'like', $search)
					->orWhere('description_short', 'like', $search)
					->orWhere('description', 'like', $search)
					;})
				->orderByRaw('type_flag, title')
				->get();

        // do deep search
        if ($matchWholeWord)
        foreach($records as $record)
        {
            $matches = [];
            $sentences = str_replace(['.', '!', '?'], '|', $record->description);
            $sentences = explode('|', $sentences);
            foreach($sentences as $sentence)
            {
                $sentence = strtolower(trim($sentence));

                $words = explode(' ', $sentence);
                foreach($words as $word)
                {
                    //if (strpos($sentence, $string) !== false)
                    if ($word == $string)
                    {
                        $matches[] = $sentence;
                        break;
                    }
                }
            }

            $record['matches'] = $matches;
        }

		return $records;
	}

}
