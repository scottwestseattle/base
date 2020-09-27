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
				if (strpos($line, 'local.ERROR') !== false)
				{					
					$records[] = ['icon' => 'exclamation-octagon', 'color' => 'danger', 'text' => $line];
				}
				else if (strpos($line, 'local.INFO') !== false)
				{					
					$records[] = ['icon' => 'info-circle', 'color' => 'success', 'text' => $line];
				}
				else if (strpos($line, 'local.WARNING') !== false)
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
