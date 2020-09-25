<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
	public function __construct ()
	{
        $this->middleware('auth')->except([
			'',
		]);
			
		parent::__construct();
	}	

	public function index(Request $request)
	{
		$records = db_collection();
		
		$records = User::select()
		//	->where('site_id', SITE_ID)
		//	->where('user_type', '<=', USER_SITE_ADMIN)
			->orderByRaw('id DESC')
			->get();
		//dd($records);
		
		return view('users.index', [
			'records' => $records,
		]);
	}
	
}
