<?php

namespace Dolphy\Plugin\Ajax;

use Dolphy\Core\Exceptions\Exception;
use Dolphy\Helpers\User;

class Action {

  protected static function name($method, $name, $prefix = NULL) {
    $method = strtolower($method);

    $action = "dolphy_ajax";

    if (!empty($action)) {
      $action .= "_{$prefix}";
    }

    $action .= "_{$method}_{$name}";

    return $action;
  }

  public static function has($method, $name, $prefix = NULL) {
    return has_action(self::name($method, $name, $prefix));
  }

  public static function add($method, $name, $callback) {
    if (self::has($method, $name)) {
      // throw new Exception('Action already exists', 500);
    } else {
      add_action(self::name($method, $name), $callback, 0, 2);
    }
  }

  public static function addAccount($method, $name, $callback) {
    if (self::has($method, $name, 'account')) {
      // throw new Exception('Action already exists', 500);
    } else {
      add_action(self::name($method, $name, 'account'), $callback, 0, 2);
    }
  }

  public static function addAdmin($method, $name, $callback) {
    if (self::has($method, $name, 'admin')) {
      // throw new Exception('Action already exists', 500);
    } else {
      add_action(self::name($method, $name, 'admin'), $callback, 0, 2);
    }
  }

  public static function call($method, $name) {
    if (!self::has($method, $name)) {
      throw new Exception('Action not found', 404);
    }

    $request = new Request;

    do_action_ref_array(self::name($method, $name), [&$request, null]);
  }

  public static function callAccount($method, $name) {
    if (!self::has($method, $name, 'account')) {
      throw new Exception('Action not found', 404);
    }

    if (!($user = User::current())) {
      throw new Exception('Access Denied', 401);
    }

    $request = new Request;

    do_action_ref_array(self::name($method, $name, 'account'), [&$request, $user]);
  }

  public static function callAdmin($method, $name) {
    if (!self::has($method, $name, 'admin')) {
      throw new Exception('Action not found', 404);
    }

    if (!($admin = User::currentAdmin())) {
      throw new Exception('Access Denied', 401);
    }

    $request = new Request;

    do_action_ref_array(self::name($method, $name, 'admin'), [&$request, $admin]);
  }

}
