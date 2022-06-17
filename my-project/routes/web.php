<?php

use App\Models\Movies;
use App\Service\Neo4j;
use Laudis\Neo4j\ClientBuilder;
use App\Http\Controllers\Flight;
use App\Service\Flight as ServiceFlight;
use Illuminate\Support\Facades\DB;
use Laudis\Neo4j\Databags\Statement;
use Illuminate\Support\Facades\Route;
use Facade\Ignition\QueryRecorder\Query;
use Laudis\Neo4j\Authentication\Authenticate;

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

Route::get('/', [Flight::class, 'search']);

Route::get('/index', function () {
    
});
