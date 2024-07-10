<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Exception;

final class UserService
{
  public function __construct(private Connection $conn)
  {
  }
  public function getAll(): array
  {
    return $this->conn->fetchAllAssociative(
      'SELECT id, name, email, password
       FROM `users`
       ORDER BY id ASC'
    );
  }

  public function getOne(string $id): array
  {
    $result = $this->conn->fetchAssociative(
      'SELECT id, name, email, password 
       FROM `users` 
       WHERE id = ?',
      [$id]
    );

    if (!$result) {
      throw new Exception('User not found');
    }
    return $result;
  }
  public function create($data): int|string
  {
    return $this->conn->insert('users', $data);
  }

  public function update(string $id, $data): int|string
  {
    return $this->conn->update('users', $data, ['id' => $id]);
  }

  public function delete(string $id): int|string
  {
    return $this->conn->delete('users', ['id' => $id]);
  }
}
