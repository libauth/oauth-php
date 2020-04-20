<?php

namespace LibAuth\Core;

use ReflectionClass;

class DataCore {

  const FIELDS = [];

  private $fields = [];

  private $data;

  final function __construct($data = NULL) {
    $this->clear();
    $this->initializeFields();
    $this->merge($data);
  }

  private function initializeFields() {
    $this->fields = [];

    $class = new ReflectionClass($this);

    do {
      if ($class->hasConstant('FIELDS')) {
        $const = $class->getReflectionConstant('FIELDS');
        $decClass = $const->getDeclaringClass();

        if ($decClass->getName() === $class->getName()) {
          $this->fields = array_merge($const->getValue(), $this->fields);
        }
      }

      $class = $class->getParentClass();
    } while ($class !== false);
  }

  public function clear() {
    $this->data = [];
  }

  /**
   * Merge data
   *
   * @param object|array $data
   * @return void
   */
  public function merge($data) {
    if (is_object($data)) $data = (array)$data;
    if (!is_array($data)) return;

    foreach ($data as $name => $value) {
      $this->__set($name, $value);
    }
  }

  public function __isset($name) {
    return array_key_exists($name, $this->data);
  }

  public function __unset($name) {
    if ($this->__isset($name)) {
      unset($this->data[$name]);
    }
  }

  public function __get($name) {
    if ($this->__isset($name)) {
      return $this->data[$name];
    }

    return NULL;
  }

  public function __set($name, $value) {
    if (!array_key_exists($name, $this->fields)) return;

    settype($value, $this->fields[$name]);
    $this->data[$name] = $value;
  }

}
