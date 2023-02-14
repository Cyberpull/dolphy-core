<?php

namespace Dolphy\Plugin\Ajax;

class Response {

  public static function send($message, int $code = 200) {
    if (is_object($message) || is_array($message)) {
      $message = json_encode($message);
    }

    if (!$code) $code = 200;

    wp_die($message, $code);
  }

}
