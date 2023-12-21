<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Form Submission</title>
</head>
<body>
<?php
$name = '';
$age = '';
$luck = '';
$intelligence = '';
$invalid = false;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        header('Location: ' . $_SERVER['PHP_SELF'] . '?message=success');
        exit();
    } else {
        // Invalid submission
        $invalid = true;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $age = isset($_GET['age']) ? $_GET['age'] : '';
    $luck = isset($_GET['luck']) ? $_GET['luck'] : '';
    $intelligence = isset($_GET['intelligence']) ? $_GET['intelligence'] : '';
    $invalid = isset($_GET['invalid']) ? true : false;
    $message = isset($_GET['message']) ? $_GET['message'] : '';
}
?>

<?php if ($message === 'success' && $age < 6) : ?>
    <p>Submission successful! You are so cute <?php echo htmlspecialchars($name); ?></p>
<?php elseif ($message === 'success' && $age < 18) : ?>
    <p>Submission successful! Hello youngone.</p>
<?php else : ?>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
        Age: <input type="number" name="age" value="<?php echo htmlspecialchars($age); ?>"><br>
        Luck: <input type="number" name="luck" value="<?php echo htmlspecialchars($luck); ?>"><br>
        Intelligence: <input type="number" name="intelligence" value="<?php echo htmlspecialchars($intelligence); ?>"><br>
        <input type="submit" value="Submit">
    </form>

    <?php if ($invalid) : ?>
        <p>Invalid submission. Please make sure all fields are filled correctly.</p>
    <?php endif; ?>

    <?php if ($age > 100) : ?>
        <p>You are too old to be here.</p>
    <?php endif; ?>

    <?php if ($luck > 100) : ?>
        <p>Rather go win a lottery.</p>
    <?php endif; ?>
<?php endif; ?>
</body>
</html>