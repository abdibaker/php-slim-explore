<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use App\Helper;
use App\Service\UserService;
use Exception;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;

final class UserController
{
  private UserService $userService;

  public function __construct(private Container $container)
  {
    $this->userService = $this->container->get('userService');
  }

  public function getAll(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->userService->getAll();
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function getOne(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->userService->getOne((string) $args['id']);
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 404);
    }
  }

  public function create(Request $request, Response $response, array $args): Response
  {
    try {
      $input = $request->getParsedBody();

      $dto = [
        'name' => $input['name'],
        'email' => $input['email'],
        'password' => $input['password'],
      ];

      foreach ($dto as $key => $value) {
        if ($value === null) {
          unset($dto[$key]);
        }
      }

      $this->userService->create($dto);
      return $response->withStatus(201);
    } catch (Exception $e) {
      $duplicateErrorCode = 1062;
      $foreignErrorCode = 1452;

      if ($e->getCode() === $duplicateErrorCode) {
        return $response->withJson(['error' => 'The data you try to insert already exists'], 409);
      } else if ($e->getCode() === $foreignErrorCode) {
        $error = Helper::getForeignKeyErrorMessage($e->getMessage());
        return $response->withJson(['error' => $error], 404);
      } else {
        return $response->withJson(['error' => $e->getMessage()], 500);
      }
    }
  }

  public function update(Request $request, Response $response, array $args): Response
  {
    try {
      $input = $request->getParsedBody();

      $dto = [
        'name' => $input['name'] ?: null,
        'email' => $input['email'] ?: null,
        'password' => $input['password'] ?: null,
        'updatedAt' => date('Y-m-d H:i:s'),
      ];

      foreach ($dto as $key => $value) {
        if ($value === null) {
          unset($dto[$key]);
        }
      }

      $this->userService->update((string) $args['id'], $dto);
      return $response->withStatus(204);
    } catch (Exception $e) {
      $duplicateErrorCode = 1062;
      $foreignErrorCode = 1452;

      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function delete(Request $request, Response $response, array $args): Response
  {
    // try {
    //   $result = $this->userService->delete((string) $args['id']);
    //   return $response->withJson($result);
    // } catch (Exception $e) {
    //   return $response->withJson(['error' => $e->getMessage()], 400);
    // }
    return $response->withJson(['error' => 'Disabled'], 400); // uncomment above code to enable delete
  }
}
