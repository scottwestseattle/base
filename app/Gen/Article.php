<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use App\Entry;
use App\Gen\Spanish;
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
				->orderByRaw($parms['orderBy'])
				->first();

        return $record;
    }

	static public function search($string, $matchWholeWord = false)
	{
		$string = alphanum($string);
   		$search = '%' . $string . '%';
        $collation = 'COLLATE UTF8MB4_GENERAL_CI'; // case insensitive

		$records = $record = Entry::select()
				->whereIn('type_flag', [ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK])
				->where(function ($query) use($search) {$query
    				->where('release_flag', '>=', Status::getReleaseFlag())
					->orWhere('user_id', Auth::id())
					;})
				->where(function ($query) use($search, $collation) {$query
                    ->whereRaw('title ' . $collation . ' like "' . $search . '"')
					->orWhereRaw('description_short ' . $collation . ' like "' . $search . '"')
					->orWhereRaw('description ' . $collation . ' like "' . $search . '"')
					;})
				->orderByRaw('type_flag, title')
				->get();

        if (isset($records))
        {
            foreach($records as $record)
            {
                $matches = [];

        		$sentences = Spanish::getSentences($record->description);
                foreach($sentences as $sentence)
                {
                    if (stristr($sentence, $string))
                    {
                        $matches[] = str_ireplace($string, highlightText($string), $sentence);
                    }
                }

                $record['matches'] = $matches;
            }
        }

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
                        $matches[] = str_ireplace($string, selfDecorateText($string), $sentence);
                        break;
                    }
                }
            }

            $record['matches'] = $matches;
        }

		return $records;
	}

}
