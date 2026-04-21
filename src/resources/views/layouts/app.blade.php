<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>OrderShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        :root {
            --bg: #0f172a;
            --bg-soft: #111827;
            --surface: #1e293b;
            --surface-light: #334155;
            --border: #334155;
            --text: #e5e7eb;
            --text-soft: #94a3b8;
            --primary: #38bdf8;
            --primary-hover: #0ea5e9;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
            --radius: 14px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background:
                radial-gradient(circle at top, rgba(56, 189, 248, 0.08), transparent 25%),
                var(--bg);
            color: var(--text);
        }

        body {
            min-height: 100vh;
        }

        a {
            color: var(--primary);
            text-decoration: none;
        }

        a:hover {
            color: #7dd3fc;
        }

        .app-header {
            position: sticky;
            top: 0;
            z-index: 10;
            backdrop-filter: blur(10px);
            background: rgba(2, 6, 23, 0.85);
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 18px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-badge {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), #2563eb);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            box-shadow: var(--shadow);
        }

        .brand-text strong {
            display: block;
            font-size: 18px;
            line-height: 1.1;
        }

        .brand-text span {
            color: var(--text-soft);
            font-size: 13px;
        }

        .page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 24px 40px;
        }

        .card {
            background: rgba(15, 23, 42, 0.9);
            border: 1px solid rgba(148, 163, 184, 0.12);
            border-radius: var(--radius);
            padding: 20px;
            box-shadow: var(--shadow);
        }

        .page-title {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-subtitle {
            margin: 0 0 28px;
            color: var(--text-soft);
            font-size: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: linear-gradient(180deg, rgba(30, 41, 59, 0.95), rgba(15, 23, 42, 0.95));
            border: 1px solid rgba(148, 163, 184, 0.10);
            border-radius: var(--radius);
            padding: 18px;
            box-shadow: var(--shadow);
        }

        .stat-label {
            color: var(--text-soft);
            font-size: 13px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: bold;
            line-height: 1;
        }

        .section-title {
            margin: 0 0 16px;
            font-size: 22px;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid rgba(148, 163, 184, 0.12);
            background: rgba(15, 23, 42, 0.85);
            box-shadow: var(--shadow);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 760px;
        }

        th,
        td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(148, 163, 184, 0.10);
        }

        th {
            font-size: 13px;
            color: var(--text-soft);
            font-weight: bold;
            background: rgba(2, 6, 23, 0.75);
        }

        tbody tr:hover {
            background: rgba(51, 65, 85, 0.25);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: bold;
            border: 1px solid transparent;
            white-space: nowrap;
        }

        .badge-success {
            background: rgba(34, 197, 94, 0.12);
            color: #86efac;
            border-color: rgba(34, 197, 94, 0.25);
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.12);
            color: #fcd34d;
            border-color: rgba(245, 158, 11, 0.25);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.12);
            color: #fca5a5;
            border-color: rgba(239, 68, 68, 0.25);
        }

        .badge-neutral {
            background: rgba(148, 163, 184, 0.12);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, 0.25);
        }

        .form-card {
            max-width: 420px;
            margin: 60px auto;
        }

        .form-title {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .form-subtitle {
            margin: 0 0 24px;
            color: var(--text-soft);
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            color: #cbd5e1;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px 14px;
            border-radius: 10px;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(2, 6, 23, 0.85);
            color: var(--text);
            outline: none;
            transition: 0.2s ease;
        }

        input:focus {
            border-color: rgba(56, 189, 248, 0.7);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.15);
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            font-size: 14px;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #fecaca;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: linear-gradient(135deg, var(--primary), #2563eb);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s ease;
        }

        .btn:hover {
            background: linear-gradient(135deg, var(--primary-hover), #1d4ed8);
            transform: translateY(-1px);
        }

        .btn-block {
            width: 100%;
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(148, 163, 184, 0.2);
            color: var(--text);
        }

        .btn-ghost:hover {
            background: rgba(148, 163, 184, 0.08);
            transform: translateY(-1px);
        }

        .muted {
            color: var(--text-soft);
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {
            .header-content {
                padding: 16px;
            }

            .page {
                padding: 24px 16px 32px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-title {
                font-size: 24px;
            }

            .form-card {
                margin: 30px auto;
            }
        }
    </style>
</head>

<body>

    <header class="app-header">
        <div class="header-content">
            <div class="brand">
                <div class="brand-badge">OS</div>
                <div class="brand-text">
                    <strong>OrderShield</strong>
                    <span>Painel de análise de pedidos</span>
                </div>
            </div>

            <div>
                @auth
                    <form method="POST" action="/logout" style="margin:0;">
                        @csrf
                        <button type="submit" class="btn btn-ghost">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </header>

    <main class="page">
        @yield('content')
    </main>

</body>

</html>