@extends('layouts.auth')

@section('content')
    <div class="auth-page page-wrapper">
        <div class="o-card">
            <div class="brand">
                <i class="logo fa fa-check-square-o" aria-hidden="true"></i>
                <span>Promise</span>
            </div>
            <form role="form" method="POST" action="/login" class="form">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="email" name="email" placeholder="email" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="password">
                </div>
                <div class="form-group checkbox-group">
                    <input id="remember" type="checkbox" class="checkbox" name="remember">
                    <label for="remember" class="label">Keep me logged in</label>
                </div>
                @include('partials._form_errors')
                <button type="submit" class="form-submit">Login</button>
            </form>
        </div>
        <p class="extra-msg">don't have an account? <a href="/register">sign up</a></p>
    </div>
@endsection
