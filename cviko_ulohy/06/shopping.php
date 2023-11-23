<?php

class ShoppingCart {
  private $items;

  public function __construct() {
    $this->items = [];
  }

  public function addItem($itemName, $quantity = 1) {
    if (!array_key_exists($itemName, $this->items)) {
      $this->items[$itemName] = 0;
    }
    $this->items[$itemName] += $quantity;
  }

  public function removeItem($itemName) {
    if (array_key_exists($itemName, $this->items)) {
      unset($this->items[$itemName]);
    }
  }

  public function __toString() {
    $cartContents = "Content:\n";
    foreach ($this->items as $item => $quantity) {
      $cartContents .= "  $item : $quantity\n";
    }
    return $cartContents;
  }
}

// Test code (Do not modify)
if (!isset($ignoreTest)) {
  $cart = new ShoppingCart();
  $cart->addItem('milk', 1);
  $cart->addItem('bread', 1);
  $cart->addItem('basil', 1);
  $cart->addItem('milk');
  $cart->removeItem('basil');
  echo $cart;
}
?>