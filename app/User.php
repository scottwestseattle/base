<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

use Auth;
use Config;

// user types
define('USER_UNCONFIRMED', 0);		// user account email unconfirmed
define('USER_CONFIRMED', 100);		// user confirmed
define('USER_MEMBER', 200);		// user paid member
define('USER_AFFILIATE', 300);		// affiliate
define('USER_SITE_ADMIN', 1000);	// user site admin
define('USER_SUPER_ADMIN', 10000);	// user super admin

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

	private static $userTypes = [
		USER_UNCONFIRMED => 'Unconfirmed',
		USER_CONFIRMED => 'Confirmed',
		USER_MEMBER => 'Member',
		USER_AFFILIATE => 'Affiliate',
		USER_SITE_ADMIN => 'Admin',
		USER_SUPER_ADMIN => 'Super Admin',
	];

    //
    // Relationships
    //
    public function definitions()
   {
		// many to many
        return $this->belongsToMany('App\Gen\Definition'); //->orderBy('created_at');
    }

	public function isBlocked()
	{
		return($this->blocked_flag == 1);
	}

	static public function isUserBlocked()
	{
		$rc = false;

		if (Auth::check())
		{
			$rc = (Auth::user()->blocked_flag == 1);
		}

		return $rc;
	}

	static public function isConfirmed()
	{
		$rc = false;

		if (Auth::check())
		{
			$rc = (Auth::user()->user_type >= Config::get('constants.user_type.confirmed'));
		}

		return $rc;
	}

	public function isUserConfirmed()
	{
		return ($this->user_type >= USER_CONFIRMED);
	}

	public function getBlocked()
	{
		return $this->blocked_flag ? 'yes' : 'no';
	}

	public function getUserTypes()
	{
		return User::$userTypes;
	}

	public function getUserType()
	{
		$v = '';

		switch($this->user_type)
		{
			case USER_UNCONFIRMED:
				$v = 'Unconfirmed';
				break;
			case USER_CONFIRMED:
				$v = 'Confirmed';
				break;
			case USER_MEMBER:
				$v = 'Member';
				break;
			case USER_AFFILIATE:
				$v = 'Affiliate';
				break;
			case USER_SITE_ADMIN:
				$v = 'Admin';
				break;
			case USER_SUPER_ADMIN:
				$v = 'Super Admin';
				break;
			default:
				$v = 'Unknown';
		}

		return $v;
	}

	static public function isAdminOrOwner($user_id)
	{
		return (isAdmin() || (Auth::check() && Auth::id() == $user_id));
	}

	static public function isOwner($user_id)
	{
		return (Auth::check() && Auth::id() == $user_id);
	}

	static public function isAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SITE_ADMIN);
	}

	static public function isSuperAdmin()
	{
		return (Auth::check() && Auth::user()->user_type >= USER_SUPER_ADMIN);
	}

	public function isSuperAdminUser()
	{
		return ($this->user_type >= USER_SUPER_ADMIN);
	}

	static public function get($limit = PHP_INT_MAX)
	{
		$records = User::select()
			->orderByRaw('id DESC')
			->limit($limit)
			->get();

		return $records;
	}

	static public function count()
	{
		$count = User::select()
			->orderByRaw('id DESC')
			->count();

		return $count;
	}

	static public function getByEmail($email)
	{
		$email = alphanum($email);
		$record = null;

		try
		{
			$record = User::select()
				->where('email', $email)
				->first();
		}
		catch(\Exception $e)
		{
			$msg = 'get user by email address - ' . $e->getMessage();
			logError($msg);
		}

		return $record;
	}
}
