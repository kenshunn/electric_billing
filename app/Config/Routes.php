<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth'); //this is the default controller to check the user
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override(); //overrides the 404 error so i can set where and what shows up when the error occurs
$routes->setAutoRoute(false); //auto route is set to false to improve security where people can skip to the actual pages

// ─── AUTH ────────────────────────────────────────────
$routes->get('/',               'Auth::index'); //the default start of routes, where it uses the auth controller 
$routes->get('login',           'Auth::index'); //
$routes->post('login',          'Auth::login'); //when sumbitting the login, to redirect
$routes->get('logout',          'Auth::logout'); // submition of logout, will delete the entire session and redirect the user to the login or default page '/'

// ─── ADMIN ───────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) { //grouped to filter, the role of the user that will login, if its admin it can access the routes below
    $routes->get('dashboard',           'Admin::dashboard'); // default admin dashboard route
    $routes->get('users',               'Admin::users'); // user page
    $routes->post('users/store',        'Admin::storeUser'); // when creating a user
    $routes->post('users/update',       'Admin::updateUser'); // updating user details
    $routes->post('users/archive',       'Admin::deleteUser'); // deleting user
    $routes->get('users/get/(:num)',    'Admin::getUser/$1');
    $routes->get('audit',               'Admin::audit'); // for audit trail page
    $routes->get('audit/data',          'Admin::auditData'); //trail records
});

// ─── NORMAL USER ─────────────────────────────────────
$routes->group('user', ['filter' => 'auth:user'], function($routes) { //same as admin group, but for actual users only.
    $routes->get('dashboard',           'User::dashboard'); //user dashboard route
    $routes->get('billing',             'User::billing'); //billing page
    $routes->post('billing/compute',    'User::computeBill'); // to submit the actual computation of the bill
    $routes->get('billing/clients',     'User::getClients'); //existing clients from database
    $routes->get('history',             'User::history'); //history page
    $routes->get('history/data',        'User::historyData'); // history records
    $routes->get('trails',              'User::trails'); //trails
    $routes->get('trails/data',         'User::trailsData'); //trail records
});