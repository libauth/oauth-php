<?php

namespace LibAuth\Tokens;

class Token {

  const FIELDS = [];

  private $data;

  final function __construct($data = NULL) {
    $this->clear();
    $this->merge($data);
  }

  public function clear() {
    $this->data = [];
  }

  /**
   * Merge Token data
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
    if (!array_key_exists($name, static::FIELDS)) return;
    settype($value, static::FIELDS[$name]);
    $this->data[$name] = $value;
  }

}
