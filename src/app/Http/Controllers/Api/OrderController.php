<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Jobs\AnalyzeOrderRiskJob;
use App\Models\Address;
use App\Models\Order;
use Illuminate\Http\Client\ResponseSequence;
use Illuminate\Http\JsonResponse;
use App\Models\AuditLog;
use App\Services\AuditLogService;
use App\Http\Resources\Api\AuditLogResource;
use App\Http\Resources\Api\OrderResource;
use App\Http\Resources\Api\RiskAnalysisResource;
use App\Http\Requests\Api\ApproveOrderRequest;
use App\Http\Requests\Api\BlockOrderRequest;
use App\Http\Requests\Api\UnderReviewOrderRequest;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(protected AuditLogService $auditLogService)
    {
    }

    /**
     * Retorna pedidos gerais da API.
     *
     * @authenticated
     */
    public function index(): JsonResponse
    {
        $orders = Order::with(['customer', 'address', 'riskAnalysis'])->paginate(10);

        return response()->json($orders);
    }

    /**
     * Retorna pedido específico da API.
     *
     * @authenticated
     */
    public function show(string $id): JsonResponse
    {
        $order = Order::with(['customer', 'address', 'riskAnalysis'])->findOrFail($id);

        return response()->json($order);
    }

    /**
     * Cria um novo pedido.
     *
     * @authenticated
     */
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

        $this->auditLogService->log(
            action: 'order.created',
            entityType: 'order',
            entityId: $order->id,
            metadata: [
                'customer_id' => $order->customer_id,
                'address_id' => $order->address_id,
                'total_amount' => (float) $order->total_amount,
                'status' => $order->status->value,
                'source' => $order->source,
            ]
        );

        AnalyzeOrderRiskJob::dispatch($order->id);

        $order->load(['customer', 'address']);

        return response()->json(new OrderResource($order), 201);
    }

    /**
     * Aprova manualmente um pedido em revisão.
     *
     * @authenticated
     */
    public function approve(string $id, ApproveOrderRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== OrderStatusEnum::UNDER_REVIEW) {
            return response()->json([
                'message' => 'Somente pedidos em revisão podem ser aprovados manualmente.'
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => OrderStatusEnum::APPROVED,
            ]);

            $this->auditLogService->log(
                action: 'order.approved',
                entityType: 'order',
                entityId: $order->id,
                metadata: [
                    'reason' => $request->string('reason')->toString(),
                ]
            );
        });

        $order->load(['customer', 'address', 'riskAnalysis']);

        return response()->json(new OrderResource($order));
    }

    /**
     * Bloqueia manualmente um pedido em revisão.
     *
     * @authenticated
     */
    public function block(string $id, BlockOrderRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status !== OrderStatusEnum::UNDER_REVIEW) {
            return response()->json([
                'message' => 'Somente pedidos em revisão podem ser aprovados manualmente.'
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => OrderStatusEnum::APPROVED,
            ]);

            $this->auditLogService->log(
                action: 'order.blocked_manual',
                entityType: 'order',
                entityId: $order->id,
                metadata: [
                    'reason' => $request->string('reason')->toString(),
                ]
            );
        });

        $order->load(['customer', 'address', 'riskAnalysis']);

        return response()->json(new OrderResource($order));
    }

    /**
     * Altera manualmente um pedido para revisão.
     *
     * @authenticated
     */
    public function underReview(string $id, UnderReviewOrderRequest $request): JsonResponse
    {
        $order = Order::findOrFail($id);

        if ($order->status === OrderStatusEnum::UNDER_REVIEW) {
            return response()->json([
                'message' => 'O pedido já está em revisão.'
            ], 422);
        }

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => OrderStatusEnum::UNDER_REVIEW,
            ]);

            $this->auditLogService->log(
                action: 'order.sent_to_under_review',
                entityType: 'order',
                entityId: $order->id,
                metadata: [
                    'reason' => $request->string('reason')->toString(),
                ]
            );
        });

        $order->load(['customer', 'address', 'riskAnalysis']);

        return response()->json(new OrderResource($order));
    }

    /**
     * Retorna a análise de risco de um pedido.
     *
     * @authenticated
     */
    public function analysis(string $id): JsonResponse
    {
        $order = Order::with('riskAnalysis')->findOrFail($id);

        if (!$order->riskAnalysis) {
            return response()->json([
                'message' => 'Análise de risco ainda não disponível.'
            ], 404);
        }

        return response()->json(
            new RiskAnalysisResource($order->riskAnalysis)
        );
    }

    /**
     * Retorna Log de um pedido específico da API.
     *
     * @authenticated
     */
    public function auditLogs(string $id): JsonResponse
    {
        Order::findOrFail($id);

        $logs = AuditLog::query()
            ->where('entity_type', 'order')
            ->where('entity_id', $id)
            ->latest()
            ->get();

        return response()->json(
            AuditLogResource::collection($logs)
        );
    }
}