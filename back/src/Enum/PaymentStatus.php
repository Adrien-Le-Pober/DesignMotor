<?php

namespace App\Enum;

enum PaymentStatus: string
{
    case PENDING = 'PE';
	case PAID = 'P';
	case VOID = 'V';
	case DECLINED = 'D';
}