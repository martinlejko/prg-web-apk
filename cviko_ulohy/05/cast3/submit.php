<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submission Result</title>
</head>
<body>
    <?php
    $name = filter_input(INPUT_GET, 'name',);
    $age = filter_input(INPUT_GET, 'age', FILTER_VALIDATE_INT);
    $luck = filter_input(INPUT_GET, 'luck', FILTER_VALIDATE_INT);
    $intelligence = filter_input(INPUT_GET, 'intelligence', FILTER_VALIDATE_INT);

    if ($name && $age !== false && $luck !== false && $intelligence !== false && $age <= 100 && $luck <= 100 && $intelligence <= 100) {
        if ($age <= 6) {
            echo "You are so cute " . htmlspecialchars($name) . ".";
        } elseif ($age <= 18) {
            echo "Hello young one.";
        } else {
            echo "Greetings, " . htmlspecialchars($name) . ".";
        }
    } else {
        echo "Invalid data provided. Please make sure all fields are filled correctly.";
        echo "<br><a href='form.php'>Back to form</a>";
    }
    ?>
</body>
</html>