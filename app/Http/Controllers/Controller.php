<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

define('SITE_ID', 0);

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function __construct ()
	{
		// middleware examples
        //$this->middleware('auth')->only('dashboard');
		//$this->middleware('admin')->only('admin');
		
		/*
		$this->middleware('auth')->except([
			'login',
			'register',
			'frontpage',
			'about',
			'contact',
			'sitemap',
		]);
		*/
	}	
	
	public function getViewData($vd)
	{
		return $vd;
	}	
}
