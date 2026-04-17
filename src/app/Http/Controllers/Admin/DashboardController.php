<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiskAnalysis;
use Illuminate\Contracts\View\View;
use App\Enums\OrderStatusEnum;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalOrders' => Order::count(),
            'approvedOrders' => Order::where('status', OrderStatusEnum::APPROVED)->count(),
            'underReviewOrders' => Order::where('status', OrderStatusEnum::UNDER_REVIEW)->count(),
            'blockedOrders' => Order::where('status', OrderStatusEnum::BLOCKED)->count(),
            'pendingOrders' => Order::where('status', OrderStatusEnum::PENDING)->count(),
            'averageRiskScore' => round((float) RiskAnalysis::avg('score'), 2),
            'recentOrders' => Order::with(['customer', 'riskAnalysis'])
                ->latest()
                ->take(10)
                ->get(),
        ]);
    }
}