<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
   return $router->app->version();
   // echo "hello";
});

$router->post('/save', 'ExampleController@save');
$router->get('/sendsbmsas', 'Controller@sendsbmsas');
$router->get('/sendsbmsasbatch', 'Controller@sendsbmsasbatch');
$router->get('/testAES', 'Controller@testAES');
$router->get('/testAES1', 'Controller@testAES1');
$router->post('/receivesbmsas', 'Controller@receivesbmsas');
$router->post('/posttest', 'Controller@posttest');
//$router->post('/deletesbmsas', 'Controller@deletesbmsas');
$router->group( ['middleware' => 'auth'], function() use ($router) {

});
$router->group( ['middleware' => 'auth:mgt_api'], function() use ($router) {
    $router->get('/test', 'ExampleController@test');
    $router->get('/sendsbm', 'ExampleController@sendsbm');
   
    $router->post('/getsbm', 'ExampleController@getsbm');
    $router->get('/testdeleteSBM', 'ExampleController@testdeleteSBM');
    $router->get('/userinfo', 'ExampleController@userinfo');
    $router->post('/create_order', 'ExampleController@create_order');
    $router->get('/user/profile', function () {
        echo 'Nancy';
    });

});

