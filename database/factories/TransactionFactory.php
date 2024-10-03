<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        if (Item::count() === 0) {
            Item::factory()->count(10)->create();
        }

        $randomItems = Item::all()->random(3);
        $amount = [
            $this->faker->numberBetween(1, $randomItems[0]->stock),
            $this->faker->numberBetween(1, $randomItems[1]->stock),
            $this->faker->numberBetween(1, $randomItems[2]->stock),
        ];
        $total = $randomItems[0]->price * $amount[0] + $randomItems[1]->price * $amount[1] + $randomItems[2]->price * $amount[2];

        return [
            'invoice' => $this->faker->unique()->randomNumber(8),
            'total' => $total,
            'user_id' => 1,
            'items' => json_encode([
                [
                    'id' => $randomItems[0]->id,
                    'sku' => $randomItems[0]->sku,
                    'price' => $randomItems[0]->price
                ],
                [
                    'id' => $randomItems[1]->id,
                    'sku' => $randomItems[1]->sku,
                    'price' => $randomItems[1]->price
                ],
                [
                    'id' => $randomItems[2]->id,
                    'sku' => $randomItems[2]->sku,
                    'price' => $randomItems[2]->price
                ],
            ])
        ];
    }
}
