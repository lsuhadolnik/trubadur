@extends('layouts.app')

@section('content')
<div class="auth">
    <div class="auth__header">
        <div class="header__title" onclick="window.location.href = '/login'">TRUBADUR</div>
    </div>

    <div class="auth__title">ZAHTEVA ZA PONASTAVITEV GESLA</div>

    <form class="auth__form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

       <div class="auth__form-content">
            <div class="auth__form-group">
                <div class="auth__form-label-wrapper">
                    <label class="auth__form-label" for="email">E-MAIL</label>
                </div>
                <input class="auth__form-input" type="email" id="email" name="email" title="Vnesite e-mail" value="{{ old('email') }}" required autofocus/>
            </div>
        </div>

        @if ($errors->count() > 0)
            <div class="auth__error-group">
                <ul class="auth__error-list">
                    @if ($errors->has('email'))
                        <li class="auth__error">{!! $errors->first('email') !!}</li>
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
            <div class="auth__button-hollow">POŠLJI</div>
            <div class="auth__button-full"></div>
        </div>
    </form>
</div>
@endsection
