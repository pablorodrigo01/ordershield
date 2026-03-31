<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogService;
use App\Http\Resources\Api\CustomerResource;

class CustomerController extends Controller
{

    public function __construct(protected AuditLogService $auditLogService)
    {
    }

    public function index(): JsonResponse
    {
        $customers = Customer::with('addresses')->paginate(10);

        return response()->json($customers);
    }

    public function show(string $id): JsonResponse
    {
        $customer = Customer::with('addresses')->findOrFail($id);

        return response()->json($customer);
    }

    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = DB::transaction(function () use ($request) {
            $customer = Customer::create([
                'name' => $request->string('name')->toString(),
                'email' => $request->string('email')->toString(),
                'cellphone' => $request->string('cellphone')->toString(),
                'document' => $request->string('document')->toString(),
                'last_access_at' => $request->input('last_access_at'),
            ]);

            foreach ($request->input('addresses', []) as $address) {
                $customer->addresses()->create($address);
            }

            return $customer->load('addresses');
        });

        $this->auditLogService->log(
            action: 'customer.created',
            entityType: 'customer',
            entityId: $customer->id,
            metadata: [
                'email' => $customer->email,
                'document' => $customer->document,
            ]
        );

        return response()->json($customer, 201);
    }
}