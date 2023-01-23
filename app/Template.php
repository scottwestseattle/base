<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

// type_flag types
define('DEFTYPE_TPL_NOTSET',    0);
define('DEFTYPE_TPL1',          1);
define('DEFTYPE_TPL2',          2);
define('DEFTYPE_TPL3',          3);
define('DEFTYPE_TPL_OTHER',     40);

class Template extends Model
{
	const _typeFlags = [
        DEFTYPE_TPL_NOTSET          => 'Not Set',
        DEFTYPE_TPL1                => 'One',
        DEFTYPE_TPL2                => 'Two',
        DEFTYPE_TPL3                => 'Three',
        DEFTYPE_TPL_OTHER           => 'Other',
	];

    public function getTypeFlagName()
    {
        return self::_typeFlags[$this->type_flag];
    }

    public function user()
    {
    	return $this->belongsTo(User::class);
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
