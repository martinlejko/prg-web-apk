<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Counter Demo</title>
</head>
<body>

<?php
$counter = filter_input(INPUT_GET, 'counter', FILTER_VALIDATE_INT);
if ($counter === false || $counter === null) {
    $counter = 0;
}

echo '<p>Counter: ' . htmlspecialchars($counter) . '</p>';

$prevLink = 'counter.php?' . http_build_query(['counter' => $counter - 1]);
$nextLink = 'counter.php?' . http_build_query(['counter' => $counter + 1]);

echo '<p><a href="' . htmlspecialchars($prevLink) . '">Previous</a> | <a href="' . htmlspecialchars($nextLink) . '">Next</a></p>';
?>

</body>
</html>