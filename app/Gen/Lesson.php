<?php

namespace App\Gen;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Status;

class Lesson extends Model
{
	use SoftDeletes;

    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function isFinished()
    {
		return Status::isFinished($this->wip_flag);
    }

    public function isPublic()
    {
		return Status::isPublic($this->release_flag);
    }

    public function getStatus()
    {
		return $this->release_flag;
    }
}
