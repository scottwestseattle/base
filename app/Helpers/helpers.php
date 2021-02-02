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

    $rc['hash'] = Str::limit(hash('md2', $rc['hash']), DEF_HASH_LENGTH);

    return $rc;
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

	function logFlash($type, $msg, $flash, $parms)
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
		//$key = 'APP_NAME_' . domainName();
		//$v = env($key, 'App Name');
		$v = ucfirst(domainName());

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
		    $hash = timestamp();

		if (isset($title))
		{
		    $v .= convertAccentChars($title);
		}

		if (isset($date))
		{
			$v .= '-' . $date;
		}

		$v = preg_replace('/[^\da-z ]/i', ' ', $v); // replace all non-alphanums with spaces
		$v = str_replace(" ", "-", $v);				// replace spaces with dashes
		$v = strtolower($v);						// make all lc

        // make the permalink unique by adding a hashed string at the end
        // hash the hash key (timestamp) and append the first 6 chars to the permalink
        $v .= '-' . substr(hash('md2', $hash), 0, 6);

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

if (!function_exists('getReleaseStatus')) {
    function getReleaseStatus($flag)
    {
        $label = [
            RELEASEFLAG_NOTSET => 'ui.None',
            RELEASEFLAG_PRIVATE => 'ui.Private',
            RELEASEFLAG_APPROVED => 'ui.Approved',
            RELEASEFLAG_PAID => 'ui.Premium',
            RELEASEFLAG_MEMBER => 'ui.Member',
            RELEASEFLAG_PUBLIC => 'ui.Public',
        ];

        $class = [
            RELEASEFLAG_NOTSET => 'btn-secondary',
            RELEASEFLAG_PRIVATE => 'btn-secondary',
            RELEASEFLAG_APPROVED => 'btn-secondary',
            RELEASEFLAG_PAID => 'btn-primary',
            RELEASEFLAG_MEMBER => 'btn-primary',
            RELEASEFLAG_PUBLIC => 'btn-success',
        ];

        $rc['label'] = $label[$flag];
        $rc['class'] = $class[$flag];

        return $rc;
    }
}

if (!function_exists('getWipStatus')) {
    function getWipStatus($flag)
    {
        $label = [
            WIP_NOTSET => 'ui.None',
            WIP_INACTIVE => 'ui.Inactive',
            WIP_DEV => 'ui.Dev',
            WIP_TEST => 'ui.Test',
            WIP_FINISHED => 'ui.Finished',
            WIP_DEFAULT => 'ui.Dev',
        ];

        $class = [
            WIP_NOTSET => 'btn-secondary',
            WIP_INACTIVE => 'btn-secondary',
            WIP_DEV => 'btn-secondary',
            WIP_TEST => 'btn-primary',
            WIP_FINISHED => 'btn-primary',
            WIP_DEFAULT => 'btn-success',
        ];

        $rc['label'] = $label[$flag];
        $rc['class'] = $class[$flag];
        $rc['done'] = $flag >= WIP_FINISHED;

        return $rc;
    }
}

if (!function_exists('getReleaseFlagForUserLevel')) {
    function getReleaseFlagForUserLevel()
    {
        return isAdmin() ? RELEASEFLAG_NOTSET : RELEASEFLAG_PUBLIC;
    }
}

if (!function_exists('getConditionForUserLevel')) {
    function getConditionForUserLevel()
    {
        return isAdmin() ? '>=' : '=';
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
