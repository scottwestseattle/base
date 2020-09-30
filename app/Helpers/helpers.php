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
	
	function logWarning($msg, $flash = null)
	{
		logFlash('warning', $msg, $flash);
	}
	
	function logError($msg, $flash = null)
	{
		logFlash('error', $msg, $flash);
	}
	
	function logInfo($msg, $flash = null)
	{
		logFlash('info', $msg, $flash);
	}
	
	function logFlash($type, $msg, $flash = null)
	{
		$info = ['user id' => Auth::id(), 'ip' => ipAddress()];
		
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
