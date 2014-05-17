<?php

/**
 * To make this exemple work properly you should create a '.htaccess' file such as :
 *
 * RewriteEngine On
 * RewriteCond %{REQUEST_FILENAME} !-d
 * RewriteCond %{REQUEST_FILENAME} !-f
 * RewriteRule ^.*$ /exemple.php [L]
 */

require_once('s2f.phar');
require_once('s2f-route.phar');

use s2f\s2f as s2f;

//Create the application called app and load core and route dependency
$app = s2f::module('app', ['core', 'route']);

//This function is a callback handler for a specific route (see below)
function handleRequest($response, $request, $routeParams, $testor)
{
  //Test and get value of $routeParams->id in one line
  $id = $testor($routeParams->id)->isNumeric()->isNotEmpty()->getValue();

  //Send a json answer
  $response->sendJson(array('user' => array('id' => $id)));
}

//This function is a callback that will be called on unknow route
function notFound($response)
{
  //Set HTTP status code
  $response->setCode(404);

  //Send a response
  $response->send("NOT FOUD");
}

//All the function registred with config will be called automaticly with injected dependency
$app->config(function ($testor, $routeProvider) {

    //Register a route for /user/:id that will be handled by handleRequest callback. the id path parameter will be accecible in the $routeParams injectable
    $routeProvider->get('/user/:id', 'handleRequest');

    //Set a fallback handler
    $routeProvider->otherwise('notFound');

  });

//All the function register with run will be called after all the config callback.
$app->run(function ($testor) {
  });

//This line is the execution entry point it MUST be called after everything else is set.
s2f::run();

?>