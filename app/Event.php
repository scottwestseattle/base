<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
	
	static public function get($filter = null)
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
	
	static public function deleteEvents($filter = null)
	{
		$path = storage_path('logs/laravel.log');
		
		$filter = alpha($filter);
		if (isset($filter))
		{
			$emergency = ($filter == 'emergency');
			if ($emergency)
			{
				$file = file_get_contents($path);
				if ($file !== false)
				{
					$records = [];		
					$lines = explode("\n", $file);
					$cnt = 0;
					foreach($lines as $line)
					{
						// we're deleting emergency lines so only save the ones that don't match
						if ($emergency && strpos($line, 'local.EMERGENCY') !== false)
						{
							// skip these since we're removing them
							$cnt++;
						}
						else
						{					
							$records[] = $line;
						}
					}
					
					try
					{
						// now put the rest of the lines back
						$file = fopen($path, "w");
						foreach($records as $line)
						{
							fwrite($file, $line . "\r\n");
						}
						fclose($file);
						
						$msg = '' . $cnt . ' emergency events deleted';
						logInfo($msg, $msg);
					}
					catch(\Exception $e)
					{
						$msg = 'Error writing log file: ' . $path;
						logError($msg, $msg);
					}
				}
				else
				{
					$msg = 'Error reading log file: ' . $path;
					logError($msg, $msg);
				}
			}
			else
			{
				// do nothing
				$msg = 'Unsupported event type delete filter: ' . $filter;
				logError($msg, $msg);
			}
		}
		else // delete it all
		{
			try 
			{
				// do this instead of unlinking so the empty file will exist
				$file = fopen($path, "w");
				fclose($file);

				$msg = 'All events deleted';
				flash('success', $msg);				
			}
			catch(\Exception $e)
			{
				$msg = 'Error deleting log file: ' . $path;
				logError($msg, $msg);
			}
		}
	}	
}