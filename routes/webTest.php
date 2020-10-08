
// Visitors (GENERATED)
Route::group(['prefix' => 'visitors'], function () {
	Route::get('/', [VisitorController::class, 'index']);
	Route::get('/index', [VisitorController::class, 'index']);
	Route::get('/view/{visitor}', [VisitorController::class, 'view']);

	// add
	Route::get('/add', [VisitorController::class, 'add']);
	Route::post('/create', [VisitorController::class, 'create']);
	
	// edit
	Route::get('/edit/{visitor}', [VisitorController::class, 'edit']);
	Route::post('/update/{visitor}', [VisitorController::class, 'update']);

	// delete
	Route::get('/confirmdelete/{visitor}', [VisitorController::class, 'confirmDelete']);
	Route::post('/delete/{visitor}', [VisitorController::class, 'delete']);
});
