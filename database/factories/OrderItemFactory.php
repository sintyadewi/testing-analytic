<?php

namespace Database\Factories;

use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $quantity = $this->faker->numberBetween(1, 10);
        $price = $this->faker->numberBetween(100, 500);
        $subTotal = $quantity * $price;

        return [
            'name' => $this->faker->randomElement(['apel', 'mangga', 'pisang', 'pir', 'jeruk', 'markisa']),
            'quantity' => $quantity,
            'price' => $price,
            'sub_total' => $subTotal,
        ];
    }

    /**
     * Configure the model factory.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (OrderItem $orderItem) {
            $orderItem->load('order');

            $order = $orderItem->order;
            $order->total += $orderItem->sub_total;
            $order->save();
        });
    }
}
