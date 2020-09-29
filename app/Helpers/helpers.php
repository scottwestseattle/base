<?php

use Illuminate\Support\HtmlString;
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

if (!function_exists('ip_address')) {
function ip_address()
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

if (!function_exists('is_admin')) {
function is_admin()
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

if (!function_exists('alpha')) {
	function alpha($text)
	{
		if (isset($text))
		{			
			$_accents = 'áÁéÉíÍóÓúÚüÜñÑ'; 
			$text = preg_replace("/\s+/", ' ', $text); // change all whitespace to one space
			$base = "a-zA-Z ";			
			$match = $base . $_accents;
			$text = preg_replace("/[^" . $match . "]+/", "", trim($text));
		}

		return $text;
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
		$info = ['user id' => Auth::id(), 'ip' => ip_address()];
		
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


