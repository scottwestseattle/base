<?php

namespace App\Gen;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

define('CONJ_PARTICIPLE', 'participle');
define('CONJ_IND_PRESENT', 'ind_pres');
define('CONJ_IND_PRETERITE', 'ind_pret');
define('CONJ_IND_IMPERFECT', 'ind_imp');
define('CONJ_IND_CONDITIONAL', 'ind_cond');
define('CONJ_IND_FUTURE', 'ind_fut');
define('CONJ_SUB_PRESENT', 'sub_pres');
define('CONJ_SUB_IMPERFECT', 'sub_imp');
define('CONJ_SUB_IMPERFECT2', 'sub_imp2');
define('CONJ_SUB_FUTURE', 'sub_fut');
define('CONJ_IMP_AFFIRMATIVE', 'imp_pos');
define('CONJ_IMP_NEGATIVE', 'imp_neg');

class Spanish
{
    static public $_verbConjugations = [
        'Participles',
        'Present',
        'Past',
        'Past Perfect',
        'Conditional',
        'Future',
        'Subjunctive Present',
        'Subjunctive Imperfect',
        'Subjunctive Imperfect 2',
        'Subjunctive Future',
        'Imperative',
        'Imperative Negative',
    ];

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

    static public function possibleVerb($word)
    {
		$word = alpha($word);

		$rc = Str::endsWith($word, array_merge(self::$_verbSuffixes, self::$_verbReflexiveSuffixes));

		return $rc;
	}

	// make forms easier to search line: ';one;two;three;'
    static public function formatForms($forms)
    {
		$v = alphanumpunct($forms);
		$v = preg_replace('/[;,]+/', ';', $v); // replace one or more spaces with one ';' to separate words
		$v = self::trimDelimitedString(';', $v);
		$v = self::wrapString(';', $v);

		return $v;
	}

    static public function wrapString($wrapping, $text)
    {
		if (strlen($text) > 0)
		{
			if (!Str::startsWith($text, $wrapping))
				$text = $wrapping . $text;

			if (!Str::endsWith($text, $wrapping))
				$text .= $wrapping;
		}

		return $text;
	}

	// trims each part of a non-whitespace delimited string, like: "no one; one;   two;  three" to "no one;one;two;three;"
    static public function trimDelimitedString($d, $text)
    {
		$parts = explode($d, $text);
		$v = '';
		foreach($parts as $part)
		{
			$part = trim($part);
			if (strlen($part) > 0)
				$v .= $part . $d; // put the trimmed part back followed by the delimiter
		}

		return $v;
	}

    static public function fixConjugations($record)
    {
		$rc = false;

		if (!isset($record))
		{
			$rc = true;
		}
		else if (isset($record->conjugations))
		{
			if (!isset($record->conjugations_search))
				$rc = true;
		}

		if (isset($record->conjugations_search))
		{
			if (!isset($record->conjugations))
				$rc = true;
			else if (!Str::startsWith($record->conjugations_search, ';'))
				$rc = true;
			else if (!Str::endsWith($record->conjugations_search, ';'))
				$rc = true;
		}

		return $rc;
	}

    static public function getConjugations($raw)
    {
		$rc['full'] = null;		// full conjugations
		$rc['search'] = null;	// conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')

		if (!isset($raw))
			return $rc; // nothing to do

		// quick check to see if it's raw or has already been formatted
		$parts = explode('|', $raw);
		if (count($parts) === 12 && Str::startsWith($parts[11], ';no '))
		{
			// already cleaned and formatted
			$rc['full'] = $raw;
			$rc['search'] = self::getConjugationsSearch($raw);
		}
		else
		{
			// looks raw so attempt to clean it
			// returns both 'full' and 'search'
			$rc = self::cleanConjugationsPasted($raw);
		}

		return $rc;
	}

	// make the search string either from a word array or from a full conjugation
    static public function getConjugationsSearch($words)
    {
		$rc = null;

		if (!is_array($words))
		{
			// make the words array first
			// raw conjugation looks like: |;mato;mata;matas;|mate;mate;matamos;|
			$tenses = [];
			$lines = explode('|', $words);
			foreach($lines as $line)
			{
				$parts = explode(';', $line);
				if (count($parts) > 0)
				{
					foreach($parts as $part)
					{
						// fix the reflexives
						if (Str::startsWith($part, ['me ', 'te ', 'se ', 'nos ', 'os ', 'no te ', 'no se ', 'no nos ', 'no os ', 'no se ']))
						{
							// chop off the reflexive prefix words, like 'me acuerdo', 'no se acuerden'
							$pieces = explode(' ', $part);
							if (count($pieces) > 2)
								$part = $pieces[2];
							else if (count($pieces) > 1)
								$part = $pieces[1];
							else if (count($pieces) > 0)
								$part = $pieces[0];
						}

						$tenses[] = $part;
					}
				}
			}

			$words = $tenses;
		}

		if (isset($words) && is_array($words))
		{
			$unique = [];
			foreach($words as $word)
			{
				if (strlen($word) > 0)
				{
					if (!in_array($word, $unique))
					{
						$unique[] = $word;
						$rc .= $word . ';';
					}
				}
			}

			$rc = ';' . $rc; // make it mysql searchable for exact match, like: ";voy;vea;veamos;ven;vamos;
		}

		return $rc;
	}

    static public function cleanConjugationsScraped($raw, $reflexive)
    {
		$rc['full'] = null;	  // full conjugations
		$rc['search'] = null; // conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')
		$conj = '';
		$search = '';

		if (!isset($raw))
			return null;

		$words = [];

		//$pos = strpos($raw, 'obtengo'); // 70574
		//dump($pos);
		//$pos = strpos($raw, 'play translation audio'); //
		//dump($pos);
		preg_match_all('/aria-label\=\"(.*?)\"/is', mb_substr($raw, 50000), $parts);

		// figure out where the start and end are
		$start = 0;
		$end = 0;

        //dd($parts); // scrapy
		$parts = $parts[1];

		$matches = count($parts);
		$participle = '';
		$progressivePrefix = $reflexive ? 'me estoy ' : 'estoy ';
		$participlePrefix = $reflexive ? 'me he ' : 'he ';
		if ($matches >= 150) // use the exact number so we can tell if we get unexpected results
		{
			// fix up the array first
			$words = [];
			$wordsPre = [];
			$word = '';
			foreach($parts as $part)
			{
			    $partialMatch = 'View the conjugation';
			    if (Str::startsWith($part, $partialMatch))
			    {
			        // fix the line that is specific to the verb looked up, looks like 'View the conjugation for to lose'
			        $part = $partialMatch;
			    }

				switch($part)
				{
					// get rid of all of the trash
					case 'Spanishdict Homepage':
					case 'SpanishDict Homepage':
					case 'SpanishDict logo':
					case 'more':
					case 'Menu':
					case 'Enter a Spanish verb':
					case 'Search':
					case 'play headword audio':
					case 'play translation audio':
					case 'Preterite':
					case 'Imperfect':
					case 'Present':
					case 'Subjunctive':
					case 'View the conjugation':
						break;
					default:
						$word = $part;
						$words[] = $word;
						break;
				}

				if (Str::startsWith($word, $progressivePrefix))
				{
					// save  the progressive form
					$wordsPre[] = mb_substr($word, strlen($progressivePrefix));
				}
				else if (Str::startsWith($word, $participlePrefix))
				{
					// save the past participle
					$wordsPre[] = mb_substr($word, strlen($participlePrefix));

					//  break because we've got everything we need
					break;
				}
			}

			// put the pre at the beginning
			$words = array_merge($wordsPre, $words);
			//dbg dump($words);

			// do a pass to create the search string
			$searchUnique = [];
			foreach($words as $word)
			{
				// remove the no from the imperatives
				if (Str::startsWith($word, 'no '))
				{
					$word = substr($word, strlen('no ')); // remove the "no"
				}

				// check unique array to only add a word once to the search string
				if (!in_array($word, $searchUnique))
				{
					$searchUnique[] = $word;
					$search .= $word . ';';
				}
			}

			//
			// save the conjugations
			//

			// participles
			$participleStem = trunc($words[1], 1);
			$offset = 5;
			$index = 0;
			$participleStem = trunc($words[1], 1);
			$conjugations[CONJ_PARTICIPLE] = ';'
				. $words[$index++] 				// abarcando
				. ';' . $words[$index++] 		// abarcado
				. ';' . $participleStem . 'os' 	// abarcados
				. ';' . $participleStem . 'a' 	// abarcada
				. ';' . $participleStem . 'as' 	// abarcadas
				. ';';
			$conj .= $conjugations[CONJ_PARTICIPLE]; // save the conjugation string

			// indicative
			$factor = 1;
			$conjugations[CONJ_IND_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_PRETERITE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRETERITE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_CONDITIONAL] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_CONDITIONAL]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_FUTURE]; // save the conjugation string

			// subjunctive
			$offset = 4;
			$factor = 1;
			$index += 26;
			$conjugations[CONJ_SUB_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT2] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT2]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_FUTURE]; // save the conjugation string

			// imperatives
			$offset = 2;
			$factor = 1;
			$index += 21;
			$conjugations[CONJ_IMP_AFFIRMATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' ;
			$conj .= '|' . $conjugations[CONJ_IMP_AFFIRMATIVE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IMP_NEGATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IMP_NEGATIVE]; // save the conjugation string

			//dbg dd($conjugations);
		}
		else
		{
			$msg = 'Error cleaning scraped conjugation: total results: ' . count($words);
			//dd($words);
			throw new \Exception($msg);
		}

		$rc['full'] = $conj;
		$rc['search'] = $search;

		return $rc;
	}

    static public function cleanConjugationsPasted($raw)
    {
		$rc['full'] = null;		// full conjugations
		$rc['search'] = null;	// conjugations list that can be searched (needed for reflexive conjugations like: 'nos acordamos')

		if (!isset($raw))
			return null;

		$words = [];
		$v = str_replace(';', ' ', $raw); 	// replace all ';' with spaces
		$v = alpha($v, true);			// clean it up
		$v = preg_replace('/[ *]/i', '|', $v);	// replace all spaces with '|'
		$parts = explode('|', $v);
		//dd($parts);
		$prefix = null;
		$search = null;
		$searchUnique = [];
		foreach($parts as $part)
		{
			$word = mb_strtolower(trim($part));
			if (strlen($word) > 0)
			{
				// the clean is specific to the verb conjugator in SpanishDict.com
				switch($word)
				{
					case 'participles':
					case 'are':
					case 'present':
					case '1':
					case '2':
					case 'affirmative':
					case 'conditional':
					case 'ellosellasuds':
					case 'future':
					case 'imperfect':
					case 'imperative':
					case 'in':
					case 'indicative':
					case 'irregularities':
					case 'negative':
					case 'nosotros':
					case 'past':
					case 'preterite':
					case 'red':
					case 'subjunctive':
					case 'ud':
					case 'uds':
					case 'vosotros':
					case 'yo':
					case 'tú':
					case 'élellaud':
					/*
					// for wiki, not done because the conjugations are in a different order
					case 'vos':
					case 'usted':
					case 'nosotras':
					case 'vosotras':
					case 'ustedes':
					case 'ellosellas':
					case 'élellaello':
					*/
						break;
					case 'no': // non reflexives with two words
						$prefix = $word; // we need the 'no'
						break;
					default:
					{
						// do this before the 'no' is added
						// check unique array to only add a word once to the search string
						if (!in_array($word, $searchUnique))
						{
							switch($word)
							{
								case 'me': // skip reflexive prefixes
								case 'te':
								case 'se':
								case 'nos':
								case 'os':
									break;
								default:
									$searchUnique[] = $word;
									$search .= $word . ';';
									break;
							}
						}

						if (isset($prefix)) // save the 'no' and use it
						{
							$word = $prefix . ' ' . $word;
							$prefix = null;
						}

						$words[] = $word;
						break;
					}
				}
			}
		}

		//dd($words);
		$search = isset($search) ? ';' . $search : null;

		$count = count($words);
		if ($count == 125) // it's reflexive so need more touch up
		{
			$parts = [];
			foreach($words as $word)
			{
				switch($word)
				{
					case 'me': // reflexive prefixes
					case 'te':
					case 'se':
					case 'nos':
					case 'os':
					case 'no te':
					case 'no se':
					case 'no nos':
					case 'no os':
						$prefix = $word;
						break;
					default:
					{
						if (isset($prefix)) // save the 'no' and use it
						{
							$word = $prefix . ' ' . $word;
							$prefix = null;
						}

						$parts[] = $word;
						break;
					}
				}
			}

			$words = $parts;
			//dd($parts);
		}

		$conj = null;
		$count = count($words);
		//dd($words);
		if ($count == 66) // total verb conjugations
		{
			//
			// save the conjugations
			//
			$conj = '';

			// participles
			$offset = 5;
			$index = 0;
			$participleStem = trunc($words[1], 1);
			$conjugations[CONJ_PARTICIPLE] = ';'
				. $words[$index++] 				// abarcando
				. ';' . $words[$index++] 		// abarcado
				. ';' . $participleStem . 'os' 	// abarcados
				. ';' . $participleStem . 'a' 	// abarcada
				. ';' . $participleStem . 'as' 	// abarcadas
				. ';';
			$conj .= $conjugations[CONJ_PARTICIPLE]; // save the conjugation string

			// indicative
			$factor = 1;
			$conjugations[CONJ_IND_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_PRETERITE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_PRETERITE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_CONDITIONAL] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_CONDITIONAL]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IND_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IND_FUTURE]; // save the conjugation string

			// subjunctive
			$offset = 4;
			$factor = 1;
			$index += 26;
			$conjugations[CONJ_SUB_PRESENT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_PRESENT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_IMPERFECT2] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_IMPERFECT2]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_SUB_FUTURE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_SUB_FUTURE]; // save the conjugation string

			// imperatives
			$offset = 2;
			$factor = 1;
			$index += 21;
			$conjugations[CONJ_IMP_AFFIRMATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' ;
			$conj .= '|' . $conjugations[CONJ_IMP_AFFIRMATIVE]; // save the conjugation string

			$factor = 1; $index++;
			$conjugations[CONJ_IMP_NEGATIVE] = ';' . $words[$index] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';' . $words[$index + ($offset * $factor++)] . ';';
			$conj .= '|' . $conjugations[CONJ_IMP_NEGATIVE]; // save the conjugation string

			//dd($conjugations);
		}
		else
		{
			$msg = 'Error cleaning pasted conjugation: total results: ' . count($words);
			//dd($words);
			throw new \Exception($msg);
		}

		$rc['full'] = $conj;
		$rc['search'] = $search;

		return $rc;
	}

    static public function getConjugationsFull($conj)
    {
		$conj = self::getConjugationsPretty($conj);

		if (isset($conj))
		{
		    $pronouns = ['yo', 'tu', 'usted', 'nosotros', 'vosotros', 'ustedes'];
		    $fullSize = count($pronouns);
		    foreach($conj as $record)
		    {
                // looks like: mato, mata, mata, matais, matamos, matan
                $tenses = [];
                $parts = explode(',', $record);
                if (count($parts) == $fullSize)
                {
                    foreach($parts as $key => $part)
                    {
                        $part = trim($part);
                        if (strlen($part) > 0)
                        {
                            $tenses[] = $pronouns[$key] . ' ' . $part;
                        }
                    }
                    $conj['tenses'][] = $tenses;
                }
                else
                {
                    // this will skip the first line which is the participle
                    if (array_key_exists('tenses', $conj))
                    {
                        $tenses = [];
                        if (count($parts) == ($fullSize - 1))
                        {
                            // the imperatives only have 5 tenses
                            foreach($parts as $key => $part)
                            {
                                $part = trim($part);
                                if (strlen($part) > 0)
                                {
                                    // start on 'tu'
                                    $tenses[] = $pronouns[$key + 1] . ' ' . $part;
                                }
                            }
                            $conj['tenses'][] = $tenses;
                        }
                    }
                }
			}
		}

		return $conj;
	}

    static public function getConjugationsPretty($conj)
    {
		$tenses = null;
		if (isset($conj))
		{
			// raw conjugation looks like: |;mato;mata;matas;|mate;mate;matamos;|
			$tenses = [];
			$parts = explode('|', $conj);
			foreach($parts as $part)
			{
				$part = trim($part);
				if (strlen($part) > 0)
				{
					$part = trim($part, ";");
					$part = str_replace(';', ', ', $part);
					$tenses[] = $part;
				}
			}
		}
		//dd($tenses);

/* output:
  0 => "siendo, sido"
  1 => "soy, eres, es, somos, sois, son"
  2 => "fui, fuiste, fue, fuimos, fuisteis, fueron"
  3 => "era, eras, era, éramos, erais, eran"
  4 => "sería, serías, sería, seríamos, seríais, serían"
  5 => "seré, serás, será, seremos, seréis, serán"
  6 => "sea, seas, sea, seamos, seáis, sean"
  7 => "fuera, fueras, fuera, fuéramos, fuerais, fueran"
  8 => "fuese, fueses, fuese, fuésemos, fueseis, fuesen"
  9 => "fuere, fueres, fuere, fuéremos, fuereis, fueren"
  10 => "sé, sea, seamos, sed, sean"
  11 => "no seas, no sea, no seamos, no seáis, no sean"
*/
		return $tenses;
	}

    static public function getFormsPretty($forms)
    {
		$v = preg_replace('/^;(.*);$/', "$1", $forms);
		$v = str_replace(';', ', ', $v);
		return $v;
	}

	// á é í ó
	static private $_verbEndings = [
		'ar' => [
			CONJ_PARTICIPLE 		=> ['ando', 'ado', 'ados', 'ada', 'adas'],
			CONJ_IND_PRESENT 		=> ['o', 'as', 'a', 'amos', 'áis', 'an'],
			CONJ_IND_PRETERITE 		=> ['é', 'aste', 'ó', 'amos', 'asteis', 'aron'],
			CONJ_IND_IMPERFECT 		=> ['aba', 'abas', 'aba', 'ábamos', 'abais', 'aban'],
			CONJ_IND_CONDITIONAL 	=> ['aría', 'arías', 'aría', 'aríamos', 'aríais', 'arían'],
			CONJ_IND_FUTURE 		=> ['aré', 'arás', 'ará', 'aremos', 'aréis', 'arán'],
			CONJ_SUB_PRESENT 		=> ['e', 'es', 'e', 'emos', 'éis', 'en'],
			CONJ_SUB_IMPERFECT 		=> ['ara', 'aras', 'ara', 'áramos', 'arais', 'aran'],
			CONJ_SUB_IMPERFECT2 	=> ['ase', 'ases', 'ase', 'ásemos', 'aseis', 'asen'],
			CONJ_SUB_FUTURE 		=> ['are', 'ares', 'are', 'áremos', 'areis', 'aren'],
			CONJ_IMP_AFFIRMATIVE 	=> ['a', 'e', 'emos', 'ad', 'en'],
			CONJ_IMP_NEGATIVE		=> ['es', 'e', 'emos', 'éis', 'en'],
		],
		'er' => [
			CONJ_PARTICIPLE 		=> ['iendo', 'ido', 'idos', 'ida', 'idas'],
			CONJ_IND_PRESENT 		=> ['o', 'es', 'e', 'emos', 'éis', 'en'],
			CONJ_IND_PRETERITE 		=> ['í', 'iste', 'ió', 'imos', 'isteis', 'ieron'],
			CONJ_IND_IMPERFECT 		=> ['ía', 'ías', 'ía', 'íamos', 'íais', 'ían'],
			CONJ_IND_CONDITIONAL 	=> ['ería', 'erías', 'ería', 'eríamos', 'eríais', 'erían'],
			CONJ_IND_FUTURE 		=> ['eré', 'erás', 'erá', 'eremos', 'eréis', 'erán'],
			CONJ_SUB_PRESENT 		=> ['a', 'as', 'a', 'amos', 'áis', 'an'],
			CONJ_SUB_IMPERFECT 		=> ['iera', 'ieras', 'iera', 'iéramos', 'ierais', 'ieran'],
			CONJ_SUB_IMPERFECT2 	=> ['iese', 'ieses', 'iese', 'iésemos', 'ieseis', 'iesen'],
			CONJ_SUB_FUTURE 		=> ['iere', 'ieres', 'iere', 'iéremos', 'iereis', 'ieren'],
			CONJ_IMP_AFFIRMATIVE 	=> ['e', 'a', 'amos', 'ed', 'an'],
			CONJ_IMP_NEGATIVE		=> ['as', 'a', 'amos', 'áis', 'an'],
		],
		'ir' => [
			CONJ_PARTICIPLE 		=> ['iendo', 'ido', 'idos', 'ida', 'idas'],
			CONJ_IND_PRESENT 		=> ['o', 'es', 'e', 'imos', 'ís', 'en'],
			CONJ_IND_PRETERITE 		=> ['í', 'iste', 'ió', 'imos', 'isteis', 'ieron'],
			CONJ_IND_IMPERFECT 		=> ['ía', 'ías', 'ía', 'íamos', 'íais', 'ían'],
			CONJ_IND_CONDITIONAL 	=> ['iría', 'irías', 'iría', 'iríamos', 'iríais', 'irían'],
			CONJ_IND_FUTURE 		=> ['iré', 'irás', 'irá', 'iremos', 'iréis', 'irán'],
			CONJ_SUB_PRESENT 		=> ['a', 'as', 'a', 'amos', 'áis', 'an'],
			CONJ_SUB_IMPERFECT 		=> ['iera', 'ieras', 'iera', 'iéramos', 'ierais', 'ieran'],
			CONJ_SUB_IMPERFECT2 	=> ['iese', 'ieses', 'iese', 'iésemos', 'ieseis', 'iesen'],
			CONJ_SUB_FUTURE 		=> ['iere', 'ieres', 'iere', 'iéremos', 'iereis', 'ieren'],
			CONJ_IMP_AFFIRMATIVE 	=> ['e', 'a', 'amos', 'id', 'an'],
			CONJ_IMP_NEGATIVE		=> ['as', 'a', 'amos', 'áis', 'an'],
		],
	];

	static private $_irregularVerbs = [
		'tropezar',
		'tener',
		'poder',
		'ser',
		'poner',
		'estar',
		'tropezar',
	];

	static private $_irregularVerbEndings = [
		'guir',
		'ger',
		'gir',
		'cer',
		'ucir',
	];

	static private $_regularVerbsAr = [ // needed for verbs that don't match a pattern
		'amar',
	];

	static private $_verbSuffixes = ['ar', 'er', 'ir'];
	static private $_verbReflexiveSuffixes = ['arse', 'erse', 'irse'];

    static public function canConjugate($word)
    {
		$rc = false;

		if (self::possibleVerb($word))
		{
			//todo: doing it the hard way, make a simple way to check if we can gen it
			$conj = self::conjugationsGen($word);
			$rc = isset($conj['records']);
		}

		return $rc;
	}

    static public function isIrregular($word)
    {
		$rc['irregular'] = false;
		$rc['reflexive'] = Str::endsWith($word, 'se');
		$rc['conj'] = [];
		$rc['error'] = null;

		$word = alphanum($word);
		$url = "https://www.spanishdict.com/conjugate/" . $word;

		$opciones = array(
		  'https'=>array(
			'method'=>"GET",
		  )
		);
		$contexto = stream_context_create($opciones);

		try
		{
			$raw = file_get_contents($url, false, $contexto);
			$pos = strpos($raw, 'Irregularities are in');
			if ($pos !== false)
			{
				$rc['irregular'] = true;
				$rc['conj'] = self::cleanConjugationsScraped($raw, $rc['reflexive']);
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error conjugating: ' . $word;

			if (strpos($e->getMessage(), '401') !== FALSE)
			{
				$msg .= ' - 401 Unauthorized';
			}

			Event::logException(LOG_MODEL, LOG_ACTION_TRANSLATE, $msg, null, $e->getMessage());
			//$result = $msg;
			//$rc['error'] = $msg;
			//$rc['found'] = $found;
			//return $rc;
		}

		return $rc;
	}

    static public function scrapeDefinition($word)
    {
		$rc = '';
		$word = alphanum($word);
		$url = "https://dle.rae.es/" . $word;

		$opciones = array(
		  'https'=>array(
			'method'=>"GET",
		  )
		);
		$contexto = stream_context_create($opciones);

		try
		{
			$raw = file_get_contents($url, false, $contexto);
			$prefix = '<meta name="description" content=';
			$pos = mb_strpos($raw, $prefix);
			if ($pos !== false)
			{
				$ix = $pos + (strlen($prefix));
				$raw = mb_substr($raw, $ix, 2000);
				$lines = explode(">", trim($raw));
				if (count($lines) > 0)
				{
					$lines = $lines[0];
					if (Str::startsWith($lines, "Versión electrónica"))
					{
						// this means word not found
						$rc = $word . ': not found';
					}
					else
					{
						// word found, clean up the definition

						// remove this: "Definición RAE de «competer» según el Diccionario de la lengua española: "
						$lines = preg_replace('/Definición RAE de.*española: /', '', $lines);
						// try the reflexive version of the word
						if (Str::endsWith($word, 'se'))
						{
							$w = trunc($word, 2); // remove the 'se'
							$remove = 'Definición RAE de «' . $w . '» según el Diccionario de la lengua española: ';
							$lines = str_replace($remove, '', $lines);
						}

						$lines = str_ireplace('‖ ', '', $lines);
						$lines = str_ireplace(' c.', ' ', $lines);
						$lines = str_ireplace(' f.', ' ', $lines);
						$lines = str_ireplace(' m.', ' ', $lines);
						$lines = str_ireplace(' p.', ' ', $lines);
						$lines = str_ireplace(' s.', ' ', $lines);
						$lines = str_ireplace(' t.', ' ', $lines);
						$lines = str_ireplace(' u.', ' ', $lines);
						$lines = str_ireplace(' y.', ' ', $lines);
						$lines = str_ireplace(' adj.', ' ', $lines);
						$lines = str_ireplace(' coloq.', ' ', $lines);
						$lines = str_ireplace(' cult.', ' ', $lines);
						$lines = str_ireplace(' desus.', ' ', $lines);
						$lines = str_ireplace(' gram.', ' ', $lines);
						$lines = str_ireplace(' intr.', ' ', $lines);
						$lines = str_ireplace(' prnl.', ' ', $lines);
						$lines = str_ireplace(' tr.', ' ', $lines);
						$lines = str_ireplace(' us.', ' ', $lines);
						$lines = str_ireplace('  ', ' ', $lines);
						$lines = str_ireplace('  ', ' ', $lines);

						$lines = trim($lines, '"');

						$rc = $lines;
					}
				}
			}

            // put each numbered item on a different line
			$rc = preg_replace('/\.[ ]*([0-9])/', "\r\n$1", $rc);
		}
		catch (\Exception $e)
		{
			$msg = 'Error scraping: ' . $word;

			if (strpos($e->getMessage(), '401') !== FALSE)
			{
				$msg .= ' - 401 Unauthorized';
			}

			Event::logException(LOG_MODEL, LOG_ACTION_TRANSLATE, $msg, null, $e->getMessage());
			$rc = $word . ': error getting definition, check event log';
		}

		return $rc;
	}

    static public function conjugationsGen($text)
    {
		$records = null;
		$rc['forms'] = null;
		$rc['formsPretty'] = null;
		$rc['records'] = null;
		$rc['status'] = null;

		$parts = null;
		$text = alphanum($text);
		if (isset($text)) // anything left?
		{
			// find the right pattern
			if (in_array($text, self::$_irregularVerbs))
			{
				// Case 1: matches specific verbs in irregular list
				$rc['status'] = 'irregular verb not implemented yet';
			}
			else if (Str::endsWith($text, self::$_irregularVerbEndings))
			{
				// Case 2: matches irregular pattern
				$rc['status'] = 'verb with irregular pattern not implemented yet';
			}
			// Case 3: ends with 'azar', 'ezar', 'ozar': aplazar, bostezar, gozar
			else if (strlen($text) > strlen('azar') && Str::endsWith($text, ['azar', 'ozar', 'ezar']))
			{
				$stem = 'zar';
				$middle = 'z';
				$middleIrregular = 'c';
				$endings = self::$_verbEndings['ar'];

				// get the regular conjugations
				$records = self::conjugate($text, $endings, $stem, $middle);

				// apply 4 irregular conjugations
				$root = $records['root'];

				for ($i = 0; $i < 6; $i++)
					$records[CONJ_SUB_PRESENT][$i] = $root . $middleIrregular . $endings[CONJ_SUB_PRESENT][$i];

				$records[CONJ_IND_PRETERITE][0] = $root . $middleIrregular . $endings[CONJ_IND_PRETERITE][0];
				$records[CONJ_IMP_AFFIRMATIVE][1] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][1];
				$records[CONJ_IMP_AFFIRMATIVE][2] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][2];
				$records[CONJ_IMP_AFFIRMATIVE][4] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][4];
			}
			// Case 3a: recalcar, remolcar
			else if (strlen($text) > strlen('calcar') && Str::endsWith($text, ['calcar', 'molcar']))
			{
				$stem = 'car';
				$middle = 'c';
				$middleIrregular = 'qu';
				$endings = self::$_verbEndings['ar'];

				// get the regular conjugations
				$records = self::conjugate($text, $endings, $stem, $middle);

				// apply 4 irregular conjugations
				$root = $records['root'];

				for ($i = 0; $i < 6; $i++)
					$records[CONJ_SUB_PRESENT][$i] = $root . $middleIrregular . $endings[CONJ_SUB_PRESENT][$i];

				$records[CONJ_IND_PRETERITE][0] = $root . $middleIrregular . $endings[CONJ_IND_PRETERITE][0];
				$records[CONJ_IMP_AFFIRMATIVE][1] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][1];
				$records[CONJ_IMP_AFFIRMATIVE][2] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][2];
				$records[CONJ_IMP_AFFIRMATIVE][4] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][4];
			}
			// Case 3b: 'o' stem change: volcar
			else if (false && strlen($text) > strlen('olcar') && Str::endsWith($text, ['olcar']))
			{
			//todo: same as??? 'olver', >>> revolver, revolcar, volver, resolver, devolver
			//todo: what about: 'over' >> mover: mueve / llover: llueve
				$stemTrimmer = 'car';
				$stemChange = 'ue';
				$middle = 'c';
				$middleIrregular = 'qu';
				$endings = self::$_verbEndings['ar'];

				// get the regular conjugations
				$records = self::conjugate($text, $endings, $stemTrimmer, $middle);

				// apply irregular conjugations
				$root = $records['root'];
				$rootIrregular = 'vuelc';
				// vol
				// vuelc

				//todo: NOT DONE YET!!! do the irregulars...
				for ($i = 0; $i < 6; $i++)
					$records[CONJ_SUB_PRESENT][$i] = $root . $middleIrregular . $endings[CONJ_SUB_PRESENT][$i];

				$records[CONJ_IND_PRETERITE][0] = $root . $middleIrregular . $endings[CONJ_IND_PRETERITE][0];
				$records[CONJ_IMP_AFFIRMATIVE][1] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][1];
				$records[CONJ_IMP_AFFIRMATIVE][2] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][2];
				$records[CONJ_IMP_AFFIRMATIVE][4] = $root . $middleIrregular . $endings[CONJ_IMP_AFFIRMATIVE][4];
			}
			// Case 4: vegular AR verbs that such as: acarrear, rodear, matar, llamar, tramar, increpar
			else if (strlen($text) > strlen('rear') && Str::endsWith($text, ['rear', 'dear', 'tar', 'amar', 'ivar', 'epar']))
			{
				$stem = 'ar';
				$middle = '';
				$endings = self::$_verbEndings[$stem];

				$records = self::conjugate($text, $endings, $stem, $middle);
			}
			// Case 5: regular AR verbs that don't match a pattern yet
			else if (in_array($text, self::$_regularVerbsAr))
			{
				$stem = 'ar';
				$middle = '';
				$endings = self::$_verbEndings[$stem];

				$records = self::conjugate($text, $endings, $stem, $middle);
			}
			// Case 6: conjugate all AR verbs as regular
			else if (Str::endsWith($text, 'ar'))
			{
				$stem = 'ar';
				$middle = '';
				$endings = self::$_verbEndings[$stem];

				$records = self::conjugate($text, $endings, $stem, $middle);
			}
			// Case 7: conjugate all ER verbs as regular
			else if (Str::endsWith($text, 'er'))
			{
				$stem = 'er';
				$middle = '';
				$endings = self::$_verbEndings[$stem];

				$records = self::conjugate($text, $endings, $stem, $middle);
			}
			// Case 8: conjugate all IR verbs as regular
			else if (Str::endsWith($text, 'ir'))
			{
				$stem = 'ir';
				$middle = '';
				$endings = self::$_verbEndings[$stem];

				$records = self::conjugate($text, $endings, $stem, $middle);
			}
			else
			{
				// verb case not handled yet
				$rc['status'] = 'verb pattern not implemented yet';
			}

			if (isset($records))
			{
				$rc['forms'] = self::getConjugationsGenString($records);
				$rc['formsPretty'] = self::getConjugationsGenString($records, /* pretty = */ true);
				$rc['records'] = $records;
			}
		}

		//dd($rc);

		return $rc;
	}

	//
	// used when we are gening our own conjugations (most regular only)
	//
    static private function conjugate($text, $endings, $stem, $middle)
    {
		$records = null;

		// crack it up to pieces
		$parts = preg_split('/(' . $stem . '$)/', $text, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		if (count($parts) > 0)
		{
			$root = $parts[0]; // verb root such as
			$records['root'] = $root;

			// participles
			$count = count($endings[CONJ_PARTICIPLE]);
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_PARTICIPLE][] = $root . $middle . $endings[CONJ_PARTICIPLE][$i];

			// indication
			$count = count($endings[CONJ_IND_PRESENT]);
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IND_PRESENT][] = $root . $middle . $endings[CONJ_IND_PRESENT][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IND_PRETERITE][] = $root . $middle . $endings[CONJ_IND_PRETERITE][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IND_IMPERFECT][] = $root . $middle . $endings[CONJ_IND_IMPERFECT][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IND_CONDITIONAL][] = $root . $middle . $endings[CONJ_IND_CONDITIONAL][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IND_FUTURE][] = $root . $middle . $endings[CONJ_IND_FUTURE][$i];

			// subjunctive
			$count = count($endings[CONJ_SUB_PRESENT]);
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_SUB_PRESENT][] = $root . $middle . $endings[CONJ_SUB_PRESENT][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_SUB_IMPERFECT][] = $root . $middle . $endings[CONJ_SUB_IMPERFECT][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_SUB_IMPERFECT2][] = $root . $middle . $endings[CONJ_SUB_IMPERFECT2][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_SUB_FUTURE][] = $root . $middle . $endings[CONJ_SUB_FUTURE][$i];

			// imperative
			$count = count($endings[CONJ_IMP_AFFIRMATIVE]);
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IMP_AFFIRMATIVE][] = $root . $middle . $endings[CONJ_IMP_AFFIRMATIVE][$i];
			for ($i = 0; $i < $count; $i++)
				$records[CONJ_IMP_NEGATIVE][] = 'no ' . $root . $middle . $endings[CONJ_IMP_NEGATIVE][$i];

			//dd($records);
		}

		return $records;
	}

    static private function getConjugationsGenString($records, $pretty = false)
    {
		$rc = '';

		foreach($records[CONJ_PARTICIPLE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IND_PRESENT] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IND_PRETERITE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IND_IMPERFECT] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IND_CONDITIONAL] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IND_FUTURE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_SUB_PRESENT] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_SUB_IMPERFECT] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_SUB_IMPERFECT2] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_SUB_FUTURE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IMP_AFFIRMATIVE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc .= '|;';

		foreach($records[CONJ_IMP_NEGATIVE] as $record)
			$rc .= $record . ';' . ($pretty ? ' ' : '');
		if (!$pretty) $rc = ';' . $rc;

		return $rc;
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
