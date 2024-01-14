<?php
include 'db_controller.php';

if (isset($_GET['name'])) {
    $articleName = filter_var($_GET['name'], FILTER_SANITIZE_STRING);

    $DBController = new DBController();
    $articleId = $DBController->createArticle($articleName);
    echo json_encode(['id' => $articleId]);
} else {
    http_response_code(400);
    echo 'Bad Request: The "name" parameter is required.';
    exit;
}
