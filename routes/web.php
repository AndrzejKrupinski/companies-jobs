<?php

$router->get('/', function () {
    return view('requirement-form');
});

$router->post('/companies', 'JobFindController@companies')->name('companies');
$router->post('/requirements', 'JobFindController@requirements')->name('requirements');
