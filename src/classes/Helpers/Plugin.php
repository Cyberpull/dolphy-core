<?php

namespace Dolphy\Core\Helpers;

if (!function_exists('is_plugin_active_for_network')) {
  require_once ABSPATH.'/wp-admin/includes/plugin.php';
}

class Plugin {

  const NAME = 'dolphy/dolphy.php';

  private static $active = array();
  private static $networkActive = array();

  public static function isActive(string $name) {
    if (!isset(self::$active[$name])) {
      $activePlugins = apply_filters('active_plugins', get_option('active_plugins'));
      self::$active[$name] = in_array($name, $activePlugins);
    }

    return self::$active[$name];
  }

  public static function isActiveForNetwork(string $name = self::NAME) {
    if (!isset(self::$networkActive[$name])) {
      self::$networkActive[$name] = is_plugin_active_for_network($name);
    }

    return self::$networkActive[$name];
  }

}
