<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Task;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


/**
 * Dashboard after login(only autheticate user)
 */
Route::get('/dashboard', function () {
    return view('dashboard',[
        'tasks' => Task::orderBy('created_at', 'asc')->get()
    ]);
})->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';

Route::group(['middleware' => ['web']], function () {
    /**
     * Add New Task
     */
    Route::post('/task', function (Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return redirect('/dashboard')
                ->withInput()
                ->withErrors($validator);
        }

        $task = new Task;
        $task->name = $request->name;
        $task->save();

        return redirect('/dashboard');
    });



    /**
     * Delete Task
     */
    Route::delete('/task/{id}', function ($id) {
        Task::findOrFail($id)->delete();

        return redirect('/dashboard');
    });



    /**
     * Update Task
     */
    Route::PUT('/task/{id}', function (Request $request ,$id) {
        $name = $request->input('name');
        Task::findOrFail($id)->update(['name' => $name]);
        return redirect('/dashboard')->with('status',"DONE");
    });

});


