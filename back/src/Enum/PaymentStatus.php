<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'PE';
	case PAID = 'P';
	case DECLINED = 'D';
}