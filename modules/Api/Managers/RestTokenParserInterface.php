<?php

namespace App\Api\Managers;

use App\Api\Managers\Session;

interface RestTokenParserInterface
{
    /**
     * @param Session $session Session to generate token for
     *
     * @return string Generated token
     */
    public function getToken(Session $session, $expirationTime = NULL);

    /**
     * @param string $token Access token
     *
     * @return Session Session restored from token
     */
    public function getSession($token);
}
