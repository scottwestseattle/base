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

		$modelFile = app_path() . '/' . $model . '.php';
		$controllerFile = app_path() . '/Http/Controllers/' . $model . 'Controller.php';

		// generate the Model
		$tpl = file_get_contents('./files/Model.php');
		$tpl = str_replace('Template', $model, $tpl);
		file_put_contents($modelFile, $tpl);
		
		// generate the Controller
		$tpl = file_get_contents('./files/Controller.php');
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', strtolower($model), $tpl);
		file_put_contents($controllerFile, $tpl);
		
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
		$viewFile = $view . '.blade.php';
		$path = resource_path() . '/views/' . strtolower($model) . 's/';
		if (!is_dir($path))
			mkdir($path, 0777);
		
		$tpl = file_get_contents('./files/' . $viewFile);
		$tpl = str_replace('Template', $model, $tpl);
		$tpl = str_replace('template', strtolower($model), $tpl);
		file_put_contents($path . $viewFile, $tpl);		
	}
	
}
