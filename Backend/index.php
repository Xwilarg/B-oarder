<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Boarder\Database;

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/data/{name}/{data}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $data = $args['data'];
    $res = new Database().get("SELECT amount FROM data WHERE id = ? and user_id = ?", $data, $name);
    $response->getBody()->write($res);
    return $response;
});
$app->post('/data/{name}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $response->getBody()->write("tried to post $name");
    return $response;
});

// Run app
$app->run();