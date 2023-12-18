<?php

function process_input_string_site($site, $url) {

    $content = "<form class=\"form\" action=\"$url\" method=\"get\">
    <input type=\"hidden\" name=\"site\" value=\"" . urlencode($site['site']) . "\">
    <label>{$site['label']}
        <input type=\"text\" name=\"statistics_{$site['target']}\">
        <input type=\"submit\" value=\"Submit\">
    </label>
    </form>";
    return $content;
}

function convert_expression_number($expression) {
    $value = substr($expression, 1);
    if(!is_numeric($value)) {
        http_response_code(500);
        echo "Invalid variable value.";
        exit();
    }
    return $value;
}

function check_array_variable($array, $index) {
    if(!isset($array[$index])) {
        http_response_code(500);
        echo "Missing variable.";
        exit();
    }
}

function do_effect($effect, $variable_name, &$url_arguments) {
    $operation = substr($effect, 0, 1);
    $number = convert_expression_number($effect);

    switch($operation) {
        case '=':
            $url_arguments['statistics_' . $variable_name] = $number;
            break;
        case '+':
            check_array_variable($url_arguments, 'statistics_' . $variable_name);
            $url_arguments['statistics_' . $variable_name] += $number;
            break;
        case '-':
            check_array_variable($url_arguments, 'statistics_' . $variable_name);
            $url_arguments['statistics_' . $variable_name] -= $number;
            break;
    }
}

function check_condition($condition, $variable) {
    $symbol = substr($condition, 0, 1);
    $number = convert_expression_number($condition);

    switch($symbol) {
        case '<':
            return $variable < $number;
        case '>':
            return $variable > $number;
    }
}

function process_action($action, $url_arguments) {
    if(isset($action['visibility'])) {
        foreach($action['visibility'] as $variable_name => $condition) {
            check_array_variable($url_arguments, 'statistics_' . $variable_name);
            if(!check_condition($condition, $url_arguments['statistics_' . $variable_name])){
                return "";
            }
        }
    }

    if(isset($action['effect'])) {
        foreach($action['effect'] as $variable_name => $effect) {
            do_effect($effect, $variable_name, $url_arguments);
        }
    }

    $url_arguments['site'] = $action['site'];
    return "<li>
    <a href=\"?" . http_build_query($url_arguments) . "\">" . $action['text'] . "</a>
    </li>";
}

function process_basic_site($site, $url_arguments) {
    if(!isset($site['actions'])) {
        return "";
    }

    $content = "<ul class=\"actions\">";
    foreach ($site['actions'] as $action) {
        $content .= process_action($action, $url_arguments);
    }
    $content .= "</ul>";
    return $content;
}

function text_substitution($text, $substitutions) {
    foreach($substitutions as $statistic => $value) {
        if(substr($statistic, 0, 11) !== "statistics_") {
            continue;
        }
        $variable = substr($statistic, 11);
        $text = str_replace("{" . $variable . "}", $value, $text);
    }
    return $text;
}

function get_site_content($site, $url_arguments, $url) {
    $content = "<div class=\"content\">" . text_substitution($site['text'], $url_arguments) . "</div>";
    $content .= ($site['type'] === "basic") ? process_basic_site($site, $url_arguments) : process_input_string_site($site, $url);
    return $content;
}

function create_body($sites, $query, $url) {
    $site_name = $query['site'];
    if(!isset($sites[$site_name])) {
        http_response_code(404);
        exit();
    }
    $html_body = "
    <body>
    <div class=\"$site_name\">
    " . get_site_content($sites[$site_name], $query, $url) . "
    </div>
    </body>";

    return $html_body;
}

function get_url($server) {
    $protocol = isset($server['HTTPS']) && $server['HTTPS'] === 'on' ? 'https' : 'http';
    
    $host = $server['HTTP_HOST'];
    
    $uri = $server['REQUEST_URI'];
    
    return $protocol . '://' . $host . $uri;
}

function create_site($sites, $query, $url, $title) {
    $html_content = "
    <!DOCTYPE html>
    <html lang=\"en\">
    <head>
    <title>" . $title . "</title>
    </head>" . create_body($sites, $query, $url);

    return $html_content;
}

function get_adventure($url) {
    $adventure_source = file_get_contents($url);
    if($adventure_source === false) {
        http_response_code(500);
        echo "Failed to read definition";
        exit();
    }
    
    $adventure_definition = json_decode($adventure_source, true);
    if($adventure_definition === null || !isset($adventure_definition['title']) || !isset($adventure_definition['starting-site']) || !isset($adventure_definition['sites'])) {
        http_response_code(500);
        echo "Invalid definition";
        exit();
    }

    return $adventure_definition;
}

function change_site($url, $site) {
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

//-------------------------------------------------------------------------------------

$adventure_definition = get_adventure('https://webik.ms.mff.cuni.cz/nswi142/php-assignment/story.json');

if (!empty($_GET['site'])) {
    echo create_site($adventure_definition['sites'], $_GET, get_url($_SERVER), $adventure_definition['title']);
} else {
    header("Location: " . change_site(get_url($_SERVER), $adventure_definition['starting-site']));
    exit();
}