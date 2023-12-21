<?php

require_once(__DIR__ . '/recodex_lib.php');

function convertTimeToMinutes($timeStr) {
    if (empty($timeStr) || strlen($timeStr) > 5) return null;
    if (!preg_match('/^\d{1,2}:\d{2}$/', $timeStr)) return false;
    list($hours, $minutes) = explode(':', $timeStr);
    return (int)$hours * 60 + (int)$minutes;
}

function validateFieldLength($field, $length) {
    return isset($_POST[$field]) && mb_strlen($_POST[$field]) <= $length;
}

function redirectWithError($message, $invalidFields) {
    recodex_survey_error($message, $invalidFields);
    header('Location: index.php', true, 303);
    exit;
}

function addInvalidField(&$invalidFields, $field) {
    if (!in_array($field, $invalidFields)) {
        $invalidFields[] = $field;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    require __DIR__ . '/form_template.html';
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $invalidFields = [];
    $mandatoryFields = ['firstName', 'lastName', 'email', 'unboxDay', 'deliveryBoy'];
    $validDeliveryBoys = ['jesus', 'santa', 'moroz', 'hogfather', 'czpost', 'fedex'];
    $validGifts = ['socks', 'points', 'jarnik', 'cash', 'teddy', 'other'];

    foreach ($mandatoryFields as $field) {
        if (empty($_POST[$field])) {
            addInvalidField($invalidFields,$field);
        }
    }

    // Email and length validation
    if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) || !validateFieldLength('email', 200)) {
        addInvalidField($invalidFields,'email');
    }

    // Validate first and last names for length
    if (!validateFieldLength('firstName', 100)) {
        addInvalidField($invalidFields,'firstName');
    }
    if (!validateFieldLength('lastName', 100)) {
        addInvalidField($invalidFields,'lastName');
    }

    // Validate deliveryBoy value
    if (!in_array($_POST['deliveryBoy'], $validDeliveryBoys)) {
        addInvalidField($invalidFields,'deliveryBoy');
    }

    // Validate unboxDay value
    if (!in_array($_POST['unboxDay'], ['24', '25'])) {
        addInvalidField($invalidFields,'unboxDay');
    }

    // Validate time fields and convert to minutes
    $fromTimeProvided = isset($_POST['fromTime']);
    $toTimeProvided = isset($_POST['toTime']);  

    $fromTime = $fromTimeProvided ? convertTimeToMinutes($_POST['fromTime']) : null;
    $toTime = $toTimeProvided ? convertTimeToMinutes($_POST['toTime']) : null;

    $fromTimeInvalid = !$fromTimeProvided || $fromTime === false;
    $toTimeInvalid = !$toTimeProvided || $toTime === false;
    $timeOrderInvalid = $fromTime !== null && $toTime !== null && $toTime < $fromTime;

    if ($fromTimeInvalid || $timeOrderInvalid) {
        addInvalidField($invalidFields, 'fromTime');
    }
    if ($toTimeInvalid || $timeOrderInvalid) {
        addInvalidField($invalidFields, 'toTime');
    }
    

    


    // Validate gifts and giftCustom
    if (!isset($_POST['gifts'])) {
        $_POST['gifts'] = []; // Default to an empty array if not set
    }
    if (empty($_POST['gifts'])) {
        $giftCustom = null; // Set giftCustom to null if no gifts
    } elseif (is_array($_POST['gifts'])) {
        foreach ($_POST['gifts'] as $gift) {
            if (!in_array($gift, $validGifts)) {
                addInvalidField($invalidFields, 'gifts');
                break;
            }
        }
        if (in_array('other', $_POST['gifts']) && (empty($_POST['giftCustom']) || !validateFieldLength('giftCustom', 100))) {
            addInvalidField($invalidFields, 'giftCustom');
        }
    } else {
        addInvalidField($invalidFields, 'gifts');
    }



    // Check for errors and redirect if necessary
    if (!empty($invalidFields)) {
        redirectWithError('Validation errors occurred', $invalidFields);
    } else {
        // No validation errors
        // Convert unboxDay to integer
        $unboxDay = (int)$_POST['unboxDay'];

        // Initialize $giftCustom based on the presence of 'other' in the gifts array
        $giftCustom = in_array('other', $_POST['gifts']) ? $_POST['giftCustom'] : null;

        // Save the data
        recodex_save_survey(
            $_POST['firstName'],
            $_POST['lastName'],
            $_POST['email'],
            $_POST['deliveryBoy'],
            $unboxDay,
            $fromTime,  
            $toTime,    
            $_POST['gifts'],  // already validated to be an array of valid gifts
            $giftCustom
        );

        // After saving, redirect to avoid re-submission of the form
        header('Location: index.php', true, 303);
        exit;
    }
}