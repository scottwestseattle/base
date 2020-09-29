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
		$rc['emergency'] = false;
		$rc['records'] = $records;
		
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
				if (strpos($line, 'local.EMERGENCY') !== false)
				{					
					$rc['emergency'] = true;
				}
				
				if ($errors && strpos($line, 'local.ERROR') !== false)
				{				
					$records[] = ['icon' => 'exclamation-diamond', 'color' => 'danger', 'bgColor' => 'default', 'text' => $line];
				}
				else if ($info && strpos($line, 'local.INFO') !== false)
				{					
					$records[] = ['icon' => 'info-circle', 'color' => 'success', 'bgColor' => 'default', 'text' => $line];
				}
				else if ($warnings && strpos($line, 'local.WARNING') !== false)
				{					
					$records[] = ['icon' => 'exclamation-triangle', 'color' => 'warning', 'bgColor' => 'default', 'text' => $line];
				}
				else if ($all && strpos($line, 'local.') !== false)
				{
					$records[] = ['icon' => 'exclamation-octagon', 'color' => 'warning', 'bgColor' => '#ff6666', 'text' => $line];
				}
			}
	
			$rc['records'] = array_reverse($records);
		}
		else
		{
			Log::error('error reading log file: ' . $path);
		}
		
		return $rc;
	}
}
