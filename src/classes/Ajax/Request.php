<?php

namespace Dolphy\Plugin\Ajax;

class Request {

  private $data = array();

  function __construct() {
    $method = $this->method();
    $contentType = $this->contentType();

    if ($method === 'GET') {
      $this->data = &$_GET;
    } else if (strpos($contentType, 'application/json') !== false) {
      $data = file_get_contents('php://input');
      $this->data = json_decode($data, true);
    } else {
      switch ($contentType) {
        // case 'application/x-www-form-urlencoded': {
        //   $this->data = &$_POST;
        // } break;
        // case 'multipart/form-data': {
        //   $this->data = &$_POST;
        // } break;
        default: {
          $this->data = &$_REQUEST;
        }
      }
    }
  }

  public function method() {
    return strtoupper($_SERVER['REQUEST_METHOD']);
  }

  public function contentType() {
    return strtolower($_SERVER['CONTENT_TYPE']);
  }

  public function action() {
    if ($value = $this->query('action')) return $value;
    return $this->input('action');
  }

  public function query($name, $default = NULL) {
    if (!array_key_exists($name, $_GET)) return $default;
    return $_GET[$name];
  }

  public function input($name, $default = NULL) {
    if (!array_key_exists($name, $this->data)) return $default;
    return $this->data[$name];
  }

  public function __get($name) {
    return $this->input($name);
  }

}
