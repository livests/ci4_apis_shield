<?php

use CodeIgniter\Router\RouteCollection;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\ProjectController;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

//service('auth')->routes($routes);

//api routes

$routes->post("/api/register", [AuthController::class, "register"]);
$routes->post("/api/login", [AuthController::class, "login"]);

$routes->group("api", ["namespace"=>"App\Controllers\Api", "filter" => "shield_auth"], function($routes) {
    $routes->get("profile", [AuthController::class, "profile"]);
    $routes->get("logout", [AuthController::class, "logout"]);
    $routes->post("add-project", [ProjectController::class, "addProject"]);
    $routes->get("list-projects", [ProjectController::class, "getProjects"]);
});
