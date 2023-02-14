<?php

namespace Dolphy\Core\Options;

use Closure;
use Dolphy\Core\Models\Model;
use Dolphy\Core\Exceptions\Exception;
use Dolphy\Helpers\DB;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;

class Options extends Model {

  const NAME = '';

  const AUTOLOAD = true;

  private static $xInstance = array();

  function __construct(array $data = NULL) {
    parent::__construct($data);

    if (is_null($data)) {
      $this->refresh();
    }
  }

  /**
   * Options Instance
   *
   * @param array $data
   *
   * @return static
   */
  final public static function instance(array $data = NULL) {
    if (!array_key_exists(static::class, self::$xInstance)) {
      self::$xInstance[static::class] = new static($data);
    }

    return self::$xInstance[static::class];
  }

  final public static function table() {
    return DB::tbl('options');
  }

  final public function clear() {
    parent::clear();
  }

  final public function exists() {
    global $wpdb;

    $table = self::table();
    $name = static::NAME;

    $query = $wpdb->prepare("SELECT * FROM {$table} WHERE `name` = %s", $name);

    if ($wpdb->get_row($query)) {
      return true;
    }

    return false;
  }

  final public function refresh() {
    global $wpdb;

    $table = self::table();
    $name = static::NAME;

    $query = $wpdb->prepare("SELECT * FROM {$table} WHERE `name` = %s", $name);

    if ($result = $wpdb->get_row($query)) {
      $data = unserialize($result->value);
      $data = $this->unescape($data);
      $this->fill($data, true);
    } else {
      $this->clear();
    }

    return $this;
  }

  final public function save() {
    global $wpdb;

    try {
      $result = $this->call('onBeforeSave');
      if ($result === false) return $this;
    } catch (Exception $e) {
      // ...
    }

    $table = self::table();

    $name = static::NAME;

    $value = $this->toArray();
    $value = $this->escape($value);
    $value = serialize($value);

    if ($this->exists()) {
      $where = array('name' => $name);

      $wpdb->update(
        $table,
        array('value' => $value),
        $where
      );
    } else {
      $wpdb->insert($table, array(
        'name' => $name,
        'value' => $value
      ));
    }

    $this->refresh();

    try {
      $this->call('onAfterSave');
    } catch (Exception $e) {
      // ...
    }

    return $this;
  }

  final protected function escape(array $data = NULL) {
    if (!is_array($data)) return NULL;

    foreach ($data as $name => &$value) {
      $method = ucfirst($name);
      $method = "escape{$method}Attribute";

      try {
        $value = $this->call($method, $value);
      } catch (Exception $e) {
        //
      }
    }

    return $data;
  }

  final protected function unescape(array $data = NULL) {
    if (!is_array($data)) return NULL;

    foreach ($data as $name => &$value) {
      $method = ucfirst($name);
      $method = "unescape{$method}Attribute";

      try {
        $value = $this->call($method, $value);
      } catch (Exception $e) {
        //
      }
    }

    return $data;
  }

}
