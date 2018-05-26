<?php

if ($_SERVER['REQUEST_METHOD'] === "OPTIONS") {
    return sendResponse();
}

include_once dirname(__FILE__) . '/router.php';
