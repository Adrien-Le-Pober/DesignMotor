<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorService
{
    private static ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        self::$validator = $validator;
    }

    public static function validate($entity): bool
    {
        $errors = self::$validator->validate($entity);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getMessage();
            }
            return false;
        }
        return true;
    }
}