<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App;
use Log;

use App\Event;
use App\Home;
use App\User;

class MvcController extends Controller
{
	private $redirectTo = '/mvc';
	
	public function __construct ()
	{
        $this->middleware('admin')->except([
			//'add',
			//'create',
			'index',
		]);
						
		parent::__construct();
	}	

	public function index(Request $request)
	{
		$path = resource_path() . '/views';
		$files = scandir($path);
		//dd($files);
		return view('mvc.index', ['files' => $files]);
	}

	public function add(Request $request)
	{
		return view('mvc.add');
	}

	public function create(Request $request)
	{
		$data = $request->validate([
			'model' => 'required|string|min:3|max:20',		
			'plural' => 'required|string|min:3|max:20',		
		]);	
		
		$model = $data['model'];
		$views = $data['plural'];

		// generate the Model
		$modelFileTpl = app_path() . '/Template.php';
		$modelFileOut = app_path() . '/' . $model . '.php';
		$tpl = file_get_contents($modelFileTpl);
		$tpl = str_replace('Template', $model, $tpl);
		file_put_contents($modelFileOut, $tpl);
		
		// generate the Controller
		$controllerFileTpl = app_path() . '/Http/Controllers/TemplateController.php';
		$controllerFileOut = app_path() . '/Http/Controllers/' . $model . 'Controller.php';		
		$tpl = file_get_contents($controllerFileTpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', strtolower($model), $tpl);
		file_put_contents($controllerFileOut, $tpl);
		
		// generate the views
		self::genView($model, $views, 'add');
		self::genView($model, $views, 'confirmdelete');
		self::genView($model, $views, 'edit');
		self::genView($model, $views, 'index');
		self::genView($model, $views, 'menu-submenu');
		self::genView($model, $views, 'view');

		if (isset($request->add_routes))
		{
			// generate the routes/web
			self::genRoutes($model, $views);
		}

		// generate the MySql db table schema
		$schemaMysql = self::genSchemaMysql($views);
		
		return redirect('/mvc/view/' . strtolower($model) . '/' . $views);
	}

	public function view(Request $request, $model, $views)
	{
		$schemaMysql = self::genSchemaMysql($views);
		
		return view('mvc.view', [
			'model' => $model,
			'views' => $views,
			'schemaMysql' => $schemaMysql,
		]);
	}
	
	static private function genView($model, $views, $view)
	{
		$root = resource_path() . '/views/';
		$pathTpl = $root . 'templates/'; //ex: '/resources/views/templates/'
		$pathOut = $root . $views . '/'; //ex: '/resources/views/visitors/'
		if (!is_dir($pathOut))
			mkdir($pathOut, 0777);
		
		$viewFile = $view . '.blade.php'; 	 // ex: 'index.blade.php'
		$viewFileTpl = $pathTpl . $viewFile; // ex:  '/resources/views/templates/index.blade.php'
		$viewFileOut = $pathOut . $viewFile; // ex: '/resources/views/visitors/index.blade.php'
		
		$tpl = file_get_contents($viewFileTpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', strtolower($model), $tpl);
		file_put_contents($viewFileOut, $tpl);		
	}

	static private $routesTemplate = "

// GENERATED for Template model
use App\Http\Controllers\TemplateController;
	
// Templates
Route::group(['prefix' => 'templates'], function () {
	Route::get('/', [TemplateController::class, 'index']);
	Route::get('/index', [TemplateController::class, 'index']);
	Route::get('/view/{template}', [TemplateController::class, 'view']);

	// add
	Route::get('/add', [TemplateController::class, 'add']);
	Route::post('/create', [TemplateController::class, 'create']);
	
	// edit
	Route::get('/edit/{template}', [TemplateController::class, 'edit']);
	Route::post('/update/{template}', [TemplateController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{template}', [TemplateController::class, 'confirmDelete']);
	Route::post('/delete/{template}', [TemplateController::class, 'delete']);
});
";

	static private function genRoutes($model, $views)
	{
		$routesOut = base_path() . '/routes/web.php'; // ex: /routes/web.php
				
		$modelLc = strtolower($model);
		
		$tpl = self::$routesTemplate;
		$tpl = str_replace('Templates', ucfirst($views), $tpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', $modelLc, $tpl);
		file_put_contents($routesOut, $tpl, FILE_APPEND);
	}

	static private $schemaMysql = "
SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";
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

	static private function genSchemaMysql($table)
	{						
		$schemaOut = app_path() . '/' . $table . '.sql'; // ex: /app/visitors.sql
		$tpl = self::$schemaMysql;
		$tpl = str_replace('templates', $table, $tpl);
		file_put_contents($schemaOut, $tpl);
		return $tpl;
	}
	
}
