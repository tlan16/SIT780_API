<?php

switch ($_SERVER['REQUEST_URI']) {
    case '/':
        json_response(['Hello', 'API']);
}