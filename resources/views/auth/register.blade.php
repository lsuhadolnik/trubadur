@extends('layouts.app')

@section('content')
<div class="auth">
    <div class="auth__header">
        <div class="header__title" onclick="window.location.href = '/login'">TRUBADUR</div>
    </div>

    <div class="auth__title">REGISTRACIJA</div>

    <form class="auth__form" method="POST" action="{{ route('register') }}">
        {{ csrf_field() }}

       <div class="auth__form-content">
            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="name">IME</label>
                </div>
                <input class="auth__form-input" type="text" id="name" name="name" title="Vnesite ime" value="{{ old('name') }}" required autofocus/>
            </div>

            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="email">E-MAIL</label>
                </div>
                <input class="auth__form-input" type="email" id="email" name="email" title="Vnesite e-mail" value="{{ old('email') }}" required/>
            </div>

            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="name">GESLO</label>
                </div>
                <input class="auth__form-input" type="password" id="password" name="password" title="Vnesite geslo" required/>
            </div>

            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="password_confirmation">POTRDITEV GESLA</label>
                </div>
                <input class="auth__form-input" type="password" id="password_confirmation" name="password_confirmation" title="Potrdite geslo" required/>
            </div>
        </div>

        @if ($errors->count() > 0)
            <div class="auth__error-group">
                <ul class="auth__error-list">
                    @if ($errors->has('name'))
                        <li class="auth__error">{!! $errors->first('name') !!}</li>
                    @endif
                    @if ($errors->has('email'))
                        <li class="auth__error">{!! $errors->first('email') !!}</li>
                    @endif
                    @if ($errors->has('password'))
                        <li class="auth__error">{!! $errors->first('password') !!}</li>
                    @endif
                    @if ($errors->has('password_confirmation'))
                        <li class="auth__error">{!! $errors->first('password_confirmation') !!}</li>
                    @endif
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="auth__status-group">
                <ul class="auth__status-list">
                    <li class="auth__status">{!! session('status') !!}</li>
                </ul>
            </div>
        @endif

        <div class="auth__button" onclick="this.parentNode.submit()">
            <input type="submit" hidden/>
            <div class="auth__button-hollow">REGISTRACIJA</div>
            <div class="auth__button-full"></div>
        </div>
    </form>

    <div class="auth__links">
        <div class="auth__link-group">
            <span class="auth__link-label">Ste že registrirani?</span><br/>
            <a class="auth__link" href="{{ route('login') }}">Prijava</a>
        </div>
    </div>
</div>
@endsection
