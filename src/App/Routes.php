<?php

declare(strict_types=1);

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\App;

return static function (App $app) {

  $authMiddleware = function (Request $request, RequestHandler $handler): Response {
    $jwt = $request->getHeaderLine('Authorization');

    if (empty($jwt)) {
      throw new Exception('JWT Token required.', 400);
    }

    try {
      $key = new Key('7w8&^7af9*!o%j#)b$#k*p2w#q9@s1z&3n1!&y^vq36znm7!%h', 'HS256');
      $decoded = JWT::decode($jwt, $key);
    } catch (Exception) {
      throw new Exception('Forbidden: you are not authorized.', 403);
    }

    $parsedBody = $request->getParsedBody() ?: [];
    $parsedBody['decoded'] = $decoded;
    $request = $request->withParsedBody($parsedBody);

    return $handler->handle($request);
  };

  // --------------- Home Routes ---------------- //
  $homeController = 'App\Controller\Home:';

  $app->get('/', "{$homeController}api");
  $app->get('/swagger-ui', "{$homeController}swagger");
  $app->get('/api', "{$homeController}getHelp");
  $app->get('/status', "{$homeController}getStatus");


  // --------------- User Routes ---------------- //
  $app->group('/users', function ($app) {
    $user = 'App\Controller\UserController:';

    $app->get('', "{$user}getAll");
    $app->post('', "{$user}create");
    $app->get('/{id}', "{$user}getOne");
    $app->put('/{id}', "{$user}update");
    $app->delete('/{id}', "{$user}delete");
  })->add($authMiddleware);


  // --------------- Post Routes ---------------- //
  $app->group('/posts', function ($app) {
    $post = 'App\Controller\PostController:';

    $app->get('', "{$post}getAll");
    $app->get('/users/{userId}', "{$post}getAllByUser");
    $app->post('', "{$post}create");
    $app->get('/{id}', "{$post}getOne");
    $app->put('/{id}', "{$post}update");
    $app->delete('/{id}', "{$post}delete");
  });

  return $app;
};
