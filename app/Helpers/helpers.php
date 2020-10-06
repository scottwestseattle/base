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
				logEmergency('isExpired - date error: ' . $e->getMessage());
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
    function createPermalink($title, $date = null)
    {
		$v = null;

		if (isset($title))
		{
			$v = $title;
		}

		if (isset($date))
		{
			$v .= '-' . $date;
		}

		$v = preg_replace('/[^\da-z ]/i', ' ', $v); // replace all non-alphanums with spaces
		$v = str_replace(" ", "-", $v);				// replace spaces with dashes
		$v = strtolower($v);						// make all lc
		$v = trimNull($v);					// trim it or null it

		return $v;
	}
}

