<div class="container pb-4 pt-2 {{ darkMode($darkMode)}}">
    {{ Breadcrumbs::render() }}
    <h2>{{ Breadcrumbs::title() }}</h2>
    @yield('CONTENT')
</div>
