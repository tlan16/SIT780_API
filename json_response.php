<?php
if (!function_exists('json_response')) {
    function json_response($payload = [], $code = 200)
    {
        ob_start();
        echo json_encode($payload);

        // set headers
        header("Content-Encoding: none");
        header("Content-Length: " . ob_get_length());
        header("Connection: close");
        header("Content-Type: application/json");
        header("X-PHP-Response-Code: $code", true, $code);

        // Flush all output.
        ob_end_flush();
        ob_flush();
        flush();
    }
}