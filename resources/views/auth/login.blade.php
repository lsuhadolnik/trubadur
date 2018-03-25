@extends('layouts.app')

@section('content')
<div class="login">
    <div class="login__logo"></div>
    <form class="login__form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="login__form-title">TRUBADUR</div>

        <div class="login__form-content">
            <div class="login__form-group">
                <div class="login__form-subtitle">Prijava</div>
            </div>

            <div class="login__form-group">
                <input class="login__form-input {{ $errors->has('email') ? 'login__form-input--error' : '' }}" type="email" name="email" placeholder="E-mail" title="Vnesite e-mail" value="{{ old('email') }}" required autofocus/>
            </div>

            <div class="login__form-group">
                <input class="login__form-input {{ $errors->has('password') ? 'login__form-input--error' : '' }}" type="password" name="password" placeholder="Geslo" title="Vnesite geslo" required/>
            </div>

            <div class="login__form-group">
                <label class="login__form-checkbox-label">
                    <input class="login__form-checkbox" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                    &nbsp;Zapomni si me
                </label>
            </div>

            <div class="login__form-group login__form-group--link">
                <a class="login__form-link" href="{{ route('password.request') }}">Ste pozabili geslo?</a>
            </div>

            <div class="login__form-group">
                <span class="login__form-text">Še niste registrirani?&nbsp;</span>
                <a class="login__form-link" href="{{ route('register') }}">Ustvarite račun</a>
                <span class="login__form-text">.</span>
            </div>

            <div class="login__form-group login__button-group">
                <button class="login__button login__button_submit" type="submit">Prijava</button>
            </div>

            <div class="login__error-group">
                <ul class="login__error-list">
                    @if ($errors->has('email'))
                        <li class="login__error">{!! $errors->first('email') !!}</li>
                    @endif
                    @if ($errors->has('password'))
                        <li class="login__error">{!! $errors->first('password') !!}</li>
                    @endif
                    @if ($errors->has('email_token'))
                        <li class="login__error">{!! $errors->first('email_token') !!}</li>
                    @endif
                </ul>
            </div>

            <div class="login__status-group">
                <ul class="login__status-list">
                    @if (session('status'))
                        <li class="login__status">{!! session('status') !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>
@endsection
