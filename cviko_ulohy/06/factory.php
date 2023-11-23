<?php

class User {
  
  public string $name;
  public int $age;

  public function __construct(string $name, int $age) {
    $this->name = $name;
    $this->age = $age;
  }

}

interface Writer {
  public function asString(User $user): string;
}

class JsonWriter implements Writer {
  public function asString(User $user): string {
    return json_encode(['name' => $user->name, 'age' => $user->age]);
  }
}

class StringWriter implements Writer {
  public function asString(User $user): string {
    return "{$user->name}:{$user->age}";
  }
}

function createWriter($name): Writer {
  $className = $name . 'Writer';
  return new $className();
}

// Do not modify code bellow this line.

// Expected output:
// JSON:
// {"name":"Ailish","age":22}
// STRING
// Ailish:22

if (!isset($ignoreTest)) {
  print("JSON\n");
  $student = new User("Ailish", "22");
  print(createWriter("json")->asString($student));
  print("\nSTRING\n");
  print(createWriter("string")->asString($student));
  print("\n");
}