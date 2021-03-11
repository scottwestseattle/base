<?php

use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

use App\User;

if (!function_exists('obj_count')) {
    function obj_count($obj)
    {
        if (isset($obj))
            return count($obj);
        else
            return 0;
    }
}

if (!function_exists('ipAddress')) {
    function ipAddress()
    {
        $ip_address = null;

        // normal
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }
        // proxy
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        // remote address
        else
        {
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }

        return $ip_address;
    }
}

if (!function_exists('getVisitorInfo')) {
    function getVisitorInfo()
    {
        $rc = [];

        //
        // get visitor info
        //
        $rc['ip'] = ipAddress();

        $rc['host'] = gethostbyaddr($_SERVER['REMOTE_ADDR']);

        $rc['hash'] = $rc['ip'] . ':' . $rc['host'];

        $rc['referrer'] = null;
        if (array_key_exists("HTTP_REFERER", $_SERVER))
        {
            $rc['referrer'] = $_SERVER["HTTP_REFERER"];
        }

        $rc['userAgent'] = null;
        if (array_key_exists("HTTP_USER_AGENT", $_SERVER))
        {
            $rc['userAgent'] = $_SERVER["HTTP_USER_AGENT"];
            $rc['hash'] .= ':' . $rc['userAgent'];
        }

        $rc['hash'] = hashQuick($rc['hash'], DEF_HASH_LENGTH);

        return $rc;
    }
}

if (!function_exists('hashQuick')) {
    function hashQuick($text, $length = PHP_INT_MAX)
    {
        $hash = hash('md2', $text);
        return substr($hash, 0, $length);
    }
}

if (!function_exists('isAdmin')) {
    function isAdmin()
    {
        return User::isAdmin();
    }
}

if (!function_exists('flash')) {
    function flash($level, $content)
    {
        request()->session()->flash('message.level', $level);
        request()->session()->flash('message.content', $content);
    }
}

if (!function_exists('referrer')) {
    function referrer()
    {
        $rc['input'] = '';
        $rc['url'] = '';

        if (isset($_SERVER["HTTP_REFERER"]))
        {
            $rc['url'] = $_SERVER["HTTP_REFERER"];
            $rc['input'] = new HtmlString("<input name='referrer' type='hidden' value='" . $rc['url'] . "' />");
        }

        return $rc;
    }
}

if (!function_exists('logWarning')) {

	function logWarning($msg, $flash = null, $parms = null)
	{
		logFlash('warning', $msg, $flash, $parms);
	}

	function logException($msg, $exception, $flash = null, $parms = null)
	{
		$msg = $exception . ', ' . $msg;
		logFlash('error', $msg, $flash, $parms);
	}

	function logExceptionEx($class, $function, $exception, $flash = null, $parms = null)
	{
		$msg = $exception . ', ' . $class . ':' . $function;
		logFlash('error', $msg, $flash, $parms);
	}

	function logError($msg, $flash = null, $parms = null)
	{
		logFlash('error', $msg, $flash, $parms);
	}

	function logInfo($msg, $flash = null, $parms = null)
	{
		logFlash('info', $msg, $flash, $parms);
	}

	function logEmergency($msg, $flash = null, $parms = null)
	{
		logFlash('emergency', $msg, $flash, $parms);
	}

	function logFlash($type, $msg, $flash, $parms = null)
	{
		$info = ['user id' => Auth::id(), 'ip' => ipAddress()];

		if (isset($parms))
			$info = array_merge($info, $parms);

		if (isset($flash))
			$msg .= ' - ' . $flash;

		switch($type)
		{
			case 'warning':
				$flashType = $type;
				Log::warning($msg, $info);
				break;
			case 'info':
				$flashType = 'success';
				Log::info($msg, $info);
				break;
			case 'error':
				$flashType = 'danger';
				Log::error($msg, $info);
				break;
			case 'emergency':
				$flashType = 'danger';
				Log::emergency($msg, $info);
				break;
			default:
				$flashType = 'danger';
				Log::error($msg, $info);
				break;
		}

		if (isset($flash))
			flash($flashType, $flash);
	}
}

if (!function_exists('domainName')) {
	function domainName()
	{
		$v = null;

		if (array_key_exists("SERVER_NAME", $_SERVER))
		{
			$v = strtolower($_SERVER["SERVER_NAME"]);

			// trim the duba duba duba
			if (Str::startsWith($v, 'www.'))
				$v = substr($v, 4);
		}

		return $v;
	}
}

if (!function_exists('appName')) {
	function appName()
	{
		$v = domainName();

		return $v;
	}
}

if (!function_exists('appNamePretty')) {
	function appNamePretty()
	{
		$key = 'APP_NAME_' . domainName();
		$v = env($key, 'App Name');

		return $v;
	}
}

if (!function_exists('uniqueToken')) {
	function uniqueToken()
	{
		return md5(uniqid());
	}
}

if (!function_exists('getTimestampFuture')) {
	function getTimestampFuture($minutes)
	{
		$timestamp = date("Y-m-d H:i:s", strtotime('+' . intval($minutes) . ' minutes'));
		return $timestamp;
	}
}

if (!function_exists('timestamp')) {
	function timestamp()
	{
		return date("Y-m-d H:i:s");
	}
}

if (!function_exists('alpha')) {
	function alpha($text)
	{
		if (isset($text))
		{
			$text = preg_replace("/\s+/", ' ', $text); // change all whitespace to one space

			$base = Config::get('constants.regex.alpha');
			$accents = Config::get('constants.characters.accents');
			$match = $base . $accents;

			$text = preg_replace("/[^" . $match . "]+/", "", trim($text));
		}

		return $text;
	}
}

if (!function_exists('alphanum')) {
	function alphanum($text, $strict = false)
	{
		if (isset($text))
		{
			// replace all chars except alphanums, some punctuation, accent chars, and whitespace
			$base = Config::get('constants.regex.alphanum');
			$accents = Config::get('constants.characters.accents');

			$match = $base . $accents;
			if (!$strict)
			{
				$punct =  Config::get('constants.characters.safe_punctuation');
				$match .= $punct;
			}

			$text = preg_replace("/[^" . $match . "]+/", "", trim($text));
		}

		return $text;
	}
}

if (!function_exists('alphanumpunct')) {
	function alphanumpunct($text)
	{
		return alphanum($text);
	}
}

if (!function_exists('alphanumHarsh')) {
	function alphanumHarsh($text)
	{
	    $debug = false;
	    //$debug = true;
	    $clean = null;

		if (isset($text))
		{
			// replace all chars except alphanums, some punctuation, accent chars, and whitespace
            $clean = preg_replace("/[^[:alnum:] '’“”\",.()?¿¡!@;:»«\-\r\n]/u", '', $text);
            $chino = '\。\，';

            if ($debug)
            {
                $c1 = strlen($text);
                $c2 = strlen($clean);
                dump('text: ' . $c1 . ' / ' . 'clean: ' . $c2);
                dump('texto: ' . $text);
                dd('clean: ' . $clean);
            }
		}

		return $clean;
	}
}

if (!function_exists('isExpired')) {
	function isExpired($sDate)
	{
		$rc = false;

		if (isset($sDate))
		{
			try
			{
				$expiration = new DateTime($sDate);
				$now = new DateTime('NOW');
				$rc = ($now <= $expiration);
			}
			catch(\Exception $e)
			{
				logException(__FUNCTION__, $e->getMessage(), 'Error checking expired date', ['date' => $sDate]);
				logEmergency(__FUNCTION__, 'Error checking expired date');
			}
		}

		return !$rc;
	}
}

if (!function_exists('getConstant')) {
	function getConstant($name)
	{
		return(Config::get('constants.' . $name));
	}
}

if (!function_exists('trimNull')) {
	// if string has non-whitespace chars, then it gets trimmed, otherwise gets set to null
	function trimNull($text, $alphanum = false)
	{
		if (isset($text))
		{
			$text = trim($text);

			if ($alphanum)
				$text = alphanum($text);

			if (strlen($text) === 0)
				$text = null;
		}

		return $text;
	}
}


if (!function_exists('copyDirty')) {
    function copyDirty($to, $from, &$isDirty, &$updates = null, $alphanum = false)
    {
		$from = trimNull($from, $alphanum);
		$to = trimNull($to, $alphanum);

		if ($from != $to)
		{
			$isDirty = true;

			if (!isset($updates) || strlen($updates) == 0)
				$updates = '';

			$updates .= '|';

			if (strlen($to) == 0)
				$updates .= '(empty)';
			else
				$updates .= $to;

			$updates .= '|';

			if (strlen($from) == 0)
				$updates .= '(empty)';
			else
				$updates .= $from;

			$updates .= '|  ';
		}

		return $from;
	}
}

if (!function_exists('createPermalink')) {
    function createPermalink($title, $hash = null, $date = null)
    {
		$v = null;

		if (blank($hash))
		{
		    $hash = microtime(true);
		}

		if (isset($title))
		{
		    $v .= convertAccentChars($title);
		}

		if (isset($date))
		{
			$v .= '-' . $date;
		}

		$v = preg_replace('/[^\da-z ]/i', ' ', $v); // replace all non-alphanums with spaces
		$v = preg_replace('/\s+/', "-", $v);		// replace spaces with dashes
		$v = strtolower($v);						// make all lc
		$v = preg_replace('/^-+/', '', $v);         // remove any leading '-'
		$v = preg_replace('/-+$/', '', $v);         // remove any trailing '-'

        // make the permalink unique by adding a hashed string at the end
        // hash the hash key (timestamp) and append the first 6 chars to the permalink
        $v .= '-' . hashQuick($hash, 6);

		$v = trimNull($v);							// trim it or null it

		return $v;
	}
}

if (!function_exists('lurl')) {
    // create localized url, such as: /es/about
    function lurl($route)
    {
        return '/' . app()->getLocale() . '/' . $route;
    }
}

if (!function_exists('getFilesVisible')) {
    function getFilesVisible($path, $wildcard = false, $includeFolders = false)
    {
		$files = [];

		if (is_dir($path))
		{
            $all = scandir($path);

            foreach($all as $file)
            {
                if (Str::startsWith($file, '.'))
                {
                    // skip the folders and hidden files
                }
                else if (!$includeFolders && is_dir($path . '/' . $file))
                {
                    // skip folders
                }
                else
                {
                    if ($wildcard !== false)
                    {
                         if (strpos($file, $wildcard) !== false)
                            $files[] = $file;
                    }
                    else
                    {
                        $files[] = $file;
                    }
                }
            }
		}

        return $files;
    }
}

if (!function_exists('convertAccentChars')) {
    function convertAccentChars($v)
    {
        //
        // replace accent / special characters one by one
        //
		//$v = preg_replace("/ /", "-", $v);
        $v = preg_replace("/ñ/ui", "n", $v);
        $v = preg_replace("/[ÀÁÄÂàáâäã]/ui", "a", $v);
        $v = preg_replace("/[ÉÈËÊèéêë]/ui", "e", $v);
        $v = preg_replace("/[ÍÌÏÎìíîï]/ui", "i", $v);
        $v = preg_replace("/[ÓÒÖÔòóôöõø]/ui", 'o', $v);
        $v = preg_replace("/[ÙÚÜÛùúûü]/ui", "u", $v);
        $v = preg_replace("/ç/ui", "c", $v);
        $v = preg_replace("/Ÿÿ/ui", "y", $v);

        return $v;
    }
}

if (!function_exists('getSpeechLanguageShort')) {
	function getSpeechLanguageShort($id)
	{
        return getSpeechLanguage($id)['code'];
    }
}

if (!function_exists('getSpeechLanguage')) {
	function getSpeechLanguage($id)
	{
        $languageFlags = [
            LANGUAGE_DE => 'de-DE',
            LANGUAGE_EN => 'en-EN',
            LANGUAGE_ES => 'es-ES',
            LANGUAGE_FR => 'fr-FR',
            LANGUAGE_IT => 'it-IT',
            LANGUAGE_PT => 'pt-PT',
            LANGUAGE_RU => 'ru-RU',
            LANGUAGE_ZH => 'zh-ZH',
            LANGUAGE_KO => 'ko-KO',
            //LANGUAGE_ => '',
        ];

        $languageFlagsAlt = [
            LANGUAGE_DE => 'ger-GER',
            LANGUAGE_EN => 'eng-GBR',
            LANGUAGE_ES => 'spa-ESP',
            LANGUAGE_FR => 'fra-FRA',
            LANGUAGE_IT => 'ita-ITA',
            LANGUAGE_PT => 'por-POR',
            LANGUAGE_RU => 'rus-RUS',
            LANGUAGE_ZH => 'chi-CHI',
            LANGUAGE_KO => 'kor-KOR',
            //LANGUAGE_ => '',
        ];

        $rc['short'] = 'en-EN';
        $rc['long'] = 'eng-GBR';

        if (array_key_exists($id, $languageFlags))
        {
            $rc['short'] = $languageFlags[$id];
        }

        if (array_key_exists($id, $languageFlagsAlt))
        {
            $rc['long'] = $languageFlagsAlt[$id];
        }

        // first two letters are the language code
        $rc['code'] = substr($rc['short'], 0, 2);

        // return the id too
        $rc['id'] = $id;

        $rc['name'] = getLanguageName($id);

	    return $rc;
	}
}

if (!function_exists('getLanguageOptions')) {
	function getLanguageOptions($includeAll = false)
	{
        $languages = [
            LANGUAGE_EN => 'English',
            LANGUAGE_ES => 'Spanish',
            LANGUAGE_ZH => 'Chinese',
            LANGUAGE_RU => 'Russian',
            LANGUAGE_FR => 'French',
            LANGUAGE_IT => 'Italian',
            LANGUAGE_DE => 'German',
            LANGUAGE_KO => 'Korean',
        ];

	    if ($includeAll)
            $languages[LANGUAGE_ALL] = 'All';

        return $languages;
    }
}

if (!function_exists('getLanguageName')) {
	function getLanguageName($languageFlag)
	{
	    return isset($languageFlag) ? getLanguageOptions(true)[$languageFlag] : '';
	}
}

if (!function_exists('getOrSetString')) {
	function getOrSetString($text, $default)
    {
		// if text not set or blank then return the default
		return ((isset($text) && strlen($text) > 0) ? $text : $default);
	}
}

if (!function_exists('timestamp2date')) {
    function timestamp2date($timestamp)
    {
		return translateDate($timestamp);
	}
}

if (!function_exists('translateDate')) {
    function translateDate($date)
    {
		$dateFormat = "%B %e, %Y";

		if (App::getLocale() == 'es')
		{
			$dateFormat = "%e " . __('ui.of') . ' ' . __('ui.' . strftime("%B", strtotime($date))) . ", %Y";

		}
		else if (App::getLocale() == 'zh')
		{
			// 2019年12月25日
			$dateFormat = "%Y" . __('ui.year') . "%m" . __('ui.month') . "%e" . __('ui.date');
		}
		else
		{
		}

		$date = strftime($dateFormat, strtotime($date));

		return $date;
	}
}

if (!function_exists('getController')) {
    function getController()
    {
        $c = app('request')->route()->getAction();
        $c = class_basename($c['controller']);
        $c = explode('@', $c);
        $c = count($c) > 0 ? $c[0] : '';

        return $c;
    }
}

if (!function_exists('intOrNull')) {
    function intOrNull($n)
    {
    	// if n isn't null then return intval
		if (isset($n))
			$n = intval($n);

		return($n);
	}
}

if (!function_exists('getSentences')) {
	function getSentences($string, $count = PHP_INT_MAX)
	{
	    $string = trim($string);
	    $rc = null;

        if (isset($string))
        {
            $s = splitSentences($string);
            if (isset($s))
            {
                foreach($s as $key => $value)
                {
                    if ($key >= $count)
                        break;

                    if (isset($rc))
                        $rc .= "\r\n";

                    $rc .= $value;
                }
            }
        }

        return $rc;
    }
}

if (!function_exists('getWord')) {
	function getWord($string, $index)
	{
	    $string = trim($string);
	    $rc = null;

        if (isset($string))
        {
            $s = explode(' ', $string);
            if (isset($s) && count($s) >= $index)
            {
                $rc = $s[$index - 1];
            }
        }

        return $rc;
    }
}

if (!function_exists('splitSentences')) {
	function splitSentences($string)
	{
		$sentences = null;

		if (isset($string))
		{
			$pattern = '/[\r\n]/';
			$parts = preg_split($pattern, $string);

			foreach($parts as $part)
			{
				$part = trim($part);
				if (strlen($part) > 0)
					$sentences[] = ucfirst($part); // self::appendIfMissing(ucfirst($part), '.');
			}
		}

		return $sentences;
	}
}

if (!function_exists('trunc')) {
	function trunc($string, $length, $ellipsis = null)
	{
	    $length = intval($length);
	    $rc = trim($string);
		$l = mb_strlen($rc);

	    if ($length < 0) // then subtract this number of chars
	    {
	        $length = $l + $length;
	    }
	    else
	    {
	        $length = $l - $length;
	    }

		if ($l > $length)
		{
		    if (isset($ellipsis))
		    {
		        $l += strlen($ellipsis);
    			$rc = mb_substr($rc, 0, $length);
    			$rc .= $ellipsis;
		    }
		    else
		    {
    			$rc = mb_substr($rc, 0, $length);
		    }
		}

		return $rc;
	}
}

if (!function_exists('endsWithAnyIndex')) {
	function endsWithAnyIndex($haystack, $needle)
	{
		$rc = false;

		if (is_array($needle))
		{
			foreach($needle as $index => $n)
			{
				$rc = Str::endsWith($haystack, $n);
				if ($rc) // if it ends with any of them then it's true
				{
					$rc = $index;
					break;
				}
			}
		}

		return $rc;
	}
}

if (!function_exists('isMobile')) {
    function isMobile($useragent = null)
    {
        if (!isset($useragent))
		    $useragent = $_SERVER['HTTP_USER_AGENT'];

		$rc = (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',
			substr($useragent,0,4)));

		return $rc;
	}
}

if (!function_exists('inc')) {
    function inc(&$value, $amount)
    {
        $value += $amount;

		return $value;
	}
}
