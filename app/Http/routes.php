<?php

Route::get('/testing', function(){
    $inboxes = Talk::user(auth()->user()->id)->getInbox();
    foreach ($inboxes as $inbox) {
        echo "<h2>". $inbox->withUser->name. "</h2>";
        echo "<br>";
        echo "<p>". $inbox->thread->message. "</p>";
        echo "<span>". $inbox->thread->humans_time. "</span>";
        echo "<hr>";
    }
});

Route::get('/roles', function() {
    $owner = new App\Role();
    $owner->name         = 'coordinador';
    $owner->display_name = 'Usuario Coordinador'; // optional
    $owner->description  = 'Coordinador del sistema con todos los privilegios'; // optional
    $owner->save();

    $admin = new App\Role();
    $admin->name         = 'graduate';
    $admin->display_name = 'Usuario Egresado'; // optional
    $admin->description  = 'Egresado el cual hara uso de los servicios de SEDSC'; // optional
    $admin->save();

    $admin = new App\Role();
    $admin->name         = 'company';
    $admin->display_name = 'Usuario Empresa'; // optional
    $admin->description  = 'Empresa la cual podra publicar sus vacantes en SEDSC'; // optional
    $admin->save();
    echo "Listo";
});
/*============== Main Routes ==============*/
Route::get('/', function () {
    return view('welcome');
});

Route::get('/register-graduate', function(){
    return view('auth.register-graduate');
});

Route::get('/register-company', function(){
    return view('auth.register-company');
});

Route::auth();

Route::get('/home', 'HomeController@index');

Route::get('/graduates', function(){
	return view('front-end.graduates');
});

Route::get('/company', function(){
	return view('front-end.company');
});

Route::get('/projects', function(){
	return view('front-end.projects');
});

Route::get('/organization', function(){
	return view('front-end.organization');
});

Route::get('/academic', function(){
	return view('front-end.academic');
});

Route::resource('graduates', 'GraduateController');

Route::get('/viewdata/{id}', 'GraduateController@data');

Route::get('graduates/{id}/delete', [
    'as' => 'graduates.delete',
    'uses' => 'GraduateController@destroy',

    ]);

Route::resource('companies', 'CompanyController');

Route::get('companies/{id}/delete', [
    'as' => 'companies.delete',
    'uses' => 'CompanyController@destroy',
    ]);

Route::get('view-company/{id}', function($id){
  $company = App\Models\Company::find($id);

  return view('companies.show')  
  ->with('company', $company);
});

Route::get('viewcompany/{id}', function($id) {
    $company = App\Models\Company::find($id);
    $user = $company->user;
    return view('administrator.show-company')
    ->with('company', $company)
    ->with('user', $user);
});

Route::get('viewvacacy/{id}', function($id) {
    $vacancy = App\Models\Vacancy::find($id);   
     $user = $company->user; 
    return view('vacancies.viewvacacy')
    ->with('company', $company)
    ->with('vacancy', $vacancy);
});
/*============== End Main Routes ==============*/
Route::group(['middleware' => 'auth'], function(){

   Route::resource('labors', 'LaborController');

   Route::get('labors/{id}/delete', [
    'as' => 'labors.delete',
    'uses' => 'LaborController@destroy',
    ]);


   Route::resource('vacancies', 'VacancyController');

   Route::get('vacancies/{id}/delete', [
    'as' => 'vacancies.delete',
    'uses' => 'VacancyController@destroy',
    ]); 

   Route::get('vacancyphoto', 'VacancyController@vacancyphoto');


   Route::resource('residents', 'ResidentsController');

   Route::get('residents/{id}/delete', [
    'as' => 'residents.delete',
    'uses' => 'ResidentsController@destroy',
    ]);


   Route::resource('services', 'ServiceController');

   Route::get('services/{id}/delete', [
    'as' => 'services.delete',
    'uses' => 'ServiceController@destroy',
    ]);
});





Route::resource('polls', 'PollController');

Route::get('polls/{id}/delete', [
    'as' => 'polls.delete',
    'uses' => 'PollController@destroy',
    ]);

/*============== Administrator ==============*/
/*
* Module Graduate for Administrator
*/
Route::get('allgraduates', 'AdminController@graduates');
Route::get('create-graduate', 'AdminController@createGraduate');
Route::post('create-graduate', 'AdminController@storeGraduate');



Route::get('allcompanies', 'AdminController@companies');
Route::get('create-company', 'AdminController@createCompany');
Route::post('create-company', 'AdminController@storeCompany');

Route::get('allservices', 'AdminController@services');
Route::get('allresidents', 'AdminController@residents');
Route::get('allvacancies', 'AdminController@vacancies');
/*
** Routes Chat
*/
Route::get('adminchat', function() {
    $role = App\Role::where('name', 'coordinador')->first();
    $users = $role->users; 
    return view('chat.users')
    ->with('users', $users);
});

Route::get('chatgraduates', function() {
    $role = App\Role::where('name', 'graduate')->first();
    $users = $role->users; 
    return view('chat.users')
    ->with('users', $users);
});

Route::get('message/{id}', 'MessageController@chatHistory')->name('message.read');

Route::group(['prefix'=>'ajax', 'as'=>'ajax::'], function() {
 Route::post('message/send', 'MessageController@ajaxSendMessage')->name('message.new');
 Route::delete('message/delete/{id}', 'MessageController@ajaxDeleteMessage')->name('message.delete');
});

Route::get('viewvacancies', function() {
    $vacancies = App\Models\Vacancy::all();
    return view('graduates.vacancies')
    ->with('vacancies', $vacancies);
});


Route::resource('periods', 'PeriodController');

Route::get('periods/{id}/delete', [
    'as' => 'periods.delete',
    'uses' => 'PeriodController@destroy',
    ]);

Route::get('viewdatagraduate/{id}', function($id) {
    $graduate = App\Models\Graduate::find($id);
    $labor = $graduate->labor;

    if(empty($graduate))
    {
        Flash::error('Graduate not found');
        return redirect(route('graduates.index'));
    }

    return view('administrator.show-profile')
    ->with('graduate', $graduate)
    ->with('labor', $labor);
});

Route::get('viewtestgraduate/{id}', function($id) {
    $graduate = App\Models\Graduate::find($id);

    if(empty($graduate))
    {
        Flash::error('Graduate not found');
        return redirect(route('graduates.index'));
    }

    return view('administrator.show-test')
    ->with('graduate', $graduate);
});

Route::get('test-pdf/{id}', function($id) {
    $data = App\Models\Graduate::find($id);
    $pdf = PDF::loadView('pdf.invoice', compact('data'));
    return $pdf->download('perfil.pdf');
});

Route::get('test2', function() {
    $graduates = App\Models\Graduate::all();
    $chunk = $graduates->take(-4);

    $chunk->all();
    foreach ($chunk as $key => $value) {
        echo $value->name;
        echo "<br>";
    }
});

Route::resource('serviceAdmins', 'ServiceAdminController');

Route::get('serviceAdmins/{id}/delete', [
    'as' => 'serviceAdmins.delete',
    'uses' => 'ServiceAdminController@destroy',
    ]);


Route::resource('vacancyAdmins', 'VacancyAdminController');

Route::get('vacancyAdmins/{id}/delete', [
    'as' => 'vacancyAdmins.delete',
    'uses' => 'VacancyAdminController@destroy',
    ]);


Route::resource('residentAdmins', 'ResidentAdminController');

Route::get('residentAdmins/{id}/delete', [
    'as' => 'residentAdmins.delete',
    'uses' => 'ResidentAdminController@destroy',
    ]);
