<?php

namespace Dolphy\Core\Helpers;

use Dolphy\Core\Exceptions\Exception;

class Page {

  public static function title($title) {
    $sitename = get_bloginfo('name');

    $title = trim($title);
    if (empty($title)) return $sitename;

    $title .= " | {$sitename}";

    return $title;
  }

  public static function currentPageTitle() {
    $post = get_post();
    return self::title($post->post_title);
  }

}
