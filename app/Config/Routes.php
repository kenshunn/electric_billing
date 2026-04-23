<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Auth');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// ─── AUTH ────────────────────────────────────────────
$routes->get('/',               'Auth::index');
$routes->get('login',           'Auth::index');
$routes->post('login',          'Auth::login');
$routes->get('logout',          'Auth::logout');

// ─── ADMIN ───────────────────────────────────────────
$routes->group('admin', ['filter' => 'auth:admin'], function($routes) {
    $routes->get('dashboard',           'Admin::dashboard');
    $routes->get('users',               'Admin::users');
    $routes->post('users/store',        'Admin::storeUser');
    $routes->post('users/update',       'Admin::updateUser');
    $routes->post('users/delete',       'Admin::deleteUser');
    $routes->get('users/get/(:num)',    'Admin::getUser/$1');
    $routes->get('audit',               'Admin::audit');
    $routes->get('audit/data',          'Admin::auditData');
});

// ─── NORMAL USER ─────────────────────────────────────
$routes->group('user', ['filter' => 'auth:user'], function($routes) {
    $routes->get('dashboard',           'User::dashboard');
    $routes->get('billing',             'User::billing');
    $routes->post('billing/compute',    'User::computeBill');
    $routes->get('billing/clients',     'User::getClients');
    $routes->get('history',             'User::history');
    $routes->get('history/data',        'User::historyData');
    $routes->get('trails',              'User::trails');
    $routes->get('trails/data',         'User::trailsData');
});