<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit</title>
</head>
<body>
<?php
$name = filter_input(INPUT_POST, 'name');
$age = filter_input(INPUT_POST, 'age', FILTER_VALIDATE_INT);
$luck = filter_input(INPUT_POST, 'luck', FILTER_VALIDATE_INT);
$intelligence = filter_input(INPUT_POST, 'intelligence', FILTER_VALIDATE_INT);

if (
    $name !== false &&
    $age !== false && $age >= 0 && $age <= 100 &&
    $luck !== false && $luck >= 0 && $luck <= 100 &&
    $intelligence !== false && $intelligence >= 0 && $intelligence <= 100
) {
    // Successful submission
    header('Location: success.php?message=success');
    exit();
} else {
    // Invalid submission
    header('Location: form.php?' . http_build_query(['name' => $name, 'age' => $age, 'luck' => $luck, 'intelligence' => $intelligence, 'invalid' => 'submit']));
    exit();
}
?>
</body>
</html>