<div class="container-fluid pb-4 pt-2 {{ darkMode($darkMode)}}">
    {{ Breadcrumbs::render() }}
    <h2>{{ Breadcrumbs::title() }}</h2>
    @yield('CONTENT')
</div>
