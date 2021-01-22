<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

use App;
use Log;

use App\Event;
use App\Home;
use App\User;

define('LOG_CLASS', 'MvcController');

class MvcController extends Controller
{
	private $redirectTo = '/mvc';

	public function __construct ()
	{
        $this->middleware('admin')->except([
		]);

		parent::__construct();
	}

	public function index(Request $request)
	{
		$path = resource_path() . '/views/gen';
		$path = app_path() . '/Gen';
        $ext = '.sql';

		//$files = getFilesVisible($path, false, /* includeFolders = */ true);
		$files = getFilesVisible($path, $ext);

        $rc = [];
        foreach($files as $file)
        {
            // remove the extension: sites.sql
            $file = Str::replaceLast($ext, '', $file);

            // make it the model name: Site
            $file = ucfirst(Str::singular($file));

            // don't include 'Template'
    		if ($file != 'Template')
    		    $rc[] = $file;
        }

		return view('mvc.index', ['files' => $rc]);
	}

	public function add(Request $request)
	{
		$paths = self::genPaths();

		return view('mvc.add', [
			'paths' => $paths,
		]);
	}

	public function create(Request $request)
	{
		$data = $request->validate([
			'model' => 'required|string|min:3|max:20',
			'plural' => 'required|string|min:3|max:20',
		]);

		$model = $data['model'];
		$views = $data['plural'];

        $topLevel = false;
		if (isset($request->topLevel))
		{
			// put it at the normal, non-gen level
			$topLevel = true;
		}

		$paths = self::genPaths($model, $views, $topLevel);

        //
		// generate the Model
		//
		$tpl = file_get_contents($paths['modelTpl']);
		$tpl = str_replace('Template', $model, $tpl);

		if ($topLevel)
		{
    		$tpl = str_replace('\Gen', '', $tpl);
 		}

		file_put_contents($paths['modelOut'], $tpl);

        //
		// generate the Controller
		//
		$tpl = file_get_contents($paths['controllerTpl']);
		$tpl = str_replace('templates', strtolower($views), $tpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('$template', '$' . strtolower($model), $tpl);

		if ($topLevel)
		{
    		$tpl = str_replace('\Gen', '', $tpl);
    		$tpl = str_replace('gen.', '', $tpl);
		}

		file_put_contents($paths['controllerOut'], $tpl);

		// generate the views
		self::genView($model, $views, $paths, $topLevel, 'add');
		self::genView($model, $views, $paths, $topLevel, 'confirmdelete');
		self::genView($model, $views, $paths, $topLevel, 'deleted');
		self::genView($model, $views, $paths, $topLevel, 'edit');
		self::genView($model, $views, $paths, $topLevel, 'index');
		self::genView($model, $views, $paths, $topLevel, 'menu-submenu');
		self::genView($model, $views, $paths, $topLevel, 'publish');
		self::genView($model, $views, $paths, $topLevel, 'view');

		if (isset($request->add_routes))
		{
			// generate the routes/web
			self::addRoutes($model, $views, $topLevel);
		}

		// generate the MySql db table schema
		$schemaMysql = self::genSchemaMysql($views, $paths);

		return redirect('/mvc/view/' . strtolower($model) . '/' . $views);
	}

	public function view(Request $request, $model, $views)
	{
		$paths = self::genPaths($model, $views);
		$schemaMysql = self::genSchemaMysql($views, $paths);
        $routes = self::genRoutes($model, $views);

		return view('mvc.view', [
			'model' => $model,
			'views' => $views,
			'schemaMysql' => $schemaMysql,
			'routes' => $routes,
			'paths' => $paths,
		]);
	}

	public function confirmDelete(Request $request, $views)
	{
		$model = ucfirst(substr($views, 0, strlen($views) - 1));

		$testFile = app_path() . '/' . $model . '.php';
		$topLevel = (file_exists($testFile));

		$paths = self::genPaths($model, $views, $topLevel);
		//dd($paths);
		return view('mvc.confirmdelete', [
			'views' => $views,
			'paths' => $paths,
			'topLevel' => $topLevel,
		]);
	}

	public function delete(Request $request)
	{
		$data = $request->validate([
			'views' => 'required|alpha|min:3|max:20',
		]);

		$views = $data['views'];

		if ($views == 'templates')
		{
			logError(LOG_CLASS, 'Error deleting MVC - cannot delete the template');
			return redirect($this->redirectTo);
		}

		$topLevel = (isset($request->topLevel));

		$model = ucfirst(substr($views, 0, strlen($views) - 1));
		$paths = self::genPaths($model, $views, $topLevel);

		try
		{
			if (is_file($paths['modelOut']))
				unlink($paths['modelOut']);

			if (is_file($paths['mysqlSchemaOut']))
				unlink($paths['mysqlSchemaOut']);

			if (is_file($paths['controllerOut']))
				unlink($paths['controllerOut']);

			// remove view files and then view directory
			if (is_dir($paths['viewsOutPath']))
			{
				$files = glob($paths['viewsOutPathWildcard']);

				foreach($files as $file)
				{
					if (is_file($file))
						unlink($file);
				}

				rmdir($paths['viewsOutPath']);
			}

			logInfo(LOG_CLASS, 'MVC has been deleted', ['model' => $model, 'views' => $views]);
		}
		catch(Exception $e)
		{
			logException(LOG_CLASS, $e->getMessage(), 'Error deleting MFC file');
		}

		return redirect($this->redirectTo);
	}

	static private function genPaths($model = null, $views = null, $topLevel = false)
	{
	    $model = isset($model) ? ucfirst($model) : null;

		// views
		$rc['viewsOutPath'] = null;
		$root = resource_path() . '/views/gen/';
		$rootOut = $topLevel ? resource_path() . '/views/' : $root;

		$rc['viewsTplPath'] = $root . 'templates/'; //ex: '/resources/views/gen/templates/'

		if (isset($views))
		{
			$rc['viewsOutPath'] = $rootOut . $views . '/'; //ex: '/resources/views/visitors/'
			$rc['viewsOutPathWildcard'] = $rootOut . $views . '/*'; //ex: '/resources/views/visitors/*'
		}

		// model
		$rc['modelOut'] = null;
		$rc['modelTpl'] = app_path() . '/Gen/Template.php';
		if (isset($model))
		{
		    if ($topLevel)
    			$rc['modelOut'] = app_path() . '/' . $model . '.php';
    		else
    			$rc['modelOut'] = app_path() . '/Gen/' . $model . '.php';
		}

		// controller
		$rc['controllerOut'] = null;
		$rc['controllerTpl'] = app_path() . '/Http/Controllers/Gen/TemplateController.php';
		if (isset($model))
		{
            if ($topLevel)
    			$rc['controllerOut'] = app_path() . '/Http/Controllers/' . $model . 'Controller.php';
            else
    			$rc['controllerOut'] = app_path() . '/Http/Controllers/Gen/' . $model . 'Controller.php';
		}

		// mySQL table schema (for topLevel mvc, they also go in the 'Gen' folder)
    	$rc['mysqlSchemaOut'] = app_path() . '/Gen/' . $views . '.sql'; // ex: /app/Gen/visitors.sql

        //dd($rc);

		return $rc;
	}

	static private function genView($model, $views, $paths, $topLevel, $view)
	{
		if (!is_dir($paths['viewsOutPath']))
			mkdir($paths['viewsOutPath'], 0777);

		$viewFile = $view . '.blade.php'; 	 // ex: 'index.blade.php'
		$viewFileTpl = $paths['viewsTplPath'] . $viewFile; // ex:  '/resources/views/templates/index.blade.php'
		$viewFileOut = $paths['viewsOutPath'] . $viewFile; // ex: '/resources/views/visitors/index.blade.php'

		$tpl = file_get_contents($viewFileTpl);
		$tpl = str_replace('templates', strtolower($views), $tpl);
		$tpl = str_replace('Template', $model, $tpl);

		if ($topLevel)
		    $tpl = str_replace('gen.', '', $tpl);

		file_put_contents($viewFileOut, $tpl);
	}

	static private $routesTemplate = "// GENERATED for Template model
use App\Http\Controllers\Gen\TemplateController;

// Templates
Route::group(['prefix' => 'templates'], function () {
	Route::get('/', [TemplateController::class, 'index']);
	Route::get('/index', [TemplateController::class, 'index']);

	// add
	Route::get('/add', [TemplateController::class, 'add']);
	Route::post('/create', [TemplateController::class, 'create']);

	// edit
	Route::get('/edit/{template}', [TemplateController::class, 'edit']);
	Route::post('/update/{template}', [TemplateController::class, 'update']);

	// publish
	Route::get('/publish/{template}', [TemplateController::class, 'publish']);
	Route::post('/publishupdate/{template}', [TemplateController::class, 'updatePublish']);
	Route::get('/publishupdate/{template}', [TemplateController::class, 'updatePublish']);

	// delete
	Route::get('/confirmdelete/{template}', [TemplateController::class, 'confirmDelete']);
	Route::post('/delete/{template}', [TemplateController::class, 'delete']);
	Route::get('/delete/{template}', [TemplateController::class, 'delete']);

	// undelete
	Route::get('/deleted', [TemplateController::class, 'deleted']);
	Route::get('/undelete/{id}', [TemplateController::class, 'undelete']);

	// view
	Route::get('/view/{entry}', [EntryController::class, 'view']);
	Route::get('/{permalink}', [EntryController::class, 'permalink']);
});
";

	static private function addRoutes($model, $views, $topLevel)
	{
		$routesOut = base_path() . '/routes/web.php'; // ex: /routes/web.php

		$tpl = self::genRoutes($model, $views, $topLevel);

		file_put_contents($routesOut, $tpl, FILE_APPEND);
	}

	static private function genRoutes($model, $views, $topLevel = false)
	{
		$modelLc = strtolower($model);

		$tpl = self::$routesTemplate;
		$tpl = str_replace('Templates', ucfirst($views), $tpl);
		$tpl = str_replace('templates', $views, $tpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', $modelLc, $tpl);

		if ($topLevel)
    		$tpl = str_replace('\Gen', '', $tpl);

        //dd($tpl);

		return $tpl;
	}

	static private $schemaMysql = "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = \"+00:00\";

CREATE TABLE `templates` (
  `id` int(10) UNSIGNED NOT NULL,
  `site_id` int(10) UNSIGNED DEFAULT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `description` text COLLATE utf8mb4_bin,
  `permalink` varchar(150) COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(50) COLLATE utf8mb4_bin DEFAULT NULL,
  `type_flag` tinyint(4) NOT NULL DEFAULT 0,
  `wip_flag` tinyint(4) NOT NULL DEFAULT 0,
  `release_flag` tinyint(4) NOT NULL DEFAULT 0,
  `view_count` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `last_viewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `templates`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
";

	static private function genSchemaMysql($table, $paths)
	{
		$tpl = self::$schemaMysql;
		$tpl = str_replace('templates', $table, $tpl);
		file_put_contents($paths['mysqlSchemaOut'], $tpl);

		return $tpl;
	}

}
