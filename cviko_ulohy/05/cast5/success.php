<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Success</title>
</head>
<body>
    <?php
    $message = isset($_GET['message']) ? $_GET['message'] : '';

    if ($message === 'success') {
        echo "Submission successful!";
    } else {
        echo "No success message found.";
    }
    ?>
</body>
</html>
