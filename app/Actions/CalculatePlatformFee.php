<?php

namespace App\Actions;

class CalculatePlatformFee
{
    public function execute(float $totalAmount): array
    {
        $feePercentage = config('platform.fee_percentage') / 100;
        $platformFee = round($totalAmount * $feePercentage, 2);
        $organizerAmount = round($totalAmount - $platformFee, 2);

        return [
            'platform_fee' => $platformFee,
            'organizer_amount' => $organizerAmount,
        ];
    }
}
