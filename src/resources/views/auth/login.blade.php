@extends('layouts.app')

@section('content')
    <div class="card form-card">
        <h1 class="form-title">Entrar</h1>
        <p class="form-subtitle">Acesse o painel do OrderShield com seu e-mail e senha.</p>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/login">
            @csrf

            <div class="form-group">
                <label for="email">E-mail</label>
                <input id="email" type="email" name="email" required autocomplete="username">
            </div>

            <div class="form-group">
                <label for="password">Senha</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <button type="submit" class="btn btn-block">Entrar</button>
        </form>
    </div>
@endsection