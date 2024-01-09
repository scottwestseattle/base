<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

define('MAX_LOG_FILE_SIZE', 100000000);

class Event extends Model
{
    use HasFactory;

	static public function hasEmergency()
	{
		$rc = false;
		$path = storage_path('logs/laravel.log');
		$handle = fopen($path, "r");
		if ($handle)
		{
			while (($line = fgets($handle)) !== false)
			{
				// process the line read.
				if (stripos($line, 'local.emergency') !== false)
				{
					$rc = true;
					break;
				}
			}

			fclose($handle);
		}
		else
		{
			logError('hasEmergency - error opening log file');
		}

		return $rc;
	}

	static public function get($filter = null)
	{
		$records = [];
		$rc['emergency'] = 0;
		$rc['errors'] = 0;
		$rc['records'] = $records;

		$path = storage_path('logs/laravel.log');

        // sometimes it gets too big to read
        $logFileSize = filesize($path);

        if ($logFileSize <= MAX_LOG_FILE_SIZE)
        {
            $file = file_get_contents($path);
        }
        else
        {
            $file = false;
        }

		if ($file !== false)
		{
			$all = (!isset($filter));
			$info = ($all || $filter == 'info');
			$errors = ($all || $filter == 'errors');
			$warnings = ($all || $filter == 'warnings');

			$lines = [];
			try
			{
    			$lines = explode("\n", $file);
			}
            catch(\Exception $e)
            {
                $msg = 'Error splitting log file (may be too big): ' . $path;
                logError($msg, $msg);
            }

			foreach($lines as $line)
			{
				if (strpos($line, 'local.EMERGENCY') !== false)
				{
					$rc['emergency']++;
				}

				if ($errors && strpos($line, 'local.ERROR') !== false)
				{
					$rc['errors']++;
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
		    $link = route('events.delete', ['locale' => app()->getLocale()]);
			$msg = 'error reading log file: ' . $path . ' (size: ' . $logFileSize . ')  <a href="$link">Delete</a>'; //route()
            logError($msg, $msg);
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
			$errors = ($filter == 'errors');
			if ($emergency || $errors)
			{
                // sometimes it gets too big to read
                $logFileSize = filesize($path);
                $file = ($logFileSize <= MAX_LOG_FILE_SIZE) ? file_get_contents($path) : false;

				if ($file !== false)
				{
					$records = [];
					$lines = explode("\n", $file);
					$cnt = 0;
					foreach($lines as $line)
					{
						if ($emergency)
						{
							if (strpos($line, 'local.EMERGENCY') !== false)
							{
								// skip and count
								$cnt++;
							}
							else
							{
								// save everything else
								$records[] = $line;
							}
						}
						else if ($errors)
						{
							if (
								strpos($line, 'local.INFO') !== false
							 || strpos($line, 'local.WARNING') !== false
							 || strpos($line, 'local.EMERGENCY') !== false
							)
							{
								// save these
								$records[] = $line;
							}
							// we're only deleting ERROR lines so skip them
							else if (strpos($line, 'local.ERROR') !== false)
							{
								// skip and count
								$cnt++;
							}
							else
							{
								// skip all of the stack trace detail lines of an ERROR message
							}
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

						if ($emergency)
							$msg = trans_choice('base.emergency events deleted', $cnt, ['count' => $cnt]);
						else if ($errors)
							$msg = trans_choice('base.error events deleted', $cnt, ['count' => $cnt]);
						else
							$msg = __('base.events deleted');

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
        			$msg = 'error reading log file: ' . $path . ' (size: ' . $logFileSize . ')';
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

				$msg = __('base.All events deleted');
				logInfo($msg, $msg);
			}
			catch(\Exception $e)
			{
				$msg = 'Error deleting log file: ' . $path;
				logError($msg, $msg);
			}
		}
	}
}
