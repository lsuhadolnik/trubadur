@extends('layouts.app')

@section('content')
<div class="login">
    <div class="login__logo"></div>
    <form class="login__form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="login__form-title">TRUBADUR</div>

        <div class="login__form-controls">
            <div class="login__form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                <input class="login__form-input" type="email" name="email" placeholder="E-mail" title="Enter your e-mail" value="{{ old('email') }}" required autofocus>
                @if ($errors->has('email'))
                    {{ $errors->first('email') }}
                @endif
            </div>

            <div class="login__form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input class="login__form-input" type="password" name="password" title="Enter your password" placeholder="Password" required>
                @if ($errors->has('password'))
                    {{ $errors->first('password') }}
                @endif
            </div>

            <div class="login__form-group">
                <div class="login__form-checkbox-wrapper">
                    <label class="login__form-checkbox-label"><input class="login__form-checkbox" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> Remember Me</label>
                </div>
            </div>

            <div class="login__form-group">
                <a class="login__form-link" href="{{ route('password.request') }}">Forgot Your Password?</a>
            </div>

            <div class="login__form-group login__button-group">
                <button class="login__button login__button_submit" type="submit">Login</button>
            </div>
        </div>
    </form>
</div>
@endsection
