<?php
require_once "Model/DatabaseService.php";
require_once "Model/Connection.php";

error_reporting(E_ALL);
ini_set('display_errors', 1);

function createTag($tagName) {
    $connector = new DatabaseConnection();
    $service = new DatabaseService($connector);
    return $service->createTag($tagName);
}

function handleRequest() {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["tagName"]) || strlen($data["tagName"]) === 0 || !(strlen($data["tagName"]) < 32)) {
        throwError(400, "Invalid or missing tag name.");
    }

    $result = createTag($data["tagName"]);

    if ($result === false) {
        throwError(500, "Creation failed.");
    }

    echo json_encode(["id" => $result]);
}

function throwError($statusCode, $message = "") {
    header("HTTP/1.1 $statusCode");
    echo json_encode(["error" => $message]);
    exit;
}

handleRequest();
?>
