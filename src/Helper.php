<?php
declare(strict_types=1);

namespace App;

use Pimple\Psr11\Container;


class Helper
{
  public static function hashPassword($password)
  {
    $options = ['type' => 'argon2', 'memoryCost' => 2048, 'timeCost' => 4, 'threads' => 3];
    $hashedPassword = password_hash($password, PASSWORD_ARGON2I, $options);
    return $hashedPassword;
  }

  public static function getForeignKeyErrorMessage($errorMessage)
  {
    $matches = [];
    preg_match(
      "/FOREIGN KEY \(`(\w+)`\) REFERENCES `(\w+)` \(`(\w+)`\)/",
      $errorMessage,
      $matches
    );

    if (count($matches) >= 4) {
      $childColumnName = $matches[1];
      $parentTableName = $matches[2];
      $parentColumnName = $matches[3];

      return "The '{$childColumnName}' does not exist in the '{$parentTableName}' table column '{$parentColumnName}'.";
    }

    return "Foreign key constraint violation occurred, but couldn't extract specific details from the error message.";
  }

}