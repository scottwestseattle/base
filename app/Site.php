<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function get()
    {
	    //
	    // Get the site info for the current domain
	    //
	    $domain = domainName();

		try
		{
			$record = Site::select()
				->where('title', $domain)
				->first();

            if (!isset($record))
                throw new \Exception('Site not found');
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('msgs.Error loading site'), ['domain' => $domain]);
		}

		return $record;
    }

    public function isFinished()
    {
		return ($this->wip_flag >= getConstant('wip_flag.finished'));
    }

    public function isPublic()
    {
		return ($this->release_flag >= getConstant('release_flag.public'));
    }

    public function getStatus()
    {
		return ($this->release_flag);
    }
}
