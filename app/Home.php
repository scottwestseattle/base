<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;
	
	static public function getEvents()
	{
		$records = [];
		
		$path = storage_path('logs/laravel.log');
		
		$file = file_get_contents($path);
		if ($file !== false)
		{
			$lines = explode("\n", $file);
			foreach($lines as $line)
			{
				if (strpos($line, 'local.') !== false)
				{
					//dd($line);
					$records[] = $line;
				}
			}
			
			$records = array_reverse($records);
		}
		else
		{
			Log::error('error reading log file: ' . $path);
		}
		
		return $records;
	}
}
