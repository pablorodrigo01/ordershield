<?php

namespace App\Services\RiskRules;

use App\Models\Order;
use App\Services\RiskRules\Contracts\RiskRuleInterface;

class SuspiciousEmailRule implements RiskRuleInterface
{
    public function handle(Order $order): ?array
    {
        $email = mb_strtolower($order->customer?->email ?? '');

        if (!$email) {
            return null;
        }

        $patterns = ['test', 'fake', 'spam', 'temp'];

        foreach ($patterns as $pattern) {
            if (str_contains($email, $pattern)) {
                return [
                    'score' => 15,
                    'reason' => 'E-mail suspeito',
                ];
            }
        }

        return null;

    }
}