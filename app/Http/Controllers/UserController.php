<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Log;

use App\User;

define('PREFIX', 'users');
define('LOG_CLASS', 'UserController');

class UserController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except([
			'edit', 'update', 'view'
		]);

        $this->middleware('owner')->only([
			'edit', 'update', 'view',
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
		$user->name = alphanum($request->name);
		$user->email = alphanum($request->email);
		$user->email_verification_token = uniqueToken();

		if (User::isAdmin())
		{
			$user->user_type = intval($request->user_type);
			$user->password = $request->password;
			$user->blocked_flag = isset($request->blocked_flag) ? 1 : 0;
		}

        try {
            $user->save();
            Log::info('User updated', ['id' => $user->id]);
            flash('success', 'User updated');
        }
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record'),
			    ['record_id' => $user->id, 'exc' => $e->getMessage()]);
			return back();
		}

		return redirect(User::isAdmin() ? '/users' : '/dashboard');
    }

    public function confirmdelete(User $user)
    {
		return view('users.confirmdelete', ['record' => $user]);
    }

    public function delete(User $user)
    {
		try
		{
            $user->email = 'DELETED-' . $user->email;
            $user->save();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating delete record'), ['record_id' => $user->id]);
			return back();
		}

		try
		{
            $user->delete();
            Log::info('User deleted', ['id' => $user->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $user->id]);
			return back();
		}

		return redirect(User::isAdmin() ? '/users' : '/dashboard');
    }

    public function undelete(Request $request, $id)
    {
		$id = intval($id);
        $record = null;

		try
		{
			$record = User::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		try
		{
		    if (isset($record))
		    {
                if (Str::startsWith($record->email, 'DELETED-'))
                {
                    $email = substr($record->email, strlen('DELETED-'));
                    $record->email = $email;
                    $record->save();
                }
		    }
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating undelete record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect('/users/deleted');
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = User::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(PREFIX . '.deleted', [
			'records' => $records,
		]);
    }

}
