@extends('layouts.app')

@section('content')
    <h1 class="page-title">Dashboard</h1>
    <p class="page-subtitle">Resumo dos pedidos e análises de risco mais recentes.</p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Total de pedidos</div>
            <div class="stat-value">{{ $totalOrders }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Aprovados</div>
            <div class="stat-value">{{ $approvedOrders }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Em revisão</div>
            <div class="stat-value">{{ $underReviewOrders }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Bloqueados</div>
            <div class="stat-value">{{ $blockedOrders }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pendentes</div>
            <div class="stat-value">{{ $pendingOrders }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Score médio</div>
            <div class="stat-value">{{ $averageRiskScore }}</div>
        </div>
    </div>

    <h2 class="section-title">Últimos pedidos</h2>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Valor</th>
                    <th>Score</th>
                    <th>Classificação</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer?->name ?? 'Não informado' }}</td>
                        <td>
                            @php
                                $status = strtolower($order->status->value ?? $order->status ?? '');
                            @endphp

                            <span class="badge
                                        @if(str_contains($status, 'aprov')) badge-success
                                        @elseif(str_contains($status, 'revis')) badge-warning
                                        @elseif(str_contains($status, 'bloque')) badge-danger
                                        @else badge-neutral
                                        @endif
                                    ">
                                {{ $order->status->value ?? $order->status ?? '-' }}
                            </span>
                        </td>
                        <td>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                        <td>{{ $order->riskAnalysis?->score ?? '-' }}</td>
                        <td>
                            @php
                                $classification = strtolower($order->riskAnalysis?->classification?->value ?? $order->riskAnalysis?->classification ?? '');
                            @endphp

                            <span class="badge
                                        @if(str_contains($classification, 'baixo')) badge-success
                                        @elseif(str_contains($classification, 'médio') || str_contains($classification, 'medio')) badge-warning
                                        @elseif(str_contains($classification, 'alto')) badge-danger
                                        @else badge-neutral
                                        @endif
                                    ">
                                {{ $order->riskAnalysis?->classification?->value ?? $order->riskAnalysis?->classification ?? '-' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="muted" style="text-align:center;padding:24px;">
                            Nenhum pedido encontrado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection