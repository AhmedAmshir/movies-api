<?php

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
});

$router->group(['prefix' => '{api}'], function () use ($router) {
    
    $router->get('/movies', ['as' => 'movies_homepage', 'uses' => 'MoviesController@index']);

    $router->get('/movie', ['as' => 'movie_homepage', 'uses' => 'MoviesController@movieDetails']);

    $router->post('/update-movie', ['as' => 'movie_update', 'uses' => 'MoviesController@updateMovie']);

    $router->delete('/movie', ['as' => 'movie_delete', 'uses' => 'MoviesController@deleteMovie']);

    $router->post('/movie', ['as' => 'post_movie', 'uses' => 'MoviesController@AddNewMovie']);
});
