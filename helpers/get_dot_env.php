<?php

function getDotEnv($key = null) {
    foreach (file(dirname(__FILE__) . '/../.env') as $row) {
        $destructed = explode('=', $row);
        if ($key === $destructed[0])
            return trim($destructed[1]);
    }
    return null;
}