<?php
Route::get('{url}', [
    'as' => 'all_route',
    'uses' => 'MainController@fallbackRouteAction'
])->where(['url' => '^((?!debugbar).)*$']);

Route::post('{url}', [
    'as' => 'all_route',
    'uses' => 'MainController@fallbackRouteAction'
])->where(['url' => '^((?!debugbar).)*$']);
