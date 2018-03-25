@extends('layouts.app')

@section('content')
<div class="reset">
    <div class="reset__logo"></div>

    <form class="reset__form" method="POST" action="{{ route('password.request') }}">
        {{ csrf_field() }}

        <input type="hidden" name="token" value="{{ $token }}">

        <div class="reset__form-title">TRUBADUR</div>

        <div class="reset__form-content">
            <div class="reset__form-group">
                <div class="reset__form-subtitle">Ponastavitev gesla</div>
            </div>

            <div class="reset__form-group">
                <input class="reset__form-input {{ $errors->has('email') ? 'reset__form-input--error' : '' }}" type="email" name="email" placeholder="E-mail" title="Vnesite e-mail" value="{{ old('email') }}" required/>
            </div>

            <div class="reset__form-group">
                <input class="reset__form-input {{ $errors->has('password') ? 'reset__form-input--error' : '' }}" type="password" name="password" placeholder="Geslo" title="Vnesite geslo" required/>
            </div>

            <div class="reset__form-group">
                <input class="reset__form-input" type="password" name="password_confirmation" placeholder="Potrditev gesla" title="Potrdite geslo" required/>
            </div>

            <div class="reset__form-group reset__button-group">
                <a class="email__button email__button_cancel" href="{{ route('login') }}">Prekliči</a>
                <button class="reset__button reset__button_submit" type="submit">Ponastavi</button>
            </div>

            <div class="reset__error-group">
                <ul class="reset__error-list">
                    @if ($errors->has('email'))
                        <li class="reset__error">{!! $errors->first('email') !!}</li>
                    @endif
                    @if ($errors->has('password'))
                        <li class="reset__error">{!! $errors->first('password') !!}</li>
                    @endif
                    @if ($errors->has('password_confirmation'))
                        <li class="reset__error">{!! $errors->first('password_confirmation') !!}</li>
                    @endif
                </ul>
            </div>
        </div>
    </form>
</div>
@endsection
