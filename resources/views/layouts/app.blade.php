<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name') }}</title>

        <!-- Styles -->
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <!-- Scripts -->
        <script>
            window.Laravel = {!! json_encode(['csrfToken' => csrf_token()]) !!};
        </script>

        <!-- MIDI.js -->
        <script src="{{ asset('lib/midi/shim/Base64.js') }}" type="text/javascript"></script>
        <script src="{{ asset('lib/midi/shim/Base64binary.js') }}" type="text/javascript"></script>
        <script src="{{ asset('lib/midi/shim/WebAudioAPI.js') }}" type="text/javascript"></script>
        <script src="{{ asset('lib/midi/midi.min.js') }}" type="text/javascript"></script>
        <script src="{{ asset('lib/midi/util/dom_request_xhr.js') }}" type="text/javascript"></script>
        <script src="{{ asset('lib/midi/util/dom_request_script.js') }}" type="text/javascript"></script>
    </head>
    <body>
        <div id="app">
            @yield('content')
        </div>

        <!-- Scripts -->
        <script src="{{ asset('js/app.js') }}"></script>
    </body>
</html>
