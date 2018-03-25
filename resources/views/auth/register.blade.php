@extends('layouts.app')

@section('content')
<div class="login">
    <div class="login__logo"></div>

    <form class="register__form" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

        <div class="register__form-title">TRUBADUR</div>

        <div class="register__form-content">
            <div class="register__form-group">
                <div class="register__form-subtitle">Registracija</div>
            </div>

            <div class="register__form-group">
                <input class="register__form-input {{ $errors->has('name') ? 'register__form-input--error' : '' }}" type="name" name="name" placeholder="Ime" title="Vnesite ime" value="{{ old('name') }}" required autofocus/>
            </div>

            <div class="register__form-group">
                <input class="register__form-input {{ $errors->has('email') ? 'register__form-input--error' : '' }}" type="email" name="email" placeholder="E-mail" title="Vnesite e-mail" value="{{ old('email') }}" required/>
            </div>

            <div class="register__form-group">
                <input class="register__form-input {{ $errors->has('password') ? 'register__form-input--error' : '' }}" type="password" name="password" placeholder="Geslo" title="Vnesite geslo" required/>
            </div>

            <div class="register__form-group">
                <input class="register__form-input" type="password" name="password_confirmation" placeholder="Potrditev gesla" title="Potrdite geslo" required/>
            </div>

            <div class="register__form-group register__form-group--link">
                <span class="register__form-text">Ste že registrirani?&nbsp;</span>
                <a class="register__form-link" href="{{ route('login') }}">Prijava</a>
                <span class="register__form-text">.</span>
            </div>

            <div class="register__form-group register__button-group">
                <button class="register__button register__button_submit" type="submit">Registracija</button>
            </div>

            <div class="register__error-group">
                <ul class="register__error-list">
                    @if ($errors->has('name'))
                        <li class="register__error">{!! $errors->first('name') !!}</li>
                    @endif
                    @if ($errors->has('email'))
                        <li class="register__error">{!! $errors->first('email') !!}</li>
                    @endif
                    @if ($errors->has('password'))
                        <li class="register__error">{!! $errors->first('password') !!}</li>
                    @endif
                    @if ($errors->has('password_confirmation'))
                        <li class="register__error">{!! $errors->first('password_confirmation') !!}</li>
                    @endif
                </ul>
            </div>

            <div class="register__status-group">
                <ul class="register__status-list">
                    @if (session('status'))
                        <li class="register__status">{!! session('status') !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>
@endsection
