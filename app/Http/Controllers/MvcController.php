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
			'add',
			'create',
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
		]);	
		
		$model = alpha(trimNull($request->model));
		if (!isset($model))
		{
			logError(__CLASS__, 'model not set');
			return redirect($this->redirectTo);
		}

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
		self::genView($model, 'add');
		self::genView($model, 'confirmdelete');
		self::genView($model, 'edit');
		self::genView($model, 'index');
		self::genView($model, 'menu-submenu');
		self::genView($model, 'view');

		return redirect('/mvc');
	}
	
	static private function genView($model, $view)
	{
		$root = resource_path() . '/views/';
		$pathTpl = $root . 'templates/';
		$pathOut = $root . strtolower($model) . 's/';
		if (!is_dir($pathOut))
			mkdir($pathOut, 0777);
		
		$viewFile = $view . '.blade.php'; 	 // ex: 'index.blade.php'
		$viewFileTpl = $pathTpl . $viewFile; //     '/resources/views/templates'
		$viewFileOut = $pathOut . $viewFile; // ex: '/resources/views/visitors'
		
		$tpl = file_get_contents($viewFileTpl);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', strtolower($model), $tpl);
		file_put_contents($pathOut . $viewFileOut, $tpl);		
	}
	
}
