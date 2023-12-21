<?php

class Container {

  private $data = [];

  public function add(string $name, int $count) {
    if (isset($this->data[$name])) {
      $this->data[$name] += $count;
    } else {
      $this->data[$name] = $count;
    }
  }
  
  public function remove(string $name) {
    if (!isset($this->data[$name])) {
      return;
    }
    unset($this->data[$name]);
  }

}

class CollectStatistics {

  private $object;
  private static $statistics = [];

  public function __construct($object) {
    $this->object = $object;
  }

  public function __call($name, $arguments) {
    if (!isset(self::$statistics[$name])) {
      self::$statistics[$name] = 0;
    }
    self::$statistics[$name]++;

    return call_user_func_array([$this->object, $name], $arguments);
  }

  public static function wrap($object) {
    return new self($object);
  }

  public function printStatistics() {
    var_dump(self::$statistics);
  }

  // Intermediate version
  public static function wrapIntermediate($object) {
    $instance = new self($object);
    return ["value" => $instance, "statistics" => &self::$statistics];
  }

}

// Do not modify code bellow this line.

// Expected output, utilize var_dump:
// array(2) {
//  ["add"]=>
//  int(3)
//  ["remove"]=>
//  int(1)
// }

// Basic version.
if (!isset($ignoreTest)) {
  $instance = CollectStatistics::wrap(new Container());
  $instance->add('tomato', 1);
  $instance->add('tomato', 1);
  $instance->add('orange', 1);
  $instance->remove('bread');
  $instance->printStatistics();
}

// Intermediate version, enable by defining wrapIntermediate method.
if (!isset($ignoreTest) && method_exists("CollectStatistics", "wrapIntermediate")) {
  $wrap = CollectStatistics::wrapIntermediate(new Container());
  $instance = $wrap["value"];
  $instance->add('tomato', 1);
  $instance->add('tomato', 1);
  $instance->add('orange', 1);
  $instance->remove('bread');
  var_dump($wrap["statistics"]);
}