<?php

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
Auth::routes();

Route::get('/','HomeController@index');
Route::get('home', 'HomeController@index')->name('home');
Route::get('/documents', 'DocumentsController@index');
Route::get('houses', 'HousesController@index');
Route::post('houses', 'HousesController@store');
Route::get('houses/{id}', 'HousesController@show');
Route::post('houses/{id}/update', 'HousesController@update');
Route::post('houses/{id}/delete', 'HousesController@destroy');
Route::get('houses/find', 'HousesController@find');
Route::get('houses/download-pdf', 'HousesController@downloadPdf');
Route::get('houses/download-excel', 'HousesController@downloadExcel');
Route::get('houses/download-csv', 'HousesController@downloadCsv');
Route::get('houses/count', function(){ return \App\Houses::count();});
Route::get('houses/pdf', function(){
    $houses = \App\Houses::all();
    return view('downloads.houses', compact('houses'));
});

Route::get('classes', 'ClassesController@index');
Route::post('classes', 'ClassesController@store');
Route::get('classes/{id}', 'ClassesController@show');
Route::post('classes/{id}/update', 'ClassesController@update');
Route::post('classes/{id}/delete', 'ClassesController@destroy');
Route::get('classes/find', 'ClassesController@find');
Route::get('classes/download-pdf', 'ClassesController@downloadPdf');
Route::get('classes/download-excel', 'ClassesController@downloadExcel');
Route::get('classes/download-csv', 'ClassesController@downloadCsv');
Route::get('classes/count', function(){ return \App\Classes::count();});
Route::get('classes/pdf', function(){
    $classes = \App\Classes::all();
    return view('downloads.classes', compact('classes'));
});

Route::get('exams', 'ExamsController@index');
Route::post('exams', 'ExamsController@store');
Route::get('exams/{id}', 'ExamsController@show');
Route::post('exams/{id}/update', 'ExamsController@update');
Route::post('exams/{id}/delete', 'ExamsController@destroy');
Route::get('exams/find', 'ExamsController@find');
Route::get('exams/download-pdf', 'ExamsController@downloadPdf');
Route::get('exams/download-excel', 'ExamsController@downloadExcel');
Route::get('exams/download-csv', 'ExamsController@downloadCsv');
Route::get('exams/count', function(){ return \App\Exams::count();});
Route::get('exams/pdf', function(){
    $exams = \App\Exams::all();
    return view('downloads.exams', compact('exams'));
});



Route::get('roles', 'RolesController@index');
Route::post('roles', 'RolesController@store');
Route::get('roles/{id}', 'RolesController@show');
Route::post('roles/{id}/update', 'RolesController@update');
Route::post('roles/{id}/delete', 'RolesController@destroy');
Route::get('roles/find', 'RolesController@find');
Route::get('roles/download-pdf', 'RolesController@downloadPdf');
Route::get('roles/download-excel', 'RolesController@downloadExcel');
Route::get('roles/download-csv', 'RolesController@downloadCsv');
Route::get('roles/count', function(){ return \App\Roles::count();});
Route::get('roles/pdf', function(){
    $roles = \App\Roles::all();
    return view('downloads.roles', compact('roles'));
});

Route::get('settings', 'SettingsController@index');
Route::post('settings', 'SettingsController@store');
Route::post('settings/updateLogo', 'SettingsController@updateLogo');
Route::get('settings/{id}', 'SettingsController@show');
Route::post('settings/{id}/update', 'SettingsController@update');
Route::delete('settings/{id}/delete', 'SettingsController@destroy');
Route::get('settings/find', 'SettingsController@find');
Route::get('settings/download-pdf', 'SettingsController@downloadPdf');
Route::get('settings/download-excel', 'SettingsController@downloadExcel');
Route::get('settings/download-csv', 'SettingsController@downloadCsv');
Route::get('settings/count', function(){ return \App\Settings::count();});
Route::get('settings/pdf', function(){
    $settings = \App\Settings::all();
    return view('downloads.settings', compact('settings'));
});


Route::get('subjects', 'SubjectsController@index');
Route::post('subjects', 'SubjectsController@store');
Route::get('subjects/{id}', 'SubjectsController@show');
Route::post('subjects/{id}/update', 'SubjectsController@update');
Route::delete('subjects/{id}/delete', 'SubjectsController@destroy');
Route::get('subjects/find', 'SubjectsController@find');
Route::get('subjects/download-pdf', 'SubjectsController@downloadPdf');
Route::get('subjects/download-excel', 'SubjectsController@downloadExcel');
Route::get('subjects/download-csv', 'SubjectsController@downloadCsv');
Route::get('subjects/count', function(){ return \App\Subjects::count();});
Route::get('subjects/pdf', function(){
    $subjects = \App\Subjects::all();
    return view('downloads.subjects', compact('subjects'));
});

Route::get('settings', 'SettingsController@index');
Route::post('settings', 'SettingsController@store');
Route::post('settings/updateLogo', 'SettingsController@updateLogo');
Route::get('settings/{id}', 'SettingsController@show');
Route::post('settings/{id}/update', 'SettingsController@update');
Route::delete('settings/{id}/delete', 'SettingsController@destroy');
Route::get('settings/find', 'SettingsController@find');
Route::get('settings/download-pdf', 'SettingsController@downloadPdf');
Route::get('settings/download-excel', 'SettingsController@downloadExcel');
Route::get('settings/download-csv', 'SettingsController@downloadCsv');
Route::get('settings/count', function(){ return \App\Settings::count();});
Route::get('settings/pdf', function(){
    $settings = \App\Settings::all();
    return view('downloads.settings', compact('settings'));
});


Route::get('students', 'StudentsController@index');
Route::post('students', 'StudentsController@store');
Route::get('students/last-adm', 'StudentsController@getLastAdm');
Route::get('students/{id}', 'StudentsController@show');
Route::post('students/{id}/update', 'StudentsController@update');
Route::post('students/{id}/delete', 'StudentsController@destroy');
Route::get('students/find', 'StudentsController@find');
Route::get('students/download-pdf', 'StudentsController@downloadPdf');
Route::get('students/download-excel', 'StudentsController@downloadExcel');
Route::get('students/download-csv', 'StudentsController@downloadCsv');
Route::get('students/count', function(){ return \App\Students::count();});
Route::get('students/pdf', function(){
    $students = \App\Students::all();
    return view('downloads.students', compact('students'));
});


Route::get('teachers', 'TeachersController@index');
Route::post('teachers', 'TeachersController@store');
Route::get('teachers/{id}', 'TeachersController@show');
Route::post('teachers/{id}/update', 'TeachersController@update');
Route::post('teachers/{id}/delete', 'TeachersController@destroy');
Route::get('teachers/find', 'TeachersController@find');
Route::get('teachers/download-pdf', 'TeachersController@downloadPdf');
Route::get('teachers/download-excel', 'TeachersController@downloadExcel');
Route::get('teachers/download-csv', 'TeachersController@downloadCsv');
Route::get('teachers/count', function(){ return \App\Teachers::count();});
Route::get('teachers/pdf', function(){
    $teachers = \App\Teachers::all();
    return view('downloads.teachers', compact('teachers'));
});


Route::get('user_types', 'UserTypeController@index');
Route::post('user_types', 'UserTypeController@store');
Route::get('user_types/{id}', 'UserTypeController@show');
Route::post('user_types/{id}/update', 'UserTypeController@update');
Route::post('user_types/{id}/delete', 'UserTypeController@destroy');
Route::get('user_types/find', 'UserTypeController@find');
Route::get('user_types/download-pdf', 'UserTypeController@downloadPdf');
Route::get('user_types/download-excel', 'UserTypeController@downloadExcel');
Route::get('user_types/download-csv', 'UserTypeController@downloadCsv');
Route::get('user_types/count', function(){ return \App\UserTypes::count();});
Route::get('user_types/pdf', function(){
    $user_types = \App\UserTypes::all();
    return view('downloads.user_types', compact('user_types'));
});

Route::get('staffs', 'StaffsController@index');
Route::post('staffs', 'StaffsController@store');
Route::get('staffs/{id}', 'StaffsController@show');
Route::post('staffs/{id}/update', 'StaffsController@update');
Route::delete('staffs/{id}', 'StaffsController@destroy');
Route::get('staffs/find', 'StaffsController@find');
Route::get('staffs/download-pdf', 'StaffsController@downloadPdf');
Route::get('staffs/download-excel', 'StaffsController@downloadExcel');
Route::get('staffs/download-csv', 'StaffsController@downloadCsv');
Route::get('staffs/count', function(){
    $request = new \Request();
    $staff_type = \Request::get('staff_type');
    if(isset($staff_type)){
        $staffTypeId =\App\UserTypes::where('name', '=', $staff_type)->first()->id;
        return \App\Staffs::where('staff_type_id', $staffTypeId)->count();
    }else{
        return \App\Staffs::count();
    }

});
Route::get('staffs/pdf', function(){
    $staffs = \App\Staffs;
    return view('downloads.staffs', compact('staffs'));
});


Route::get('marks', 'MarksController@index');
Route::post('marks', 'MarksController@store');
Route::get('marks/{id}', 'MarksController@show');
Route::post('marks/{id}/update', 'MarksController@update');
Route::post('marks/{id}/delete', 'MarksController@destroy');
Route::get('marks/find', 'MarksController@find');
Route::get('marks/download-pdf', 'MarksController@downloadPdf');
Route::get('marks/download-excel', 'MarksController@downloadExcel');
Route::get('marks/download-csv', 'MarksController@downloadCsv');
Route::get('marks/count', function(){ return \App\Marks::count();});
Route::get('marks/pdf', function(){
    $subjects = \App\Marks::all();
    return view('downloads.marks', compact('marks'));
});


Route::post('uploads', 'UploadsController@index');

Route::post('logs', 'LogsController@store');

Route::get('{path}','HomeController@index')->where('path', '([A-z\d-/_.]+)?' );
