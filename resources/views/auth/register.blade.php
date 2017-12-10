@extends('layouts.app')

@section('content')
    <h1>Register</h1>
    <form role="form" method="POST" action="/register">
        {{ csrf_field() }}
        <input type="text" name="name" placeholder="name">
        <input type="email" name="email" placeholder="email">
        <input type="password" name="password" placeholder="password">
        {{--<input type="password" name="confirm_password" placeholder="confirm password">--}}
        <button type="submit">Submit</button>
    </form>
@endsection