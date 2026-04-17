<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>OrderShield</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            margin: 0;
        }

        header {
            background: #020617;
            padding: 16px;
            display: flex;
            justify-content: space-between;
        }

        a {
            color: #38bdf8;
            text-decoration: none;
        }

        .container {
            padding: 24px;
        }

        .card {
            background: #020617;
            padding: 16px;
            border-radius: 8px;
        }

        table {
            width: 100%;
            background: #020617;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 10px;
            border-bottom: 1px solid #1e293b;
        }

        th {
            background: #020617;
        }

        button {
            background: #38bdf8;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <header>
        <div>
            <strong>OrderShield</strong>
        </div>

        <div>
            @auth
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit">Logout</button>
                </form>
            @endauth
        </div>
    </header>

    <div class="container">
        @yield('content')
    </div>

</body>

</html>