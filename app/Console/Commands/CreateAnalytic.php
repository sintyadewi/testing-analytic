<?php

namespace App\Console\Commands;

use App\Models\Analytic;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class CreateAnalytic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createAnalytic';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to input order analytic';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // BELUM HANDLING UTC TIME
        // Saat menjalankan event cron job pada pada 19 April 2024 pukul 00:00 (asumsi cron job dijalankan setiap 10 menit)
        // Data yang diambil => 18 April 2024 pkl 23:50 - 23:59
        // Maka seharusnya analytic record yang di update adalah tanggal 18 April 2024

        // check the existing orders on the specific period of time
        $isOrderExists = $this->orderQuery()->exists();

        if ($isOrderExists) {
            $this->upsertAnalyticAction();
        }
    }

    protected function upsertAnalyticAction(): void
    {
        // get all the existing orders on the specific period of time
        $orders = $this->orderQuery()->get();

        // calculate each status into 2 categories => total sales & total amount
        $totalSales = $this->calculationQuery($orders);
        $totalAmount = $this->calculationQuery($orders, 'total_amount');

        // check the existing analytics record by current date
        $isAnalyticExist = $this->analyticQuery()->exists();

        // upsert record based on $isAnalyticExist value
        if ($isAnalyticExist) {
            // update
            $this->upsertAnalytics($totalAmount, 'total_amount', false);
            $this->upsertAnalytics($totalSales, 'total_sales', false);
        } else {
            // create
            $this->upsertAnalytics($totalAmount, 'total_amount');
            $this->upsertAnalytics($totalSales, 'total_sales');
        }
    }

    protected function orderQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Order::where('created_at', '>', Carbon::now()->subMinutes(30))
            ->orderBy('status');
    }

    protected function calculationQuery(Collection $orders, string $type = 'total_sales'): Collection
    {
        return $orders->groupBy('status')->map(function ($orders) use ($type) {
            if ($type === 'total_sales') {
                return $orders->sum('total');
            } else {
                return $orders->count();
            }
        });
    }

    protected function analyticQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return Analytic::where('daily_date', Carbon::now()->toDateString());
    }

    protected function upsertAnalytics(Collection $data, string $type, bool $isNewAnalytic = true): void
    {
        $data->map(function ($total, $key) use ($type, $isNewAnalytic) {
            if ($isNewAnalytic) {
                $this->insertAnalytic($type, $key, $total);
            } else {
                $salesAnalytic = $this->analyticQuery()->where('type', 'like', "{$type}_{$key}")->first();

                if ($salesAnalytic) {
                    $salesAnalytic->total += $total;
                    $salesAnalytic->save();
                } else {
                    $this->insertAnalytic($type, $key, $total);
                }
            }
        });
    }

    public function insertAnalytic(string $type, string $status, float $total): void
    {
        $currentDate = Carbon::now();
        $dailyDate = $currentDate->toDateString();
        $weeklyDate = $currentDate->endOfWeek()->toDateString();
        $monthlyDate = $currentDate->endOfMonth()->toDateString();

        Analytic::create([
            'type'  => $type . '_' . $status,
            'total' => $total,
            'daily_date' => $dailyDate,
            'weekly_date' => $weeklyDate,
            'monthly_date' => $monthlyDate,
        ]);
    }
}
