<?php

function passwordHash($password)
{
    return sha1($password);
}