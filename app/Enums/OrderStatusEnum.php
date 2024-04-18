<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum OrderStatusEnum: string
{
    case COMPLETED = 'completed';
    case IN_PROGRESS = 'in_progress';
    case WAITING_PAYMENT = 'waiting_payment';

    public function getTotalSalesType(): AnalyticTypeEnum
    {
        return $this->filterAnalyticType('total_sales');
    }

    public function getTotalAmountType(): AnalyticTypeEnum
    {
        return $this->filterAnalyticType('total_amount');
    }

    protected function filterAnalyticType(string $type): AnalyticTypeEnum
    {
        $matchedEnum = array_filter(AnalyticTypeEnum::cases(), function ($analyticType) use ($type) {
            return Str::contains($analyticType->value, "{$type}_{$this->value}");
        });

        return reset($matchedEnum);
    }
}
