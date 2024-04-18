<?php

namespace App\Enums;

enum AnalyticTypeEnum: string
{
    case TOTAL_AMOUNT_COMPLETED = 'total_amount_completed';
    case TOTAL_AMOUNT_IN_PROGRESS = 'total_amount_in_progress';
    case TOTAL_AMOUNT_WAITING_PAYMENT = 'total_amount_waiting_payment';
    case TOTAL_SALES_COMPLETED = 'total_sales_completed';
    case TOTAL_SALES_IN_PROGRESS = 'total_sales_in_progress';
    case TOTAL_SALES_WAITING_PAYMENT = 'total_sales_waiting_payment';
}
