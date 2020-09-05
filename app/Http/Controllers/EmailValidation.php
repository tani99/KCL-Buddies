<?php

namespace App\Http\Controllers;

trait EmailValidation
{
    /**
     * @param string|null $emailHost The email host address
     * @return bool True if the server is able to send out emails.
     */
    protected final function canEmail(?string $emailHost): bool
    {
        return isset($emailHost) && strlen($emailHost) > 0;
    }
}