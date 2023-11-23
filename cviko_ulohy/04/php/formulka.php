<?php

function generateForm($definition) {
    $formHtml = '<!doctype html><meta charset=utf-8><title>Form</title>';
    $formHtml .= '<form action="' . htmlspecialchars($definition["action"]) . '" method="GET">';

    foreach ($definition["fields"] as $field) {
        if (isset($field["label"])) {
            $formHtml .= '<label>' . htmlspecialchars($field["label"]) . ' ';
        }

        $formHtml .= '<input type="' . htmlspecialchars($field["type"]) . '" name="' . htmlspecialchars($field["name"]) . '"';

        if (isset($field["required"]) && $field["required"]) {
            $formHtml .= ' required';
        }
        
        if (isset($field["min"]) && $field["name"] === "age") {
            $formHtml .= ' min="' . intval($field["min"]) . '"';
        }
        
        if (isset($field["max"]) && $field["name"] === "age") {
            $formHtml .= ' max="' . intval($field["max"]) . '"';
        }
        
        if (isset($field["value"])) {
            $formHtml .= ' value="' . htmlspecialchars($field["value"]) . '"';
        }

        $formHtml .= '>';

        if (isset($field["label"])) {
            $formHtml .= ' </label>';
        }
    }

    $formHtml .= '<input type="submit" value="Submit">';
    $formHtml .= '</form>';
    return $formHtml;
}

$jsonContent = file_get_contents('https://webik.ms.mff.cuni.cz/nswi142/practicals/documents/04-form.json');
$definition = json_decode($jsonContent, true);


echo generateForm($definition);

?>