<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit</title>
</head>
<body>
    <?php
    $name = filter_input(INPUT_GET, 'name');
    $age = filter_input(INPUT_GET, 'age', FILTER_VALIDATE_INT);
    $luck = filter_input(INPUT_GET, 'luck', FILTER_VALIDATE_INT);
    $intelligence = filter_input(INPUT_GET, 'intelligence', FILTER_VALIDATE_INT);

    if ($name !== false && $age !== false && $luck !== false && $intelligence !== false && 
        0 <= $age && $age <= 100 && 0 <= $luck && $luck <= 100 && 0 <= $intelligence && $intelligence <= 100) {
        if ($age <= 6) {
            echo "You are so cute " . htmlspecialchars($name) . ".";
        } elseif ($age <= 18) {
            echo "Hello young one.";
        } else {
            echo "Greetings, " . htmlspecialchars($name) . ".";
        }
    } else {
        header('Location: ./form.php?' . http_build_query(['name' => $name, 'age' => $age, 'luck' => $luck, 'intelligence' => $intelligence, 'invalid' => 'submit']));
        exit();
    }
    ?>
</body>
</html>