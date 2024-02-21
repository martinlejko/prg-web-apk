<?php
require_once "Model/DatabaseService.php";
require_once "Model/Connection.php";

function deleteArticle($articleId) {
    $connector = new DatabaseConnection();
    $service = new DatabaseService($connector);

    return $service->deleteArticle($articleId);
}

function handleRequest() {
    if (!isset($_GET["id"])) {
        throwError(404, "Article ID not provided.");
    }

    $articleId = filter_var($_GET["id"], FILTER_VALIDATE_INT);

    if ($articleId === false || $articleId < 0) {
        throwError(400, "Invalid ID.");
    }

    $result = deleteArticle($articleId);

    if ($result) {
        echo json_encode(["success" => "Article deleted successfully."]);
    } else {
        throwError(500, "Failed to delete article.");
    }
}

function throwError($statusCode, $message = "") {
    header("HTTP/1.1 $statusCode");
    echo json_encode(["error" => $message]);
    exit;
}

handleRequest();
?>
