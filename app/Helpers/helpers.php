<?php

use App\Models\User;

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
	return \App\Models\User::isAdmin();
}
}