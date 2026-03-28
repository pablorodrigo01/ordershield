<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Jobs\AnalyzeOrderRiskJob;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::with(['customer', 'address', 'riskAnalysis'])->paginate(10);

        return response()->json($orders);
    }

    public function show(string $id): JsonResponse
    {
        $order = Order::with(['customer', 'address', 'riskAnalysis'])->findOrFail($id);

        return response()->json($order);
    }

    public function store(StoreOrderRequest $request): JsonResponse
    {
        $address = Address::where('id', $request->input('address_id'))
            ->where('customer_id', $request->input('customer_id'))
            ->firstOrFail();

        $order = Order::create([
            'customer_id' => $request->input('customer_id'),
            'address_id' => $address->id,
            'total_amount' => $request->input('total_amount'),
            'status' => OrderStatusEnum::PENDING,
            'source' => $request->string('source')->toString(),
        ]);

        AnalyzeOrderRiskJob::dispatch($order->id);

        return response()->json(
            $order->load(['customer', 'address']),
            201
        );
    }
}