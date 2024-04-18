<?php

namespace App\Console\Commands;

use App\Enums\OrderStatusEnum;
use App\Models\Analytic;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Contracts\Database\Query\Builder;

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
        $totalAmount = $this->getTotalAmount($status);
        $totalSales  = $this->getTotalSales($status);

        $this->upsertAnalytic($status->getTotalAmountType(), $totalAmount);
        $this->upsertAnalytic($status->getTotalSalesType(), $totalSales);
    }

    protected function getTotalAmount(OrderStatusEnum $status): int
    {
        return Order::query()->whereStatus($status)->count();
    }

    protected function getTotalSales(OrderStatusEnum $status): float
    {
        return Order::query()->whereStatus($status)->sum('total');
    }

    protected function upsertAnalytic($type, $total): void
    {
        $currentDate = Carbon::now();
        $dailyDate = $currentDate->toDateString();
        $weeklyDate = $currentDate->endOfWeek()->toDateString();
        $monthlyDate = $currentDate->endOfMonth()->toDateString();

        Analytic::upsert(
            [
                'type'  => $type,
                'total' => $total,
                'daily_date' => $dailyDate,
                'weekly_date' => $weeklyDate,
                'monthly_date' => $monthlyDate,
            ],
            uniqueBy: [
                'type',
                'daily_date',
                'weekly_date',
                'monthly_date',
            ],
            update: ['total']
        );
    }
}
