<?php

$errorsList = [];
$correctParamList = [];
$notReqParamList = ["deliveryBoy", "unboxDay", "fromTime", "toTime"];
$reqParamList = ["firstName", "lastName", "email"];

function timeToMinutes($value)
{
    list($hours, $minutes) = explode(':', $value);
    return ($hours * 60) + $minutes;
}

function timeDiff($value): bool
{
    global $correctParamList;
    global $errorsList;
    $lastElement = end($correctParamList);
    if (!in_array("fromTime", $errorsList)) {
        if ($value >= $lastElement) {
            return true;
        } else {
            return false;
        }
    }
    return true;
}
function addToCorrect($value)
{
    global $correctParamList;
    $correctParamList[] = $value;
}

function addToErrors($value)
{
    global $errorsList;
    $errorsList[] = $value;
}

require_once(__DIR__ . '/recodex_lib.php');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    include 'form_template.html';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($reqParamList as $param) {
        if (!empty($_POST[$param])) {
            $value = $_POST[$param];
        } else {
            addToErrors($param);
            continue;
        }
        if ($param === "firstName" || $param === "lastName") {
            strlen($value) <= 100 ? addToCorrect($value) : addToErrors($param);
        } elseif ($param === "email") {
            if (strlen($value) <=200 && filter_var($value, FILTER_VALIDATE_EMAIL)) {
                addToCorrect($value);
            } else {
                addToErrors($param);
            }
        }
    }

    foreach($notReqParamList as $param) {
        if (isset($_POST[$param])) {
            $value = $_POST[$param];
        } else {
            addToErrors($param);
            continue;
        }
        if (!empty($value)) {
            if ($param === "deliveryBoy") {
                $validDeliveryBoys = ["jesus", "santa", "moroz", "hogfather", "czpost", "fedex"];
                in_array($value, $validDeliveryBoys) ? addToCorrect($value) : addToErrors($param);
            } elseif ($param === "unboxDay") {
                if (is_numeric($value) && ($value == 24 || $value == 25)) {
                    addToCorrect($value);
                } else {
                    addToErrors($param);
                }
            } elseif ($param === "fromTime") {
                if (strlen($value) <= 5 && preg_match("/^[0-9]{1,2}:[0-9]{2}$/", $value)) {
                    $value = timeToMinutes($value);
                    addToCorrect($value);
                } else {
                    addToErrors("fromTime");
                }
            } elseif ($param === "toTime") {
                if (strlen($value) <= 5 && preg_match("/^[0-9]{1,2}:[0-9]{2}$/", $value)) {
                    $value = timeToMinutes($value);
                    if (timeDiff($value)) {
                        addToCorrect($value);
                    } else {
                        addToErrors("fromTime");
                        addToErrors("toTime");
                    }
                } else {
                    addToErrors("toTime");
                }
            }
        } else {
            addToCorrect(null);
        }
    }

    if (empty($_POST["gifts"])) {
        $giftCustom = $_POST["giftCustom"];
    } else {
        $gift = $_POST["gifts"];
        $validGifts = array("socks", "points", "jarnik", "cash", "teddy");
        
        if (in_array("other", $gift)) {
            if (!empty($_POST["giftCustom"])) {
                addToCorrect($gift);
                
                if (strlen($_POST["giftCustom"]) <= 100) {
                    addToCorrect($_POST["giftCustom"]);
                } else {
                    addToErrors("giftCustom");
                }
            } else {
                addToErrors("giftCustom");
            }
        } else {
            if (count(array_diff($gift, $validGifts)) === 0) {
                addToCorrect($gift);
                addToCorrect(null);
            } else {
                addToErrors("gifts");
            }
        }
    }
    

    if (sizeof($errorsList) != 0) {
        recodex_survey_error("Not correct values", $errorsList);
        header('Location: index.php', true, 303);
        exit;
    } 
    else {
        recodex_save_survey(...$correctParamList);
        header('Location: index.php', true, 303);
        exit;
    }

}
