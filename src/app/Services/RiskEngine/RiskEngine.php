<?php

namespace App\Services\RiskEngine;

use App\DTOs\RiskRuleResult;
use App\Models\Order;
use App\Services\RiskEngine\Contracts\RiskRuleInterface;

class RiskEngine
{
    /**
     * @param  array<int, RiskRuleInterface>  $rules
     */
    public function __construct(
        protected array $rules = []
    ) {
    }

    /**
     * @return array{
     *     score:int,
     *     results:array<int, RiskRuleResult>
     * }
     */
    public function evaluate(Order $order): array
    {
        $score = 0;
        $results = [];

        foreach ($this->rules as $rule) {
            $result = $rule->evaluate($order);

            if (!$result) {
                continue;
            }

            $score += $result->score;
            $results[] = $result;
        }

        return [
            'score' => $score,
            'results' => $results,
        ];
    }
}