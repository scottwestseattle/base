<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log;
use App;
use Auth;
use Lang;

use App\Event;
use App\Entry;
use App\Tools;
use App\Translation;

define('TRANSLATIONS_FOLDER', '../resources/lang/');

class TranslationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

	public function __construct ()
	{
        $this->middleware('auth');

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];
		$files = [];
		$folder = TRANSLATIONS_FOLDER . 'en'; //App::getLocale();

		try
		{
			if (is_dir($folder))
			{
				// folder exists, nothing to do
			}
			else
			{
				// make the folder with read/execute for everybody
				mkdir($folder, 0755);
			}
		}
		catch (\Exception $e)
		{
			$msg = 'Error creating translation folder: ' . $folder;
			Event::logException(LOG_MODEL_TRANSLATIONS, LOG_ACTION_INDEX, $msg, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg . ' ' . $e->getMessage());
		}

		try
		{
			$files = scandir($folder);
		}
		catch (\Exception $e)
		{
			$msg = 'Error opening translation folder: ' . $folder;
			Event::logException(LOG_MODEL_TRANSLATIONS, LOG_ACTION_INDEX, $msg, null, $e->getMessage());

			$request->session()->flash('message.level', 'danger');
			$request->session()->flash('message.content', $msg . ' ' . $e->getMessage());
		}

		foreach($files as $file)
		{
			if ($file != '.' && $file != '..')
			{
				$records[] = str_replace('.php', '', $file);
			}
		}

		return view('translations.index', $this->getViewData([
			'records' => $records,
		]));
	}

    public function add(Request $request)
    {
		$vdata = $this->getViewData([
			'records' => $records,
		]);

		return view('entries.add', $vdata);
	}

    public function view(Request $request, $filename)
    {
		$filename = alpha($filename);
		
		$locale = App::getLocale();

		App::setLocale('en');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale('es');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale('zh');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale($locale);

		foreach($records['en'] as $key => $value)
		{
			if (!array_key_exists($key, $records['es']))
			{
				$records['es'][$key] = null;
			}
			if (!array_key_exists($key, $records['zh']))
			{
				$records['zh'][$key] = null;
			}
		}

		$vdata = $this->getViewData([
			'prefix' => 'translations',
			'filename' => $filename,
			'records' => $records,
		]);

		return view('translations.view', $vdata);
    }
	
    public function edit(Request $request, $filename)
    {
		$locale = App::getLocale();

		App::setLocale('en');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale('es');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale('zh');
		$records[App::getLocale()] = Lang::get($filename);

		App::setLocale($locale);

		if (array_key_exists('en', $records))
		{
			$keys = $records['en'];
			if (is_array($keys)) // && count($keys) > 0)
			{
				foreach($keys as $key => $value)
				{
					if (!array_key_exists($key, $records['es']))
					{
						$records['es'][$key] = null;
					}
					if (!array_key_exists($key, $records['zh']))
					{
						$records['zh'][$key] = null;
					}
				}
			}
		}

		return view('translations.edit', [
			'prefix' => 'translations',
			'filename' => $filename,
			'records' => $records,
		]);
    }

    public function update(Request $request, $filename)
    {
		$lines = [];
		$array = [];
				
		for ($j = 0; $j < 100; $j++) // foreach each language
		{
			if (isset($request->records[$j]))
			{
				for ($i = 0; $i < 1000; $i++) // each key in the language
				{
					if (isset($request->records[$j][$i])) // if language key set
					{
						if (isset($request->records[$j+1][$i]))
						{
							$line = "'" . $request->records[0][$i] . "' => '" . $request->records[$j + 1][$i] . "'";
						}
						else
						{
							// key exists but not translation, put key in for the value
							$line = "'" . $request->records[0][$i] . "' => '" . $request->records[0][$i] ."'";
						}

						//dump($line);

						$array[$j][$i] = $line;
					}
					else
					{
					}
				}
				
				//dd($array);
			}
			else
			{
				break;
			}
		}

		$this->save('en', $filename, $array[0]);
		$this->save('es', $filename, $array[1]);
		$this->save('zh', $filename, $array[2]);

		Log::info('Translations updated', ['id' => Auth::id()]);
		flash('success', __('msgs.Translation file has been updated'));
		
		return redirect('/translations');
    }

    private function save($locale, $filename, $lines)
    {
		$folder = TRANSLATIONS_FOLDER . $locale . '/';
		$path = $folder . $filename . '.php';

		try
		{
			$fp = fopen($path, "wb");

			if ($fp)
			{
				fputs($fp, '<?php' . PHP_EOL);
				fputs($fp, 'return [' . PHP_EOL);

				foreach($lines as $line)
				{
					fputs($fp, $line . ',' . PHP_EOL);
				}

				fputs($fp, '];' . PHP_EOL);
			}

			fclose($fp);
		}
		catch (\Exception $e)
		{
			Event::logException(LOG_MODEL_TRANSLATIONS, LOG_ACTION_EDIT, 'Error accessing translation file: ' . $path, null, $e->getMessage());
            Tools::flash('danger', $e->getMessage());
		}

	}

    public function updateEntry(Request $request, Entry $entry)
    {
		$record = Translation::select()
			->where('parent_id', $entry->id)
			->where('parent_table', 'entries')
			->where('language', $request->language)
			->first();

		$logMessage = 'Translation has been ';
		if (!isset($record))
		{
			$record = new Translation();

			$record->language = App::getLocale();
			$record->parent_id = $entry->id;
			$record->parent_table = 'entries';

			$logAction = LOG_ACTION_ADD;
			$logMessage .= 'added';
		}
		else
		{
			$logAction = LOG_ACTION_EDIT;
			$logMessage .= 'updated';
		}

    	if ($this->isOwnerOrAdmin($entry->user_id))
        {
			$record->small_col1		= $this->trimNull($request->small_col1);
			$record->small_col2		= $this->trimNull($request->small_col2);
			$record->small_col3		= $this->trimNull($request->small_col3);
			$record->small_col4		= $this->trimNull($request->small_col4);
			$record->small_col5		= $this->trimNull($request->small_col5);
			$record->small_col6		= $this->trimNull($request->small_col6);
			$record->small_col7		= $this->trimNull($request->small_col7);
			$record->small_col8		= $this->trimNull($request->small_col8);
			$record->small_col9		= $this->trimNull($request->small_col9);
			$record->small_col10	= $this->trimNull($request->small_col10);

			$record->medium_col1	= $this->trimNull($request->medium_col1);
			$record->medium_col2	= $this->trimNull($request->medium_col2);
			$record->medium_col3	= $this->trimNull($request->medium_col3);
			$record->medium_col4	= $this->trimNull($request->medium_col4);
			$record->medium_col5	= $this->trimNull($request->medium_col5);
			$record->medium_col6	= $this->trimNull($request->medium_col6);
			$record->medium_col7	= $this->trimNull($request->medium_col7);
			$record->medium_col8	= $this->trimNull($request->medium_col8);
			$record->medium_col9	= $this->trimNull($request->medium_col9);
			$record->medium_col10	= $this->trimNull($request->medium_col10);

			$record->large_col1		= $this->trimNull($request->large_col1);
			$record->large_col2		= $this->trimNull($request->large_col2);
			$record->large_col3		= $this->trimNull($request->large_col3);
			$record->large_col4		= $this->trimNull($request->large_col4);
			$record->large_col5		= $this->trimNull($request->large_col5);
			$record->large_col6		= $this->trimNull($request->large_col6);
			$record->large_col7		= $this->trimNull($request->large_col7);
			$record->large_col8		= $this->trimNull($request->large_col8);
			$record->large_col9		= $this->trimNull($request->large_col9);
			$record->large_col10	= $this->trimNull($request->large_col10);

			try
			{
				$record->save();

				Event::logEdit(LOG_MODEL_TRANSLATIONS, $entry->title, $entry->id);

				$request->session()->flash('message.level', 'success');
				$request->session()->flash('message.content', $logMessage);
			}
			catch (\Exception $e)
			{
				Event::logException(LOG_MODEL_TRANSLATIONS, $logAction, Tools::getTextOrShowEmpty($entry->title), null, $e->getMessage());

				$request->session()->flash('message.level', 'danger');
				$request->session()->flash('message.content', $e->getMessage());
			}
		}

		return redirect($this->getReferer($request, '/entries/indexadmin'));
    }
}
