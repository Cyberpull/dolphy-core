<?php

namespace Dolphy\Core\Exceptions;

use Exception as GlobalException;

class Exception extends GlobalException {

  function __construct($message, $code = 500) {
    $code = (is_numeric($code)) ? (int)$code : 500;
    parent::__construct($message, $code);
  }

}
