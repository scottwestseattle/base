<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Site extends Model
{
	use SoftDeletes;

    static private $_site = null;

	static private $_sites = [
		0 => 'localhost',
		1 => 'language4.me',
		2 => 'speakclearer.com',
		3 => 'spanish50.com',
		4 => 'codespace.us',
		5 => 'english50.com',
		6 => 'espdaily.com',
	];

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function site()
    {
        if (!isset(self::$_site)) // only do it once and keep it
        {
            try
            {
                self::$_site = self::get();
                //dump('load site: ' . self::$_site);

                if (empty(self::$_site))
                    throw new \Exception('Site not found');

                if (blank(self::$_site->frontpage))
                    throw new \Exception('Site frontpage not set');
            }
            catch (\Exception $e)
            {
                $dn = domainName();
                logException(__CLASS__, $e->getMessage(), __('base.Error loading site'), ['domain' => $dn]);
            }

            if (!isset(self::$_site))
            {
                // make a dummy site, only happens if site record hasn't been added yet
                self::$_site = new Site();
                self::$_site->title = 'Add Site Record';
                self::$_site->description = 'Not set, add site record';
                self::$_site->language_flag = LANGUAGE_ALL;
                self::$_site->frontpage = 'fp-learn';
            }
        }

        return self::$_site;
    }

    static public function getIconFolder()
    {
        //$rc = self::site()->title;
        $rc = 'icons-' . domainName();
        $path = public_path() . '/' . $rc;
        $rc = (file_exists($path)) ? $rc : null;

        return $rc;
    }

    static public function getTitle()
    {
        $rc = self::site()->description;
        return isset($rc) ? __($rc) : 'Site Title';
    }

	static public function getLanguage()
	{
        //orig: $rc = self::site()->language_flag;
        $rc = getLanguageId();
        $id = isset($rc) ? $rc : LANGUAGE_ALL;
        $rc = getSpeechLanguage($id);
        $rc['condition'] = ($rc['id'] == LANGUAGE_ALL) ? '>=' : '=';

		return $rc;
	}

    static public function getId()
    {
        return self::site()->id;
    }

    static public function hasOption($key)
    {
        $rc = false;
        $options = isset(self::site()->options) && strlen(self::site()->options) > 0 ? self::site()->options : null;
        if (isset($options) && !empty($key))
        {
            if (stristr($options, $key) !== FALSE)
            {
                $rc = true;
            }
        }

        //if ($rc) dump($key . ': ' . $rc);

        return $rc;
    }

    static public function get($domain = null)
    {
	    //
	    // Get the site info for the current domain
	    //
	    if (!isset($domain))
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
			logExceptionEx(__CLASS__, __FUNCTION__, $e->getMessage(), __('base.Error loading site'), ['domain' => $domain]);
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

	static public function getSiteIds()
	{
		$ids = (self::$_sites);

		return $ids;
	}

	static public function getSiteName($id)
	{
		$id = intval($id);
		$rc = "not found";

		$sites = (self::$_sites);
		if (array_key_exists($id, $sites))
		{
			$rc = $sites[$id];
		}

		return $rc;
	}

	static public function getReturnPath()
	{
        $refererPath = referrer('HTTP_REFERER')['path'];
        $requestPath = referrer('REQUEST_URI')['path'];
        //dump('request: ' . $requestPath);
        //dump('referer: ' . $refererPath);

        if (empty($refererPath) || $refererPath === $requestPath)
        {
            // use last saved return path from session
    		$returnPath = self::getReturnPathSession();
            //dump('return path (from session): ' . $returnPath);
        }
        else
        {
            // return path is referer path
	    	self::setReturnPathSession($refererPath);
            $returnPath = $refererPath;
            //dump('return path (from referer): ' . $returnPath);
        }

		return $returnPath;
	}

    static public function setReturnPathSession($path = null)
    {
        $refererPath = isset($path) ? $path : referrer('HTTP_REFERER')['path'];
        session(['returnPath' => $refererPath]);
        //dump($refererPath);
    }

	static public function getReturnPathSession($default = '/')
	{
    	$rc = session('returnPath');
    	$rc = isset($rc) ? $rc : $default;

		return $rc;
	}
}
