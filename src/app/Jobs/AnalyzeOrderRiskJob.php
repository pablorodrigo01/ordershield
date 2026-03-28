<?php

namespace App\Jobs;

use App\Services\RiskAnalysisService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class AnalyzeOrderRiskJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $orderId
    ) {
    }

    public function handle(RiskAnalysisService $riskAnalysisService): void
    {
        $riskAnalysisService->analyze($this->orderId);
    }
}