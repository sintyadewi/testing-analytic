<?php

namespace App\Console\Commands;

use App\Enums\AnalyticTypeEnum;
use App\Enums\OrderStatusEnum;
use App\Models\Analytic;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class CreateDailyAnalytic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createDailyAnalytic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->insert(OrderStatusEnum::COMPLETED);
        $this->insert(OrderStatusEnum::IN_PROGRESS);
        $this->insert(OrderStatusEnum::WAITING_PAYMENT);
    }

    protected function insert(OrderStatusEnum $status): void
    {
        $totalAmount = $this->orderQuery($status)->count();
        $totalSales  = $this->orderQuery($status)->sum('total');

        $this->upsertAnalytic(AnalyticTypeEnum::getTotalAmountType($status), $totalAmount);
        $this->upsertAnalytic(AnalyticTypeEnum::getTotalSalesType($status), $totalSales);
    }

    protected function orderQuery(OrderStatusEnum $status): Builder
    {
        return Order::query()
            ->whereStatus($status)
            ->whereDate('updated_at', now());
    }

    protected function upsertAnalytic($type, $total): void
    {
        $currentDate = now();
        $dailyDate = $currentDate->toDateString();
        $weeklyDate = $currentDate->endOfWeek()->toDateString();
        $monthlyDate = $currentDate->endOfMonth()->toDateString();

        Analytic::updateOrCreate(
            [
                'type' => $type,
                'daily_date' => $dailyDate,
                'weekly_date' => $weeklyDate,
                'monthly_date' => $monthlyDate,
            ],
            ['total' => $total]
        );
    }
}
