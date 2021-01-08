<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sample extends Base
{
    public function user()
    {
    	return $this->belongsTo(User::class);
    }

    public function isUnfinished()
    {
    	return (!$this->finished_flag || !$this->approved_flag || !$this->published_flag);
    }

    public function getStatus()
    {
		$text = '';
		$color = '';
		$done = true;
		$btn = '';
		
		if (!$this->approved_flag)
		{
			$text = 'Approve';
			$color = 'yellow';
			$btn = 'btn-warning';
			$done = false;
		}
		else if (!$this->published_flag)
		{
			$text = 'Publish';
			$color = 'green';
			$btn = 'btn-success';
			$done = false;
		}
		
    	return ['text' => $text, 'color' => $color, 'btn' => $btn, 'done' => $done];
    }
}
