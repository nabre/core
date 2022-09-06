<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale() ) }}" {!! $HTML_ATTRIBUTE??'' !!}  >
<head>
    @include('Nabre::paginate.skeleton.code.header')
</head>
<body class="{{ darkMode($darkMode)}}">
    @yield('BODY')
    <footer>
        @include('Nabre::paginate.skeleton.code.footer')
        @include('Nabre::paginate.skeleton.code.toast')
    </footer>
</body>
</html>
