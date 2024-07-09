<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'P';
	case COMPLETED = 'C';
	case FAILED = 'F';
}