<?php

namespace Dolphy\Core\Database;

use Dolphy\Core\Exceptions\Exception;
use JsonSerializable;
use ReflectionException;
use ReflectionMethod;

class Model implements JsonSerializable {

  const FIELDS = array();

  const DEFAULT = array();

  const WITH = array();

  private $attribute = array();

  function __construct(array $data = NULL) {
    $data = (!is_null($data)) ? $data : array();
    $this->fill($data, true);
  }

  public function toArray() {
    $result = array();

    foreach (static::FIELDS as $name => $type) {
      $value = $this->getAttribute($name);
      if (!is_null($value)) {
        $result[$name] = $value;
      }
    }

    foreach (static::WITH as $name) {
      $value = $this->{$name};
      if (!is_null($value)) {
        $result[$name] = $value;
      }
    }

    return $result;
  }

  public function jsonSerialize() {
    return (object)$this->toArray();
  }

  public function clear() {
    $this->attribute = array();
  }

  final public function fill($data, $clear = false) {
    if (is_object($data)) $data = (array)$data;
    if (!is_array($data)) $data = array();

    if ($clear === true) $this->clear();

    foreach ($data as $name => $value) {
      $this->__set($name, $value);
    }

    return $this;
  }

  final protected function getAttribute($name) {
    if (array_key_exists($name, $this->attribute)) {
      return $this->attribute[$name];
    } else if (array_key_exists($name, static::DEFAULT)) {
      return static::DEFAULT[$name];
    }

    return NULL;
  }

  final protected function setAttribute($name, $value) {
    $fields = (is_array(static::FIELDS)) ? static::FIELDS : array();

    if (array_key_exists($name, $fields)) {
      $this->attribute[$name] = $value;
    }
  }

  final protected function call($name, ...$arguments) {
    try {
      $method = new ReflectionMethod(static::class, $name);

      if ($method->isPrivate()) {
        $message = "'{$method->name}' in '".static::class."' cannot be private.";
        throw new ReflectionException($message);
      }

      $method->setAccessible(true);

      return $method->invoke($this, ...$arguments);
    } catch (ReflectionException $e) {
      throw new Exception($e->getMessage());
    }
  }

  public function __get($name) {
    $method = ucfirst($name);
    $method = "get{$method}Attribute";

    try {
      return $this->call($method, $name);
    } catch (Exception $e) {
      return $this->getAttribute($name);
    }
  }

  public function __set($name, $value) {
    $method = ucfirst($name);
    $method = "set{$method}Attribute";

    try {
      $this->call($method, $value);
    } catch (Exception $e) {
      $this->setAttribute($name, $value);
    }
  }

  public function __toString() {
    $data = $this->jsonSerialize();
    return json_encode($data);
  }

}
