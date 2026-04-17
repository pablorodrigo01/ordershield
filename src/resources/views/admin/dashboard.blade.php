@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard OrderShield</h1>

        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;">
            <div><strong>Total:</strong> {{ $totalOrders }}</div>
            <div><strong>Aprovados:</strong> {{ $approvedOrders }}</div>
            <div><strong>Em revisão:</strong> {{ $underReviewOrders }}</div>
            <div><strong>Bloqueados:</strong> {{ $blockedOrders }}</div>
            <div><strong>Pendentes:</strong> {{ $pendingOrders }}</div>
            <div><strong>Score médio:</strong> {{ $averageRiskScore }}</div>
        </div>

        <h2>Últimos pedidos</h2>

        <table border="1" cellpadding="10" cellspacing="0" width="100%">
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
                @foreach($recentOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer?->name }}</td>
                        <td>{{ $order->status->value ?? $order->status }}</td>
                        <td>R$ {{ number_format((float) $order->total_amount, 2, ',', '.') }}</td>
                        <td>{{ $order->riskAnalysis?->score ?? '-' }}</td>
                        <td>{{ $order->riskAnalysis?->classification?->value ?? $order->riskAnalysis?->classification ?? '-' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection