<?php

namespace App\Enums;

enum OrderStatusEnum: string
{
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case WAITING_PAYMENT = 'waiting_payment';
}
