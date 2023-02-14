<?php

namespace Dolphy\Core\Helpers;

use Dolphy\Core\Ajax\Response;
use Dolphy\Core\Exceptions\Exception;

class AdminPage {

  public static function url($name, $tag = NULL) {
    $slug = self::slug($name);

    $params = array('page' => $slug);
    if (!empty($tag)) {
      $params['tag'] = $tag;
    }

    $path = '/admin.php?' . http_build_query($params);

    return admin_url($path);
  }

  public static function slug($name = NULL) {
    $slug = DOLPHY_NAME;

    if (!empty($name) && $name !== DOLPHY_NAME) {
      $slug .= "-{$name}";
    }

    return $slug;
  }

  public static function page() {
    include DOLPHY_SETTINGS_INCLUDES.'/header.php';
    include DOLPHY_SETTINGS.'/page.php';
    include DOLPHY_SETTINGS_INCLUDES.'/footer.php';
  }

  public static function load($path) {
    if (substr($path, 0, 1) !== '/') {
      $path = '/' . $path;
    }

    $fullPath = DOLPHY_SETTINGS_PAGES . "{$path}.php";

    if (!is_file($fullPath)) {
      $fullPath = apply_filters('dolphy_admin_settings_page', $fullPath, $path);
    }

    if (!is_file($fullPath)) {
      throw new Exception("The file, \"{$fullPath}\", was not found.", 404);
    }

    ob_start();
    include $fullPath;
    return ob_get_clean();
  }

  public static function component() {
    $path = esc_url_raw($_GET['path']);

    try {
      $page = self::load($path);
      Response::send($page);
    } catch (Exception $e) {
      $page = DOLPHY_SETTINGS_ERRORS."/{$e->getCode()}.php";

      $data = $e->getMessage();

      if (is_file($page)) {
        ob_start();
        include $page;
        $data = ob_get_clean();
      }

      Response::send($data, $e->getCode());
    }
  }

}
