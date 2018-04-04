@extends('layouts.app')

@section('content')
<div class="auth">
    <div class="auth__header">
        <div class="header__title">TRUBADUR</div>
    </div>

    <div class="auth__title">PRIJAVA</div>

    <form class="auth__form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="auth__form-content">
            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="email">E-MAIL</label>
                </div>
                <input class="auth__form-input" type="email" id="email" name="email" title="Vnesite e-mail" value="{{ old('email') }}" required autofocus/>
            </div>

            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="name">GESLO</label>
                </div>
                <input class="auth__form-input" type="password" id="password" name="password" title="Vnesite geslo" required/>
            </div>

            <div class="auth__form-group auth__form-group--checkbox">
                <label class="auth__form-checkbox-label">Zapomni si me
                    <input class="auth__form-checkbox" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                    <span class="auth__form-checkbox-checkmark"></span>
                </label>
            </div>
        </div>

        @if ($errors->count() > 0)
            <div class="auth__error-group">
                <ul class="auth__error-list">
                    @if ($errors->has('email'))
                        <li class="auth__error">{!! $errors->first('email') !!}</li>
                    @endif
                    @if ($errors->has('password'))
                        <li class="auth__error">{!! $errors->first('password') !!}</li>
                    @endif
                    @if ($errors->has('email_token'))
                        <li class="auth__error">{!! $errors->first('email_token') !!}</li>
                    @endif
                    @if ($errors->has('csrf_token'))
                        <li class="auth__error">{!! $errors->first('csrf_token') !!}</li>
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
            <div class="auth__button-hollow">PRIJAVA</div>
            <div class="auth__button-full"></div>
        </div>
    </form>

    <div class="auth__links">
        <div class="auth__link-group">
            <a class="auth__link" href="{{ route('password.request') }}">Ste pozabili geslo?</a>
        </div>

        <div class="auth__link-group">
            <span class="auth__link-label">Še niste registrirani?</span><br/>
            <a class="auth__link" href="{{ route('register') }}">Ustvarite račun</a>
        </div>
    </div>
</div>
@endsection
