<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    static public function add($domainName, $ip, $model, $page, $record_id = null)
    {
		$save = false;
		$host = null;
		$referrer = null;
		$userAgent = null;

		Visitor::getVisitorInfo($host, $referrer, $userAgent);

		if (strlen($userAgent) == 0 && strlen($referrer) == 0)
		{
			return; // no host or referrer probably means that it's the tester so don't count it
		}

		$visitor = new Visitor();
		$visitor->ip_address = $ip;

		$visitor->visit_count++;
		$visitor->site_id = SITE_ID;
		$visitor->host_name = Tools::trunc($host, VISITOR_MAX_LENGTH);
		$visitor->user_agent = Tools::trunc($userAgent, VISITOR_MAX_LENGTH);
		$visitor->referrer = Tools::trunc($referrer, VISITOR_MAX_LENGTH);

		// new fields
		$visitor->domain_name = $domainName;
		$visitor->model = $model;
		$visitor->page = $page;
		$visitor->record_id = $record_id;

		try
		{
			$visitor->save();
		}
		catch (\Exception $e)
		{
			Event::logException(LOG_MODEL_VISITORS, LOG_ACTION_ADD, 'Error Adding Visitor', null, $e->getMessage());
			throw $e;
		}
	}

	static protected function getVisitorInfo(&$host, &$referrer, &$userAgent)
	{
		//
		// get visitor info
		//
		$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

		$referrer = null;
		if (array_key_exists("HTTP_REFERER", $_SERVER))
			$referrer = $_SERVER["HTTP_REFERER"];

		$userAgent = null;
		if (array_key_exists("HTTP_USER_AGENT", $_SERVER))
			$userAgent = $_SERVER["HTTP_USER_AGENT"];
	}

}
