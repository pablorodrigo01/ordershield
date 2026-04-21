<?php

namespace App\DTOs;

class RiskRuleResult
{
    /**
     * Estrutura que retorna: score, motivo, codigo interno.
     */
    public function __construct(
        public readonly int $score,
        public readonly string $reason,
        public readonly string $code,
    ) {

    }
}