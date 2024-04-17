<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;
use Faker\Factory as Faker;
use Illuminate\Support\Testing\Fakes\Fake;

class CreateOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'createOrder {--add=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Used to create order along with order items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $faker = Faker::create();

        $stoppingValue = (int)$this->option('add');

        for ($i = 0; $i < $stoppingValue; $i++) {
            $order = Order::create([
                'status' => $faker->randomElement(['waiting_payment', 'in_progress', 'completed']),
            ]);

            $quantity1 = $faker->numberBetween(1, 10);
            $quantity2 = $faker->numberBetween(11, 20);

            $price1 = $faker->numberBetween(101, 300);
            $price2 = $faker->numberBetween(301, 500);

            $orderItems = [
                [
                    'name' => 'apel',
                    'quantity' => $quantity1,
                    'price' => $price1,
                    'sub_total' => $quantity1 * $price1,
                ],
                [
                    'name' => 'mangga',
                    'quantity' => $quantity2,
                    'price' => $price2,
                    'sub_total' => $quantity2 * $price2,
                ],
            ];

            $order->items()->createMany($orderItems);

            $total = $order->items->sum('sub_total');

            $order->total = $total;
            $order->save();
        }
    }
}
