<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Form</title>
</head>

<body>
    <?php
    $name = isset($_GET['name']) ? $_GET['name'] : '';
    $age = isset($_GET['age']) ? $_GET['age'] : '';
    $luck = isset($_GET['luck']) ? $_GET['luck'] : '';
    $intelligence = isset($_GET['intelligence']) ? $_GET['intelligence'] : '';
    $invalid = isset($_GET['invalid']) ? true : false;
    ?>

    <form action="submit.php" method="get">
        Name: <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>"><br>
        Age: <input type="number" name="age" value="<?php echo htmlspecialchars($age); ?>"><br>
        Luck: <input type="number" name="luck" value="<?php echo htmlspecialchars($luck); ?>"><br>
        Intelligence: <input type="number" name="intelligence" value="<?php echo htmlspecialchars($intelligence); ?>"><br>
        <input type="submit" value="Submit">
    </form>

    <?php
    if ($invalid) {
        echo "Invalid submission. Please make sure all fields are filled correctly.";
    }
    if ($age > 100){
        echo "You are too old to be here.";
    }
    if ($luck > 100){
        echo "Rather go win a lottery.";
    }
    ?>
</body>
</html>
