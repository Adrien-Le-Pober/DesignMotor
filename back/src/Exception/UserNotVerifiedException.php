<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UserNotVerifiedException extends HttpException
{
    public function __construct()
    {
        parent::__construct(403, "Votre adresse email n'a pas été confirmée, consultez l'email de confirmation que nous vous avons envoyé.");
    }
}