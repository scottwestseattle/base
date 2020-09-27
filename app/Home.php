<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Home extends Model
{
    use HasFactory;
	
	static public function getEvents($filter)
	{
		$records = [];
		
		$path = storage_path('logs/laravel.log');
		
		$file = file_get_contents($path);
		if ($file !== false)
		{
			$all = (!isset($filter));
			$info = ($all || $filter == 'info');
			$errors = ($all || $filter == 'errors');
			$warnings = ($all || $filter == 'warnings');
			
			$lines = explode("\n", $file);
			foreach($lines as $line)
			{
				if ($errors && strpos($line, 'local.ERROR') !== false)
				{				
					$records[] = ['icon' => 'exclamation-diamond', 'color' => 'danger', 'text' => $line];
				}
				else if ($info && strpos($line, 'local.INFO') !== false)
				{					
					$records[] = ['icon' => 'info-circle', 'color' => 'success', 'text' => $line];
				}
				else if ($warnings && strpos($line, 'local.WARNING') !== false)
				{					
					$records[] = ['icon' => 'exclamation-triangle', 'color' => 'warning', 'text' => $line];
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
