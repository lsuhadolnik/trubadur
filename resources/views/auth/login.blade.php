@extends('layouts.app')

@section('content')
<div class="login">
    <div class="login__header">
        <div class="header__title">TRUBADUR</div>
    </div>

    <div class="login__title">PRIJAVA</div>

    <form class="login__form" method="POST" action="{{ route('login') }}">
        {{ csrf_field() }}

        <div class="login__form-content">
            <div class="login__form-group">
                <div class="login__form-label-wrapper">
                    <label class="login__form-label" for="name">E-MAIL</label>
                </div>
                <input class="login__form-input" type="email" id="name" name="email" title="Vnesite e-mail" value="{{ old('email') }}" required autofocus/>
            </div>

            <div class="login__form-group">
                <div class="login__form-label-wrapper">
                    <label class="login__form-label" for="name">GESLO</label>
                </div>
                <input class="login__form-input" type="password" id="password" name="password" title="Vnesite geslo" required/>
            </div>

            <div class="login__form-group">
                <label class="login__form-checkbox-label">
                    <input class="login__form-checkbox" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}/>
                    &nbsp;Zapomni si me
                </label>
            </div>
        </div>

        @if ($errors->count() > 0)
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
                    @if ($errors->has('csrf_token'))
                        <li class="login__error">{!! $errors->first('csrf_token') !!}</li>
                    @endif
                </ul>
            </div>
        @endif

        @if (session('status'))
            <div class="login__status-group">
                <ul class="login__status-list">
                    <li class="login__status">{!! session('status') !!}</li>
                </ul>
            </div>
        @endif

        <div class="login__button" onclick="this.parentNode.submit()">
            <input type="submit" hidden/>
            <div class="login__button-hollow">PRIJAVA</div>
            <div class="login__button-full"></div>
        </div>
    </form>

    <div class="login__links">
        <div class="login__link-group">
            <a class="login__link" href="{{ route('password.request') }}">Ste pozabili geslo?</a>
        </div>

        <div class="login__link-group">
            <span class="login__link-label">Še niste registrirani?&nbsp;</span><br/>
            <a class="login__link" href="{{ route('register') }}">Ustvarite račun</a>
        </div>
    </div>
</div>
@endsection
