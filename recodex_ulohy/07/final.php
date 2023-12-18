<?php

function sendError($errorMess, $errorCode){
    http_response_code($errorCode);
    echo $errorMess;
    exit(0);
}

//getting the JSON from the website
$urlStory = "https://webik.ms.mff.cuni.cz/nswi142/php-assignment/story.json";
$local = "story.json";

//parsing  the JSON and checks
$storyGet = file_get_contents($urlStory);
if ($story === false){
    sendError("Error while getting the JSON from the website", 500);
}

$storyArray = json_decode($storyGet, true);
if ($storyArray === null || !isset($storyArray["sites"]) || !isset($storyArray["starting-site"]) || !isset($storyArray["statistics"])){
    sendError("Invalid definition", 500);
}

