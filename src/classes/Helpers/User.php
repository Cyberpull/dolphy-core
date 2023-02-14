<?php

namespace Dolphy\Core\Helpers;

use Dolphy\Core\Exceptions\Exception;

class User {

  public static function findBy($field, $value) {
    if ($value === NULL) {
      $user = \wp_get_current_user();
    } else {
      $user = \get_user_by($field, $value);
    }

    if (!$user || !$user->ID) return NULL;

    return $user;
  }

  public static function get($id = NULL) {
    return self::findBy('id', $id);
  }

  public static function current() {
    return self::get();
  }

  public static function currentAdmin() {
    $user = self::get();

    // Check if user is admin

    return $user;
  }

}
