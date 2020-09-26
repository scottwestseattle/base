<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

// my models
use App\Models\User;

class UserController extends Controller
{
    use SoftDeletes; 
	
	public function __construct ()
	{
        $this->middleware('admin')->except([
			'',
		]);
			
		parent::__construct();
	}	

	public function index(Request $request)
	{		
		$records = User::select()
			->orderByRaw('id DESC')
			->get();
		
		return view('users.index', [
			'records' => $records,
		]);
	}
	
    public function view(User $user)
    {
		return view('users.view', ['user' => $user, 'data' => null]);			
    }
	
    public function edit(User $user)
    {
		return view('users.edit', ['user' => $user, 'data' => null]);			
    }
	
    public function update(Request $request, User $user)
    {	
		$user->name = trim($request->name);
		$user->email = trim($request->email);
		$user->user_type = intval($request->user_type);
		$user->password = $request->password;
		$user->blocked_flag = isset($request->blocked_flag) ? 1 : 0;

		$user->save();
		
		return redirect($this->redirect); 
    }

    public function confirmdelete(User $user)
    {				 		
		return view('users.confirmdelete', ['record' => $user]);
    }
	
    public function delete(User $user)
    {	
		$user->deleteSafe();
		Log::info('User deleted.', ['id' => $user->id]);
    	return redirect($this->redirect); 
    }	
		
}
