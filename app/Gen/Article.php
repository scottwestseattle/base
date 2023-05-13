<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use App\DateTimeEx;
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

    static public function getExercise($subType)
    {
        $record = null;
		$count = self::getCount();
		if ($count > 1)
		{
		    $ix = 1;
		    switch($subType)
		    {
		        case HISTORY_SUBTYPE_EXERCISE_RANDOM:
        			$ix = rand(1, $count);                       // 1-based
		            break;
		        case HISTORY_SUBTYPE_EXERCISE_OTD:
                    $ix = DateTimeEx::getIndexByDay($count) + 1; // 1-based
		            break;
		        default:
		            break;
		    }

            $parms['orderBy'] = 'id';
            $parms['start'] = $ix;

            $record = self::getRecord($parms);
		}

        return $record;
    }

    static public function getCount($parms = null)
    {
		$count = Entry::select()
				//->where('site_id', Site::getId())
				->where('type_flag', ENTRY_TYPE_ARTICLE)
				->where('language_flag', getLanguageId())
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->count();
				//->toSql();

        return $count;
    }

    static public function getRecord($parms = null)
    {
        $parms = crackParms($parms);

		$record = Entry::select()
				//->where('site_id', Site::getId())
				->where('type_flag', ENTRY_TYPE_ARTICLE)
				->where('language_flag', getLanguageId())
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->orderByRaw($parms['orderBy'])
				->offset($parms['start'])
				->limit(1)
				->get();

		if (isset($record) && count($record) > 0)
		    $record = $record->first();

        return $record;
    }

    static public function getFirst($parms)
    {
		$record = Entry::select()
				->where('site_id', Site::getId())
				->where('type_flag', ENTRY_TYPE_ARTICLE)
				->where('language_flag', getLanguageId())
				->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
				->orderByRaw($parms['orderBy'])
				->first();

        return $record;
    }

	static public function search($string, $options = null)
	{
		$string = alphanum($string);
        $collation = 'COLLATE UTF8MB4_GENERAL_CI'; // case insensitive
   		$search = $collation . ' LIKE "%' . $string . '%"';

        $wholeWord = false; //getOrSet($options['wholeWord'], false); << doesn't work for array indexes

		$records = $record = Entry::select()
				->whereIn('type_flag', [ENTRY_TYPE_ARTICLE, ENTRY_TYPE_BOOK])
				->where('language_flag', getLanguageId())
				->where(function ($query) use($search) {$query
    				->where('release_flag', '>=', Status::getReleaseFlag())
					->orWhere('user_id', Auth::id())
					;})
				->where(function ($query) use($search) {$query
                    ->whereRaw('title ' . $search)
					->orWhereRaw('description_short ' . $search)
					->orWhereRaw('description ' . $search)
					->orWhereRaw('description_translation ' . $search)
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
                    else
                    {
                        //
                        // no match so look for accent chars
                        //
                        $string = iconv('UTF-8','ASCII//TRANSLIT', $string);

                        $sentenceUtf = utf8_encode($sentence);
                        $stringUtf = utf8_encode($string);
                        $utf = (strlen($stringUtf) != strlen($string)) ? 'UTF: ' : ''; // if has an accent char

                        if (stristr($sentence, $string))
                        {
                            $matches[] = str_ireplace($stringUtf, highlightText($string), $sentence);
                        }

                        //dd($stringUtf);
                    }
                }

                $record['matches'] = $matches;
            }
        }

        // do deep search
        if ($wholeWord)
        {
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
        }

		return $records;
	}

}
