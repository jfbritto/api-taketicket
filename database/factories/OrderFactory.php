<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        $totalAmount = fake()->randomFloat(2, 50, 1000);
        $platformFee = round($totalAmount * 0.05, 2);
        return [
            'user_id' => User::factory(),
            'event_id' => Event::factory(),
            'status' => OrderStatus::PENDING,
            'total_amount' => $totalAmount,
            'platform_fee' => $platformFee,
            'organizer_amount' => $totalAmount - $platformFee,
            'expires_at' => now()->addMinutes(15),
        ];
    }

    public function paid(): static { return $this->state(['status' => OrderStatus::PAID]); }
    public function awaitingPayment(): static { return $this->state(['status' => OrderStatus::AWAITING_PAYMENT]); }
}
