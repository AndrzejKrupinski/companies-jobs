<?php

$router->get('/', function () {
    return view('requirement-form');
});

$router->post('/company', 'CompanyController@show')->name('company.show');
$router->post('/requirement', 'RequirementController@index')->name('requirement.index');
