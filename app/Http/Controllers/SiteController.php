<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Auth;
use DB;
use Config;
use Log;

use App\Entry;
use App\Gen\Definition;
use App\Site;
use App\Status;
use App\Tools;
use App\User;

define('PREFIX', 'sites');
define('VIEWS', 'sites');
define('LOG_CLASS', 'SiteController');

class SiteController extends Controller
{
	private $redirectTo = PREFIX;

	public function __construct ()
	{
        $this->middleware('admin')->except([
            'index',
            'view',
            'permalink',
            'sitemap',
        ]);

		parent::__construct();
	}

    public function index(Request $request)
    {
		$records = [];

		try
		{
			$records = Site::select()
				->get(5);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting record list'));
		}

		return view(VIEWS . '.index', [
			'records' => $records,
		]);
    }

    public function add()
    {
		return view(VIEWS . '.add', [
			'languages' => getLanguageOptions(/* includeAll = */ isAdmin()),
			]);
	}

    public function create(Request $request)
    {
		$record = new Site();

		$record->user_id 		= Auth::id();
		$record->title 			= trimNull($request->title);
		$record->frontpage  	= trimNull($request->frontpage);
		$record->description	= trimNull($request->description);
		$record->options        = trimNull($request->options);
        $record->language_flag  = $request->language_flag;

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('base.New record has been added'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error adding new record'));
			return back();
		}

		return redirect($this->redirectTo . '/view/' . $record->id);
    }

    public function permalink(Request $request, $permalink)
    {
		$permalink = trim($permalink);

		$record = null;

		try
		{
			$record = Site::select()
				->where('site_id', SITE_ID)
				->where('published_flag', 1)
				->where('permalink', $permalink)
				->first();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Record not found'), ['permalink' => $permalink]);
			return back();
		}

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
	}

	public function view(Request $request, $locale, Site $site)
    {
		$record = $site;

		return view(VIEWS . '.view', [
			'record' => $record,
			]);
    }

	public function edit(Request $request, $locale, Site $site)
    {
		$record = $site;

		return view(VIEWS . '.edit', [
			'record' => $record,
			'languages' => getLanguageOptions(/* includeAll = */ isAdmin()),
			]);
    }

    public function update(Request $request, Site $site)
    {
		$record = $site;

		$isDirty = false;
		$changes = '';

		$record->title = copyDirty($record->title, $request->title, $isDirty, $changes);
		$record->description = copyDirty($record->description, $request->description, $isDirty, $changes);
		$record->frontpage = copyDirty($record->frontpage, $request->frontpage, $isDirty, $changes);
		$record->language_flag = copyDirty($record->language_flag, $request->language_flag, $isDirty, $changes);
		$record->options = copyDirty($record->options, $request->options, $isDirty, $changes);

		if ($isDirty)
		{
			try
			{
				$record->save();
				logInfo(LOG_CLASS, __('base.Record has been updated'), ['record_id' => $record->id, 'changes' => $changes]);
			}
			catch (\Exception $e)
			{
				logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record'), ['record_id' => $record->id]);
			}
		}
		else
		{
			logInfo(LOG_CLASS, __('base.No changes made'), ['record_id' => $record->id]);
		}

		return redirect('/' . PREFIX . '/view/' . $record->id);
	}

    public function confirmDelete(Request $request, $locale, Site $site)
    {
		$record = $site;

		return view(VIEWS . '.confirmdelete', [
			'record' => $record,
		]);
    }

    public function delete(Request $request, Site $site)
    {
		$record = $site;

		try
		{
			$record->delete();
			logInfo(LOG_CLASS, __('base.Record has been deleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error deleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function undelete(Request $request, $id)
    {
		$id = intval($id);

		try
		{
			$record = Site::withTrashed()
				->where('id', $id)
				->first();

			$record->restore();
			logInfo(LOG_CLASS, __('base.Record has been undeleted'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error undeleting record'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }

    public function deleted()
    {
		$records = []; // make this countable so view will always work

		try
		{
			$records = Site::withTrashed()
				->whereNotNull('deleted_at')
				->get();
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error getting deleted records'));
		}

		return view(VIEWS . '.deleted', [
			'records' => $records,
		]);
    }

    public function publish(Request $request, Site $site)
    {
		$record = $site;

		return view(VIEWS . '.publish', [
			'record' => $record,
			'release_flags' => Status::getReleaseFlags(),
			'wip_flags' => Status::getWipFlags(),
		]);
    }

    public function updatePublish(Request $request, Site $site)
    {
		$record = $site;

        if ($request->isMethod('get'))
        {
            // quick publish, set to toggle public / private
            $record->wip_flag = $record->isFinished() ? getConstant('wip_flag.dev') : getConstant('wip_flag.finished');
            $record->release_flag = $record->isPublic() ? RELEASEFLAG_PRIVATE : RELEASEFLAG_PUBLIC;
        }
        else
        {
            $record->wip_flag = $request->wip_flag;
            $record->release_flag = $request->release_flag;
        }

		try
		{
			$record->save();
			logInfo(LOG_CLASS, __('base.Record status has been updated'), ['record_id' => $record->id]);
		}
		catch (\Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), __('base.Error updating record status'), ['record_id' => $record->id]);
			return back();
		}

		return redirect($this->redirectTo);
    }


	public function sitemapTRAVEL(Request $request)
	{
		return view('home.sitemap');
	}

    public function sitemap(Request $request)
    {
        if (domainName() != 'localhost')
        {
            $sites = [
                ['https://', 'espdaily.com'],
    		];
        }
        else
        {
            $sites = [
    			['http://', 'localhost'],
    		];
        }

		$siteMaps = [];

		foreach($sites as $site)
		{
			$siteMap = $this->makeSiteMap($site);

			if (isset($siteMap))
				$siteMaps[] = $siteMap;
		}

        $view = isAdmin() ? 'sitemap-admin' : 'sitemap';

		return view('sites.' . $view, [
			'siteMaps' => $siteMaps,
			//'records' => $siteMap['sitemap'],
			//'server' => $siteMap['server'],
			//'filename' => $siteMap['filename'],
			'executed' => null,
			'sitemap' => true,
		]);
	}

    protected function makeSiteMap($sites)
    {
    	$http = $sites[0];
    	$domainName = $sites[1];

		$filename = 'sitemap-' . $domainName . '.txt';

		$urls = [
			'/',
			'/login',
			'/about',
			'/dictionary',
			'/favorites',
			'/books',
		];

		$site = Site::site($domainName);

		if (!isset($site->id))
		{
			$siteMap['sitemap'] = null; // no records
			$siteMap['server'] = $domainName;
			$siteMap['filename'] = $filename;

			return $siteMap;
		}

		if (true) // articles
		{
			$urls[] = '/articles';
			$urls = array_merge($urls, self::getSiteMapEntries(ENTRY_TYPE_ARTICLE, 'articles/view'));
		}

		if (false) // snippets
		{
			$urls[] = '/practice';
			$urls = array_merge($urls, self::getSiteMapDefinitions(DEFTYPE_SNIPPET, 'practice/view'));
		}

		if (true) // dictionary
		{
			$urls[] = '/dictionary';
			$urls = array_merge($urls, self::getSiteMapDefinitions(DEFTYPE_DICTIONARY, 'definitions/view'));
		}

		if (false) // favorites
		{
			//$urls[] = '/definitions';
			//$urls = array_merge($urls, self::getSiteMapEntries());
		}

		if (false)
		{
			$urls[] = '/comments';
		}

		if (isset($urls))
		{
			// write the sitemap file
			$siteMap = [];

			$server = $domainName;

            try
            {
                $myFile = null;
                if (isAdmin())
                {
                    // file name looks like: sitemap-domain.com.txt
                    $myfile = fopen($filename, "w") or die("Unable to open file!");
                }

                $server = $http . $server;

                foreach($urls as $url)
                {
                    $line = $server . $url;
                    $siteMap[] = $line;

                    if (isset($myFile))
                        fwrite($myfile, utf8_encode($line . PHP_EOL));
                }

                if (isset($myFile))
                    fclose($myfile);
            }
            catch (\Exception $e)
            {
                logException(LOG_CLASS, $e->getMessage(), __('base.Error writing sitemap file'));
            }
		}

		$rc = [];
		$rc['sitemap'] = $siteMap;
		$rc['filename'] = $filename;
		$rc['server'] = $server;

		return $rc;
	}

    protected function getSiteMapEntries($type, $prefix)
    {
		$urls = [];

        $siteLanguage = Site::getLanguage();

        $records = Entry::select()
            ->where('type_flag', $type)
            ->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
            ->where('language_flag', $siteLanguage['condition'], $siteLanguage['id'])
            ->get();

		if (isset($records))
		{
			foreach($records as $record)
			{
			    $urls[] = '/' . $prefix . '/' . $record->permalink;
			}
		}

		return $urls;
	}

    protected function getSiteMapDefinitions($type, $prefix)
    {
		$urls = [];

        $siteLanguage = Site::getLanguage();

        $records = Definition::select()
            ->where('type_flag', $type)
            ->where('release_flag', '>=', RELEASEFLAG_PUBLIC)
            ->where('language_flag', $siteLanguage['condition'], $siteLanguage['id'])
            ->orderByRaw('title ' . COLLATE_ACCENTS . ' ASC')
            ->get();

		if (isset($records))
		{
			foreach($records as $record)
			{
				$urls[] = '/' . $prefix . '/' . $record->permalink;
				//dd($record);
			}
		}

		return $urls;
	}

}
