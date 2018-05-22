<?php
/**
 * @param int $code
 * @param $payload array|string|null
 *
 * @return bool
 */
function sendResponse($code = 200, $payload = null)
{
    ob_start();

    if (is_array($payload))
        echo json_encode($payload);
    elseif (isset($payload)) echo $payload;

    // set headers
    header("Content-Encoding: none");
    header("Content-Length: " . ob_get_length());
    header("Connection: close");
    if (is_array($payload))
        header("Content-Type: application/json");
    header("X-PHP-Response-Code: $code", true, $code);

    // Flush all output.
    ob_end_flush();
    ob_flush();
    flush();

    return true;
}