<?php
include 'db_controller.php';

if (!isset($_GET['id'])) {
    http_response_code(404);
    exit();
}

$articleId = $_GET['id'];
$DBController = new DBController();
$DBController->deleteArticle($articleId);
?>
