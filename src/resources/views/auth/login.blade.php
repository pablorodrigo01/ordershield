@extends('layouts.app')

@section('content')
    <div class="card" style="max-width:400px;margin:auto;">
        <h2>Login</h2>

        @if ($errors->any())
            <div style="color:red;">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div>
                <label>Email</label>
                <input type="email" name="email" required style="width:100%;">
            </div>

            <div>
                <label>Senha</label>
                <input type="password" name="password" required style="width:100%;">
            </div>

            <br>

            <button type="submit">Entrar</button>
        </form>
    </div>
@endsection