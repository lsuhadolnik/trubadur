@extends('layouts.app')

@section('content')
<div class="email">
    <div class="email__logo"></div>

    <form class="email__form" method="POST" action="{{ route('password.email') }}">
        {{ csrf_field() }}

        <div class="email__form-title">TRUBADUR</div>

        <div class="email__form-content">
            <div class="email__form-group">
                <div class="email__form-subtitle">Zahteva za ponastavitev gesla</div>
            </div>

            <div class="email__form-group">
                <input class="email__form-input {{ $errors->has('email') ? 'email__form-input--error' : '' }}" type="email" name="email" placeholder="E-mail" title="Vnesite e-mail" value="{{ old('email') }}" required autofocus/>
            </div>

            <div class="email__form-group email__button-group">
                <a class="email__button email__button_cancel" href="{{ route('login') }}">Prekliči</a>
                <button class="email__button email__button_submit" type="submit">Pošlji</button>
            </div>

            <div class="email__error-group">
                <ul class="email__error-list">
                    @if ($errors->has('email'))
                        <li class="email__error">{!! $errors->first('email') !!}</li>
                    @endif
                </ul>
            </div>

            <div class="email__status-group">
                <ul class="email__status-list">
                    @if (session('status'))
                        <li class="email__status">{!! session('status') !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>
@endsection
