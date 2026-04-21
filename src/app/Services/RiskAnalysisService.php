<?php

namespace App\Services;

use App\Enums\OrderStatusEnum;
use App\Enums\RiskClassificationEnum;
use App\Models\Order;
use App\Models\RiskAnalysis;
use App\Services\RiskEngine\RiskEngine;
use App\Services\RiskEngine\Rules\HighAmountRule;
use App\Services\RiskEngine\Rules\ManyRecentOrdersRule;
use App\Services\RiskEngine\Rules\RecentCustomerRule;
use App\Services\RiskEngine\Rules\SuspiciousEmailRule;
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

        $engine = new RiskEngine([
            new HighAmountRule(),
            new RecentCustomerRule(),
            new SuspiciousEmailRule(),
            new ManyRecentOrdersRule(),
        ]);

        $evaluation = $engine->evaluate($order);

        $score = $evaluation['score'];
        $results = $evaluation['results'];
        $reasons = array_map(fn($result) => $result->reason, $results);
        $ruleCodes = array_map(fn($result) => $result->code, $results);

        [$classification, $status] = $this->resolveClassificationAndStatus($score);

        return DB::transaction(function () use ($order, $score, $classification, $status, $reasons, $ruleCodes) {
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
                    'rule_codes' => $ruleCodes,
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