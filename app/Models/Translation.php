<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    static public function updateEntry($parent_id, $parent_table, $language, $medium_col1, $medium_col2, $large_col1, $large_col2)
    {		
		$record = Translation::select()
			->where('parent_id', $parent_id)
			->where('parent_table', $parent_table)
			->where('language', $language)
			->first();
			
		$rc = [];
		$rc['saved'] = false;
		$rc['logMessage'] = 'Translation has been ';
		
		if (!isset($record))
		{
			$record = new Translation();
			
			$record->language = $language;
			$record->parent_id = $parent_id;
			$record->parent_table = $parent_table;
			
			$rc['logAction'] = LOG_ACTION_ADD;
			$rc['logMessage'] .= 'added';
		}
		else
		{
			$rc['logAction'] = LOG_ACTION_EDIT;
			$rc['logMessage'] .= 'updated';
		}

		$record->medium_col1	= Translation::trimNullStatic($medium_col1);
		$record->medium_col2	= Translation::trimNullStatic($medium_col2);

		$record->large_col1		= Translation::trimNullStatic($large_col1);
		$record->large_col2		= Translation::trimNullStatic($large_col2);
		
		try
		{
			$record->save();

			$rc['logMessageLevel'] = 'success';
			$rc['saved'] = true;
		}
		catch (\Exception $e) 
		{
			$rc['logMessageLevel'] = 'danger';
			$rc['exception'] = $e->getMessage();
		}			

		return $rc;
	}	
	
	static protected function trimNullStatic($text)
	{
		if (isset($text))
		{
			$text = trim($text);
			
			if (strlen($text) === 0)
				$text = null;
		}
		
		return $text;
	}	
}
