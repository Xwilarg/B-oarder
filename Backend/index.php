<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Boarder\Database\Database;

require __DIR__ . '/vendor/autoload.php';

$app = AppFactory::create();

$app->addRoutingMiddleware();

$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Define app routes
$app->get('/data/{name}/{data}', function (Request $request, Response $response, $args) {
    $name = $args['name'];
    $data = $args['data'];
    $res = (new Database())->get("SELECT amount FROM data WHERE id = ? and user_id = ?", $data, $name);
    if ($res === false) {
        $response->withStatus(400);
        return $response;
    }
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