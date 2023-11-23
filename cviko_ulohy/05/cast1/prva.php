<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simple Web Page</title>
</head>
<body>
    <?php
    // Property 1: $name
    $name = 'Ailish';
    echo '<p>Hello, ' . htmlspecialchars($name) . '!</p>';
    ?>

    <?php
    // Property 2: $age
    $age = 32;
    echo '<p>Your age is ' . htmlspecialchars($age) . ' years old.</p>';
    ?>

    <?php
    // Property 3: $title
    $title = '<AI>';
    echo '<p>Title: ' . htmlspecialchars($title) . '</p>';
    ?>
</body>
</html>
