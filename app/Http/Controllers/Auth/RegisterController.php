<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use Config;
use Log;

use App\Email;
use App\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        //$this->middleware('auth'); // don't let people register until Admin and Super Admin are set up

		parent::__construct();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function createUser(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    protected function create(Request $request)
    {
		$request->validate([
			'name' => 'required|string|min:5|max:25',
			'email' => 'required|email|unique:users',
			'password' => 'required|string|min:8|max:25|confirmed',
		]);

		$record = new User();

		$credentials = $request->only('name', 'email', 'password');

		$record->name = $credentials['name'];
		$record->email = $credentials['email'];
		$record->email_verification_token = uniqueToken();
		$record->password = Hash::make($credentials['password']);
		$record->site_id = SITE_ID;
		$record->ip_register = ipAddress();
		$record->blocked_flag = 0;
		$record->user_type = Config::get('constants.user_type.unconfirmed');

        $msg = 'new user registered: ' . $record->email;

        // robot test data
	    $rand1 = intval($request->rand1);
	    $rand2 = intval($request->rand2);
	    $sum = intval($request->sum);

		try
		{
		    if ($rand1 + $rand2 === $sum)
		    {
		        // passed the robot test
		        logInfo("robot test passed: " . $rand1 . ' + ' . $rand2 . ' = ' . $sum);
		    }
		    else
		    {
		        // didn't pass, throw
		        throw new \Exception("robot test failed: " . $rand1 . ' + ' . $rand2 . ' = ' . $sum);
		    }

			$record->save();
			logInfo($msg, 'New user added, please check you email for the verification link, and then log in');
		}
		catch(\Exception $e)
		{
			$flash = 'new user not added';
			logError($flash . ': ' . $record->email, $flash, ['exception' => $e->getMessage()]);
			return back();
		}

		try
		{
			if (Email::sendVerification($record))
				logInfo('New user email successfully sent - ' . $msg);
		}
		catch(\Exception $e)
		{
			$flash = 'error: new user email not sent';
			logError($flash . ': ' . $record->email, $flash, ['exc' => $e->getMessage()]);
			return redirect('/');
		}

		return redirect('/login');
	}

	public function register(Request $request)
    {
		return redirect('/');

        $min = 1;
        $max = 20;

        $rand1 = rand($min, $max);
        $rand2 = rand($min, $max);

		return view('auth.register', [
			'rand1' => $rand1,
			'rand2' => $rand2,
		]);
	}

}
