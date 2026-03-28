<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\RiskClassificationEnum;
use App\Models\AuditLog;
use App\Models\Order;
use App\Models\RiskAnalysis;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class RiskAnalysisService
{
    public function analyze(string $orderId): RiskAnalysis
    {
        $order = Order::with('customer')->findOrFail($orderId);

        $score = 0;
        $reasons = [];

        if ((float) $order->total_amount > 5000) {
            $score += 30;
            $reasons[] = 'Pedido acima de 5000';
        }

        if ($order->customer && $order->customer->created_at->greaterThan(Carbon::now()->subDays(7))) {
            $score += 20;
            $reasons[] = 'Cliente criado recentemente';
        }

        $recentOrdersCount = Order::query()
            ->where('customer_id', $order->customer_id)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->count();

        if ($recentOrdersCount >= 3) {
            $score += 20;
            $reasons[] = 'Múltiplos pedidos em curto intervalo';
        }

        if ($this->hasSuspiciousEmail($order->customer->email ?? null)) {
            $score += 15;
            $reasons[] = 'E-mail suspeito';
        }

        [$classification, $status] = $this->resolveClassificationAndStatus($score);

        return DB::transaction(function () use ($order, $score, $classification, $status, $reasons) {
            $analysis = RiskAnalysis::updateOrCreate(
                ['order_id' => $order->id],
                [
                    'score' => $score,
                    'classification' => $classification,
                    'reasons' => $reasons,
                    'analyzed_at' => now(),
                ]
            );

            $order->update([
                'status' => $status,
            ]);

            AuditLog::create([
                'user_id' => null,
                'action' => 'order.analyzed',
                'entity_type' => 'order',
                'entity_id' => $order->id,
                'metadata' => [
                    'score' => $score,
                    'classification' => $classification->value,
                    'status' => $status->value,
                    'reasons' => $reasons,
                ],
            ]);

            return $analysis;
        });
    }

    private function resolveClassificationAndStatus(int $score): array
    {
        return match (true) {
            $score >= 60 => [RiskClassificationEnum::HIGH, OrderStatusEnum::BLOCKED],
            $score >= 30 => [RiskClassificationEnum::MEDIUM, OrderStatusEnum::UNDER_REVIEW],
            default => [RiskClassificationEnum::LOW, OrderStatusEnum::APPROVED],
        };
    }

    private function hasSuspiciousEmail(?string $email): bool
    {
        if (!$email) {
            return false;
        }

        $suspiciousPatterns = [
            'test',
            'fake',
            'spam',
            'temp',
        ];

        $email = mb_strtolower($email);

        foreach ($suspiciousPatterns as $pattern) {
            if (str_contains($email, $pattern)) {
                return true;
            }
        }

        return false;
    }
}