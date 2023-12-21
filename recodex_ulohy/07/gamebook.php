<?php

function sendError($errorMess, $errorCode){
    http_response_code($errorCode);
    echo $errorMess;
    exit(0);
}

function changeSite($url, $site) {
    $url_parts = parse_url($url);

    $query_params = [];
    if (isset($url_parts['query'])) {
        parse_str($url_parts['query'], $query_params);
    }
    $query_params['site'] = $site;
    $new_query = http_build_query($query_params);

    $new_url = $url_parts['scheme'] . '://' . $url_parts['host'];

    if (isset($url_parts['port'])) {
        $new_url .= ':' . $url_parts['port'];
    }

    $new_url .= $url_parts['path'] . '?' . $new_query;

    return $new_url;
}

function getUrl(){
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $uri = $_SERVER['REQUEST_URI'];

    return $protocol . '://' . $host . $uri;
}

function statsGetter($stats){
    foreach($_GET as $key => $value){
        if(substr($key, 0, 11) !== "statistics_"){
            continue;
        }
        $variable = substr($key, 11);
        $stats = str_replace("{" . $variable . "}", $value, $stats);
    }
    return $stats;
}

function processStringSite($site, $url){
    $content = "<form class=\"form\" action=\"$url\" method=\"get\">
    <input type=\"hidden\" name=\"site\" value=\"" . urlencode($site['site']) . "\">
    <label>{$site['label']}
        <input type=\"text\" name=\"statistics_{$site['target']}\">
        <input type=\"submit\" value=\"Submit\">
    </label>
    </form>";
    return $content;
}

function checker($condition, $var) {
    $symbol = substr($condition, 0, 1);
    $number = substr($condition, 1);
    if (!is_numeric($number)){
        sendError("Invalid variable value.", 500);
    }
    switch($symbol){
        case "<":
            return $var < $number;
        case ">":
            return $var > $number;
    }
}

function actionProcessor($action){
    if(isset($action["visibility"])){
        foreach($action["visibility"] as $variable_name => $condition){
            if(!isset($_GET["statistics_" . $variable_name])){
                sendError("Missing variable.", 500);
            }
            if(!checker($condition, $_GET["statistics_" . $variable_name])){
                return "";
            }
        }
    }

    if(isset($action["effect"])){
        foreach($action["effect"] as $variable_name => $effect){
            $var = substr($effect, 0, 1);
            $number = substr($effect, 1);
            if (!is_numeric($number)){
                sendError("Invalid variable value.", 500);
            }
            switch($var){
                case "=":
                    $_GET["statistics_" . $variable_name] = $number;
                    break;
                case "+":
                    if(!isset($_GET["statistics_" . $variable_name])){
                        sendError("Missing variable.", 500);
                    }
                    $_GET["statistics_" . $variable_name] += $number;
                    break;
                case "-":
                    if(!isset($_GET["statistics_" . $variable_name])){
                        sendError("Missing variable.", 500);
                    }
                    $_GET["statistics_" . $variable_name] -= $number;
                    break;
            }
        }
    }


    $_GET["site"] = $action["site"];
    return "<li>
    <a href=\"?" . http_build_query($_GET) . "\">" . $action["text"] . "</a>
    </li>";
}

function processSite($site){
    if(!isset($site["actions"])){
        return "";
    }

    $content = "<ul class=\"actions\">";
    foreach($site["actions"] as $action){
        $content .= actionProcessor($action);
    }
    $content .= "</ul>";
    return $content;
}

function siteWirter($storyGet){
    $name = $_GET["site"];

    if(!isset($storyGet["sites"][$name])){
        sendError("", 404);
    }

    //get url
    $url = getUrl();

    //getting the stats
    $stats = statsGetter($storyGet["sites"][$name]['text']);

    //getting the content
    $content = "<div class=\"content\">" . $stats . "</div>";
    if ($storyGet["sites"][$name]["type"] === "basic") {
        $content .= processSite($storyGet["sites"][$name]);
    } else {
        $content .= processStringSite($storyGet["sites"][$name], $url);
    }

    //html body
    $htmlBody = "
    <body>
    <div class=\"$name\">
    " . $content . "
    </div>
    </body>";

    //full html
    $htmlContent = "
    <!DOCTYPE html>
    <html lang=\"en\">
    <head>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background-color: #15202b;
            color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .title {
            text-align: center;
            font-size: 3em; /* Increased font size */
            margin-bottom: 30px; /* Adjusted margin */
            font-family: 'Times New Roman', Times, serif; /* Changed font family */
            color: #f0c040; /* Different title color */
        }
        .content {
            line-height: 1.6;
            font-size: 1.2em; /* Increased content font size */
            font-family: 'Georgia', serif; /* Changed font family */
        }
    </style>
    <title>" . $storyGet["title"] . "</title>
    </head>" . $htmlBody;
    return $htmlContent;
}

//getting the JSON from the website
$urlStory = "https://webik.ms.mff.cuni.cz/nswi142/php-assignment/story.json";
$local = "story.json";

//parsing  the JSON and checks
$storyGet = file_get_contents($urlStory);

if ($storyGet === false){
    sendError("Error while getting the JSON from the website", 500);
}

$storyArray = json_decode($storyGet, true);

if ($storyArray === null || !isset($storyArray["sites"]) || !isset($storyArray["starting-site"]) || !isset($storyArray["title"])){
    sendError("Invalid definition", 500);
}

//getting the site
$site = $_GET['site'];
if (!empty($site)){
   echo siteWirter($storyArray); 
} else {
    header("Location: " . changeSite(getUrl(), $storyArray["starting-site"]));
    exit();
}
