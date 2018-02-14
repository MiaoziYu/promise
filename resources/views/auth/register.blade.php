@extends('layouts.auth')

@section('content')
    <div class="auth-page page-wrapper">
        <div class="o-card">
            <div class="brand">
                <i class="logo fa fa-check-square-o" aria-hidden="true"></i>
                <span>Promise</span>
            </div>
            <form role="form" method="POST" action="/register" class="form">
                {{ csrf_field() }}
                <div class="form-group">
                    <input type="text" name="name" placeholder="name" value="{{ old('name') }}">
                </div>
                <div class="form-group">
                    <input type="email" name="email" placeholder="email" value="{{ old('email') }}">
                </div>
                <div class="form-group">
                    <input type="password" name="password" placeholder="password">
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" placeholder="confirm password">
                </div>
                @include('partials._form_errors')
                <button class="form-submit" type="submit">register</button>
            </form>
        </div>
        <p class="extra-msg">already have an account? <a href="/login">login</a></p>
    </div>
@endsection
