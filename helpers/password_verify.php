<?php

function passwordVerify ($password, $hash) {
    return sha1($password) === $hash;
}