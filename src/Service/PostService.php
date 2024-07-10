<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Exception;

final class PostService
{
  public function __construct(private Connection $conn)
  {
  }
  public function getAll(): array
  {
    return $this->conn->fetchAllAssociative(
      'SELECT id, title, content, userId
       FROM `posts`
       ORDER BY id ASC'
    );
  }


  public function getAllByUser($userId): array
  {
    return $this->conn->fetchAllAssociative(
      'SELECT id, title, content, userId
       FROM `posts`
       JOIN `users` ON `posts`.`userId` = `users`.`id`
        WHERE `users`.`id` = ?
       ORDER BY id ASC',
      [$userId]
    );
  }

  public function getOne(string $id): array
  {
    $result = $this->conn->fetchAssociative(
      'SELECT id, title, content, userId 
       FROM `posts` 
       WHERE id = ?',
      [$id]
    );

    if (!$result) {
      throw new Exception('Post not found');
    }
    return $result;
  }
  public function create($data): int|string
  {
    return $this->conn->insert('posts', $data);
  }

  public function update(string $id, $data): int|string
  {
    return $this->conn->update('posts', $data, ['id' => $id]);
  }

  public function delete(string $id): int|string
  {
    return $this->conn->delete('posts', ['id' => $id]);
  }
}
