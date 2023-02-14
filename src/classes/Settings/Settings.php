<?php

use Dolphy\Plugin\Core\Model;

class Settings extends Model {

  private static $xInstance = array();

  function __construct(array $data = NULL) {
    parent::__construct($data);

    if (is_null($data)) {
      // $this->refresh();
    }
  }

  final public static function instance() {
    if (!array_key_exists(static::class, self::$xInstance)) {
      self::$xInstance[static::class] = new static();
    }

    return self::$xInstance[static::class];
  }

}
