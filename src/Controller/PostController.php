<?php

declare(strict_types=1);

namespace App\Controller;

use App\CustomResponse as Response;
use App\Helper;
use App\Service\PostService;
use Exception;
use Pimple\Psr11\Container;
use Psr\Http\Message\ServerRequestInterface as Request;

final class PostController
{
  private PostService $postService;

  public function __construct(private Container $container)
  {
    $this->postService = $this->container->get('postService');
  }

  public function getAll(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->postService->getAll();
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function getAllByUser(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->postService->getAllByUser($args['userId']);
      return $response->withJson($result);
    } catch (Exception $e) {
      return $response->withJson(['error' => $e->getMessage()], 500);
    }
  }

  public function getOne(Request $request, Response $response, array $args): Response
  {
    try {
      $result = $this->postService->getOne((string) $args['id']);
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
        'title' => $input['title'],
        'content' => $input['content'],
        'userId' => $input['userId'],
      ];

      foreach ($dto as $key => $value) {
        if ($value === null) {
          unset($dto[$key]);
        }
      }

      $this->postService->create($dto);
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
        'title' => $input['title'] ?: null,
        'content' => $input['content'] ?: null,
        'userId' => $input['userId'] ?: null,
        'updatedAt' => date('Y-m-d H:i:s'),
      ];

      foreach ($dto as $key => $value) {
        if ($value === null) {
          unset($dto[$key]);
        }
      }

      $this->postService->update((string) $args['id'], $dto);
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
    //   $result = $this->postService->delete((string) $args['id']);
    //   return $response->withJson($result);
    // } catch (Exception $e) {
    //   return $response->withJson(['error' => $e->getMessage()], 400);
    // }
    return $response->withJson(['error' => 'Disabled'], 400); // uncomment above code to enable delete
  }
}
