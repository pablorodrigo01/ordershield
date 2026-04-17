<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\RiskClassificationEnum;
use App\Models\Order;
use App\Models\RiskAnalysis;
use App\Services\RiskRules\HighAmountRule;
use App\Services\RiskRules\RecentCustomerRule;
use App\Services\RiskRules\SuspiciousEmailRule;
use Illuminate\Support\Facades\DB;

class RiskAnalysisService
{
    public function __construct(
        protected AuditLogService $auditLogService
    ) {
    }

    public function analyze(string $orderId): RiskAnalysis
    {
        $order = Order::with('customer')->findOrFail($orderId);

        $rules = [
            new HighAmountRule(),
            new RecentCustomerRule(),
            new SuspiciousEmailRule(),
        ];

        $score = 0;
        $reasons = [];

        foreach ($rules as $rule) {
            $result = $rule->handle($order);

            if (! $result) {
                continue;
            }

            $score += $result['score'];
            $reasons[] = $result['reason'];
        }

        [$classification, $status] = $this->resolveClassificationAndStatus($score);

        return DB::transaction(function () use ($order, $score, $reasons, $classification, $status) {
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

            $this->auditLogService->log(
                action: 'order.analyzed',
                entityType: 'order',
                entityId: $order->id,
                metadata: [
                    'score' => $score,
                    'classification' => $classification->value,
                    'status' => $status->value,
                    'reasons' => $reasons,
                ],
                userId: null
            );

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
}