<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entry extends Model
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

	static public $_lineSplitters = array('Mr.', 'Miss.', 'Sr.', 'Mrs.', 'Ms.', 'St.');
	static public $_lineSplittersSubs = array('Mr:', 'Miss:', 'Sr:', 'Mrs:', 'Ms:', 'St:');
	static public $_fixWords = array(
		'Mr.', 'Sr.', 'Sr ', 'Mrs.', 'Miss.',
		'Y,', 'Y;', 'y,', 'y:',
		'Jessica', 'Jéssica', 'Jess',
		'Max', 'Aspid', 'Áspid',
		'Mariel', 'MARIEL', 'Beaumont', 'BEAUMONT',
		'Dennis',
		'Geovanny', 'Giovanny', 'Geo', 'Gio',
		);
	static public $_fixWordsSubs = array(
		'Señor', 'Señor', 'Señor ', 'Señora', 'Señorita',
		'Y ', 'Y ', 'y ', 'y ',
		'Sofía', 'Sofía', 'Sofía',
		'Pedro', 'Picapiedra', 'Picapiedra',
		'Gerarda', 'Gerarda', 'Gonzalez', 'Gonzalez',
		'Fernando',
		'Jorge', 'Jorge', 'Jorge', 'Jorge',
		);

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

	static public function getSentences($text)
	{
		$lines = [];

		$paragraphs = explode("\r\n", strip_tags(html_entity_decode($text)));
		foreach($paragraphs as $p)
		{
			$p = trim($p);

			// doesn't work for: "Mr. Tambourine Man" / Mr. Miss. Sr. Mrs. Ms. St.
			$p = str_replace(self::$_lineSplitters, self::$_lineSplittersSubs, $p);

			// sentences end with: ". " or "'. " or "\". " or "? " or "! "
			if (true) // split on more characters because the lines are too long
				$sentences = preg_split('/(\. |\.\' |\.\" |\? |\! )/', $p, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
			else
				// the original to avoid splitting on conversation
				$sentences = preg_split('/(\. |\.\' |\.\" )/', $p, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);

			//$sentences = explode($eos, $p);
			for($i = 0; $i < count($sentences); $i++)
			{
				// get the sentence text
				$s = self::formatForReading($sentences[$i]);

				// get the delimiter which is stored in the next array entry
				$i++;
				if (count($sentences) > $i)
				{
					$s .= trim($sentences[$i]);
				}

				// save the sentence
				if (strlen($s) > 0)
				{
					$lines[] = $s;
				}
			}
		}

		//dump($lines);
		return $lines;
	}

	static public function formatForReading($text)
	{
		// change dash to long dash so it won't be read as 'minus'
		$text = str_replace('-', '–', trim($text));

		// put the sentence splitter subs back to the originals
		$text = str_replace(self::$_lineSplittersSubs, self::$_lineSplitters, $text);

		// apply any word fixes
		$text = str_replace(self::$_fixWords, self::$_fixWordsSubs, $text);

		return $text;
	}

}
