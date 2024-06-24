<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Support\Arr;

use Auth;
use App;
use App\User;
use App\Gen\Spanish;
use DateTime;

class Quiz
{
    static private $_type = [
        'flashcards' => LESSON_TYPE_QUIZ_FLASHCARDS,
        'quiz' => LESSON_TYPE_QUIZ_MC,
    ];

    static private $_qnaEraFue = [
        ['key' => ['fui'],  'choices' => 'era, fui'],
        ['key' => ['eras', 'fuiste'],  'choices' => 'eras, fuiste'],
        ['key' => ['era', 'fue'],  'choices' => 'era, fue'],
        ['key' => ['eran', 'fueron'],  'choices' => 'eran, fueron'],
        ['key' => ['eramos', 'fuimos'],  'choices' => 'eramos, fuimos'],

        ['key' => ['estuve'],  'choices' => 'estuve, estaba'],
        ['key' => ['estuviste', 'estabas'],  'choices' => 'estuviste, estabas'],
        ['key' => ['estuvo', 'estaba'],  'choices' => 'estuvo, estaba'],
        ['key' => ['estuvieron', 'estaban'],  'choices' => 'estuvieron, estaban'],
        ['key' => ['estuvimos', 'estabamos'],  'choices' => 'estuvimos, estabamos'],

        ['key' => ['tuve'],  'choices' => 'tuve, tenía'],
        ['key' => ['tuviste', 'tenías'],  'choices' => 'tuviste, tenías'],
        ['key' => ['tuvo', 'tenía'],  'choices' => 'tuvo, tenía'],
        ['key' => ['tuvieron', 'tenían'],  'choices' => 'tuvieron, tenían'],
        ['key' => ['tuvimos', 'teníamos'],  'choices' => 'tuvimos, teníamos'],

        ['key' => ['hice'],  'choices' => 'hice, hacía'],
        ['key' => ['hiciste', 'hacías'],  'choices' => 'hiciste, hacías'],
        ['key' => ['hizo', 'hacía'],  'choices' => 'hizo, hacía'],
        ['key' => ['hicieron', 'hacían'],  'choices' => 'hicieron, hacían'],
        ['key' => ['hicimos', 'hacíamos'],  'choices' => 'hicimos, hacíamos'],

        ['key' => ['pude'],  'choices' => 'pude, podía'],
        ['key' => ['pudiste', 'podías'],  'choices' => 'pudiste, podías'],
        ['key' => ['pudo', 'podía'],  'choices' => 'pudo, podía'],
        ['key' => ['pudieron', 'podían'],  'choices' => 'pudieron, podían'],
        ['key' => ['pudimos', 'podíamos'],  'choices' => 'pudimos, podíamos'],

    ];

    static private $_qnaPorPara = [
        ['key' => ['por', 'para'],  'choices' => 'por, para']
    ];

    static public function getQnaParms($qnaType)
    {
        $rc = null;

        switch($qnaType)
        {
            case 'por':
                $rc = self::$_qnaPorPara;
                break;
            case 'era':
                $rc = self::$_qnaEraFue;
                break;
            default:
                break;
        }

        return $rc;
    }

	// this version puts the answer options into a separate cell
	static public function makeReviewQuiz($quiz)
    {
		$quizNew = [];
		$answers = [];
		$max = 0;

		$randomOptions = 5;
		$cnt = 0;
		foreach($quiz as $record)
		{
			$options = [];

			if (true)
			{
				//sbw
			}
			else if (preg_match('#\[(.*)\]#is', $record['q']))
			{
				// there is already an answer so it will be handled in formatMc1
			}
			else
			{
				//
				// get random answers from other questions
				//
				$max = count($quiz) - 1; // max question index
				if ($max > 0)
				{
					// using 100 just so it's not infinite, only goes until three unique options are picked
					$pos = rand(0, $randomOptions - 1); // position of the correct answer
					for ($i = 0; $i < 100 && count($options) < $randomOptions; $i++)
					{
						// pick three random options
						$rnd = rand(0, $max);	// answer from other random question
						$option = $quiz[$rnd];
						//dd($option['a']);

						// not the current question AND has answer text AND answer not used yet
						if ($option['id'] != $record['id'] && strlen($option['a']) > 0 && !array_key_exists($option['a'], $options))
						{
							if ($pos == count($options))
							{
								// add in the real answer at the random position
								$options[$record['a']] = $record['a'];
							}

							$options[$option['a']] = $option['a'];

							if ($pos == count($options))
							{
								// add in the real answer at the random position
								$options[$record['a']] = $record['a'];
							}
						}
						else
						{
							//dump('duplicate: ' . $option);
						}
					}

					$quizNew[$cnt]['options'] = $options;
				}

				$quizNew[$cnt]['q'] = $record['q'];
				$quizNew[$cnt]['a'] = $record['a'];
				$quizNew[$cnt]['id'] = $record['id'];
				$quizNew[$cnt]['ix'] = $record['id'];
			}


			//dump($quizNew[$cnt]);

			$cnt++;
		}

		if ($max > 0)
		{
			$quizNew = self::addAnswerButtons($quizNew);
		}

		//dd($quizNew);
		return $quizNew;
	}

	//
	// this is the new way, updated for review.js
	//
	static public function makeFlashcards($questions, $answers)
    {
		$records = [];

        // split lines
		$questions = preg_split('/\r\n/', $questions, -1, PREG_SPLIT_NO_EMPTY);
		$answers = preg_split('/\r\n/', $answers, -1, PREG_SPLIT_NO_EMPTY);
		$qna = [];
		$cnt = 0;
		foreach($questions as $record)
		{
			$line = strip_tags($record);
            $q = trim($line);
            if (isset($q) && strlen($q) > 0)
            {
                $qna[$cnt]['q'] = $q;
                $qna[$cnt]['a'] = (count($answers) > $cnt) ? trim(strip_tags($answers[$cnt])) : '';
                $qna[$cnt]['id'] = $cnt;
                $qna[$cnt]['ix'] = $cnt; // this will be the button id, just needs to be unique

                $qna[$cnt]['choices'] = null;
                $qna[$cnt]['definition'] = 'false';
                $qna[$cnt]['translation'] = '';
                $qna[$cnt]['extra'] = '';
                $qna[$cnt]['options'] = '';

                $cnt++;
            }
		}

		return $qna;
	}

	//
	// make qna from article text
	// input: article text
	// output: lines formatted for lesson mc quiz
	static public function mineQna($text, $parms)
    {
        $records = [];

        if (Spanish::on()) // qna mining only implemented in ES so far
        {
            // accepts either an array or text
            $sentences = (is_array($text)) ? $text : Spanish::getSentences($text);
            $text = '';

            foreach($parms as $parm)
            {
                $key = $parm['key'];

                //dump($sentences);
                foreach($sentences as $record)
                {
                    $line = '';
                    if (Str::contains(strtolower($record), $key))
                    {
                        $words = explode(' ', $record);
                        $found = null;
                        $line = '';
                        foreach($words as $word)
                        {
                            if (empty($found) && matchExact($word, $key))
                            {
                                $line .= '[' . (startsWithUpper($word) ? ucwords($parm['choices']) : $parm['choices']) . ']';
                                $line .= ' ';
                                $found = $word;
                            }
                            else
                            {
                                $line .= $word . ' '; // save in case there is a match later
                            }
                        }

                        if (isset($found))
                        {
                            $line .= '- ' . $found; // put in the right answer
                            $records[] = $line;
                            $text .= '<p>' . $line . "</p>\r\n";
                        }
                    }
                }
            }
        }

        //dump($records); // only used for debugging
		return ['text' => $text, 'count' => count($records)];
	}

    static public function makeQnaFromText($text)
    {
		$records = [];
		// chop it into lines by using the <p>'s
	    $text = str_replace(['&ndash;', '&nbsp;'], ['-', ' '], $text);
		preg_match_all('#<p>(.*?)</p>#is', $text, $records, PREG_SET_ORDER);

        return self::makeQna($records);
    }

	//
	// this is the new way to multiple choice qna, updated for review.js
	//
	static public function makeQna($records)
    {
		$qna = [];
		$cnt = 0;
		$delim = ' - ';

		foreach($records as $record)
		{
			$line = $record[1];
			$line = strip_tags($line);
			$parts = explode($delim, $line); // split the line into q and a, looks like: "question text - correct answer text"

			if (count($parts) > 1)
			{
				$q = trim($parts[0]);

				// see if there are embedded answer choices such "El [es, está] alto."
    			preg_match('#\[.*\]#is', $q, $choices);
                if (count($choices) > 0)
                {
                    $choices = str_replace(', ', '|', trim($choices[0], '[]')); // leaves in a string separated by '|'
                }
                else
                {
                    $choices = null;
                }

                // now replace choices with a big blank
                $q = self::blankChoices($q);

				$qna[$cnt]['q'] = $q;
				$qna[$cnt]['a'] = array_key_exists(1, $parts) ? trim($parts[1]) : null;
				$qna[$cnt]['choices'] = $choices;
				$qna[$cnt]['definition'] = 'false';
				$qna[$cnt]['translation'] = '';
				$qna[$cnt]['extra'] = '';
				$qna[$cnt]['rule'] = null;
				$qna[$cnt]['id'] = $cnt;
				$qna[$cnt]['ix'] = $cnt; // this will be the button id, just needs to be unique
				$qna[$cnt]['options'] = '';

				if (!isset($qna[$cnt]['a']))
					throw new \Exception('parse error: ' . $q);

			    $cnt++;
			}
		}

		return $qna;
	}

	static public function blankChoices($text)
    {
        return preg_replace('/\[.*\]/i', '__________', $text);
    }

	// creates buttons for each answer option
	// and puts them into the question
	static private function addAnswerButtons($quiz)
    {
		$quizNew = [];
		$i = 0;

		foreach($quiz as $record)
		{
			$a = trim($record['a']);
			$q = trim($record['q']);
			$id = $record['id'];

			if (strlen($a) > 0)
			{
				$buttonId = 0;
				if (array_key_exists('options', $record) && is_array($record['options']))
				{
					// use the options
					$options = $record['options'];

					//
					// create a button for each answer option
					//
					$buttons = '';
					foreach($options as $m)
					{
						// mark the correct button so it can be styled during the quiz
						$buttonClass = ($m == $a) ? 'btn-right' : 'btn-wrong';

						$buttons .= self::formatButton($m, $buttonId++, $buttonClass);
					}

					// put the formatted info back into the quiz
					$quizNew[] = [
						'q' => $q,
						'a' => $a,
						'options' => $buttons,
						'id' => $record['id'],
        				'ix' => $record['id'],
					];
				}
			}
		}

		//dd($quizNew);
		return $quizNew;
	}

	static public function formatButton($text, $id, $class)
    {
		$button = '<div><button id="'
            . $id
            //new way. '" onclick="checkAnswerFromButtonClick(event)"'
            . '" onclick="checkAnswerMc1('
		    . $id . ')" class="btn btn-primary btn-quiz-mc3 '
		    . ' class="btn btn-primary btn-quiz-mc3 '
		    . $class . '">'
		    . $text
		    . '</button></div>';

		//dump($button);

		return $button;
	}

	static public function getCommaSeparatedAnswers($text)
    {
		$words = '';
		$array = [];

		// pattern looks like: "The words [am, is, are] in the sentence."
		preg_match_all('#\[(.*)\]#is', $text, $words, PREG_SET_ORDER);

		// if answers not found, set it to ''
		$words = (count($words) > 0 && count($words[0]) > 1) ? $words[0][1] : '';

		if (strlen($words) > 0)
		{
			$raw = explode(',', $words); // extract the comma-separated words

			if (is_array($raw) && count($raw) > 0)
			{
				foreach($raw as $word)
				{
					$array[] = trim($word);
				}
			}
		}

		return $array;
	}

    // convert the url parameter (1 or 2) to the quiz type
	static public function getReviewTypeFlag($reviewType)
	{
	    $type = LESSON_TYPE_NOTSET;

        if (is_int($reviewType))
        {
            // it's already set so just return it.
            switch($reviewType)
            {
                case 1:
                    $type = LESSON_TYPE_QUIZ_FLASHCARDS;
                    break;
                case 2:
                    $type = LESSON_TYPE_QUIZ_MC;
                    break;
                default:
                    break;
            }
        }
        else
        {
            $reviewType = alphaNum($reviewType);
            if (array_key_exists($reviewType, self::$_type))
                $type = self::$_type[$reviewType];
        }

		return $type;
    }

	static public function isQuiz($reviewType)
	{
        // url review type looks like 'quiz' or 'flashcards', so get the type_flag and check it
	    return (self::getReviewTypeFlag($reviewType) > LESSON_TYPE_NOTSET);
    }

    // reviewType can either be a string or an int of type REVIEW_TYPE_*
	static public function getSettings($reviewType)
	{
		$loadJs = 'qnaReview.js';
		$view = 'shared.review';
		$programName = 'Quiz';

		$quizText = [
			'Round' => 'Round',
			'Correct' => 'Correct',
			'TypeAnswers' => 'Type the Answer',
			'Wrong' => 'Wrong',
			'of' => 'of',
		];

		// options
		$options = self::getOptionArray('font-size="150%"');
		$options['prompt'] = Arr::get($options, 'prompt', 'Select the correct answer');
		$options['prompt-reverse'] = Arr::get($options, 'prompt-reverse', 'Select the correct question');
		$options['question-count'] = Arr::get($options, 'question-count', 0);
		$options['font-size'] = Arr::get($options, 'font-size', '120%');

        $type = self::getReviewTypeFlag($reviewType);
		if ($type == LESSON_TYPE_QUIZ_MC)
		{
			// use the default settings above
		}
		else if ($type == LESSON_TYPE_QUIZ_FLASHCARDS)
		{
			$options['prompt'] = 'Tap or click to continue';
			$view = 'shared.qna';
			$loadJs = 'qnaBoth.js';
			$programName = 'Flashcards';
		}
		else if ($type == LESSON_TYPE_QUIZ_WHEELOFFORTUNE)
		{
			$options['prompt'] = 'Tap or click to continue';
			$view = 'shared.wheeloffortune';
			$loadJs = 'qnaFlashcards.js';
		}

		$rc['options'] = $options;
		$rc['loadJs'] = $loadJs;
		$rc['view'] = $view;
		$rc['quizText'] = $quizText;
        $rc['programName'] = $programName;

        //dd($rc);

		return $rc;
	}

    static public function getOptionArray($options)
    {
		$arr = [];

		// prompt="Select the correct capital"; reverse-prompt="Select the country for the capital"; question-count=20; text-size="medium";
		$key = '/([a-zA-Z\-^=]*)=\"([^\"]*)/i';
		if (preg_match_all($key, $options, $matches))
		{
			if (count($matches) > 2)
			{
				foreach($matches[1] as $key => $data)
				{
					$arr[$data] = $matches[2][$key];
				}
			}
		}

        return $arr;
    }

    static public function getOption($options, $key)
    {
		$r = '';

		// prompt="Select the correct capital"; reverse-prompt="Select the country for the capital"; question-count=20; text-size="medium";
		$key = "/" . $key . '=\"([^\"]*)/';
		if (preg_match($key, $options, $matches))
		{
			if (count($matches) > 1)
			{
				$r = $matches[1];
			}
		}

        return $r;
    }

}
