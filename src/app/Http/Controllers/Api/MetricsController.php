<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiskAnalysis;
use Illuminate\Http\JsonResponse;
use App\Enums\OrderStatusEnum;

class MetricsController extends Controller
{
    /**
     * Retorna métricas gerais da API.
     *
     * @authenticated
     */
    public function index(): JsonResponse
    {
        $totalOrders = Order::count();

        return response()->json([
            'total_orders' => $totalOrders,
            'approved_orders' => Order::where('status', OrderStatusEnum::APPROVED)->count(),
            'under_review_orders' => Order::where('status', OrderStatusEnum::UNDER_REVIEW)->count(),
            'blocked_orders' => Order::where('status', OrderStatusEnum::BLOCKED)->count(),
            'pending_orders' => Order::where('status', OrderStatusEnum::PENDING)->count(),
            'average_risk_score' => round((float) RiskAnalysis::avg('score'), 2),
            'high_risk_count' => RiskAnalysis::where('classification', 'high')->count(),
            'medium_risk_count' => RiskAnalysis::where('classification', 'medium')->count(),
            'low_risk_count' => RiskAnalysis::where('classification', 'low')->count(),
        ]);
    }
}