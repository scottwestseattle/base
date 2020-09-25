<?php

use Illuminate\Database\Eloquent\Collection;

function obj_count($obj)
{
	if (isset($obj))
		return count($obj);
	else
		return 0;
}

function db_collection()
{
	return new Collection();
}