<?php

class ShoppingCart implements ArrayAccess, Iterator {
  private $items = [];
  private $position = 0;

  public function __construct() {
    $this->position = 0;
  }

  // ArrayAccess implementation
  public function offsetSet($offset, $value): void {
    if (is_null($offset)) {
      $this->items[] = $value;
    } else {
      $this->items[$offset] = $value;
    }
  }

  public function offsetExists($offset): bool {
    return isset($this->items[$offset]);
  }

  public function offsetUnset($offset): void {
    unset($this->items[$offset]);
  }

  public function &offsetGet($offset): mixed {
    return $this->items[$offset];
  }

  // Method to increment the count of an item
  public function increment($itemName, $amount = 1): void {
    if (!isset($this->items[$itemName])) {
      $this->items[$itemName] = 0;
    }
    $this->items[$itemName] += $amount;
  }

  // Iterator implementation
  public function rewind(): void {
    $this->position = 0;
  }

  public function current(): int { 
    $keys = array_keys($this->items);
    return $this->items[$keys[$this->position]] ?? 0; 
  }

  public function key(): string {
    $keys = array_keys($this->items);
    return $keys[$this->position] ?? '';
  }

  public function next(): void {
    ++$this->position;
  }

  public function valid(): bool {
    $keys = array_keys($this->items);
    return isset($keys[$this->position]);
  }
}

// Test code (Do not modify)
if (!isset($ignoreTest)) {
  $cart = new ShoppingCart();
  print("Content:\n");
  $cart['apple'] = 3;
  ++$cart['apple'];
  foreach($cart as $name => $count) {
    print("  $name : $count\n");
  }
}
?>