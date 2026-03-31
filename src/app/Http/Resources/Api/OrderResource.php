<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'address_id' => $this->address_id,
            'total_amount' => (float) $this->total_amount,
            'status' => $this->status?->value ?? $this->status,
            'source' => $this->source,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'risk_analysis' => new RiskAnalysisResource($this->whenLoaded('riskAnalysis')),
        ];
    }
}