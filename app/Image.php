<?php

namespace App;

//
// Handles Release and WIP Status
//
class Image
{
	static public function getPhotos($path)
	{
	    $array = [];

		try
		{
            // '/img/plancha'
            $path = public_path() . $path;
            $files = scandir($path);

            $i = 0;
            foreach($files as $file)
            {
                try {
                    //$file = strtolower($file);
                    if (!is_dir($path . $file))
                    {
                        if (exif_imagetype($path . $file) !== FALSE)
                            $array[$file] = $file;
                    }
                }
                catch (\Exception $e)
                {
                    // ignore the read errors
                    dd('file type: ' . $e);
                }

            }
            //dd($array);
        }
		catch (\Exception $e)
		{
		    $msg = "Error reading directory";
			logException(__FUNCTION__, $e->getMessage(), $msg, ['path' => $path]);
		}

	    return $array;
	}

}
