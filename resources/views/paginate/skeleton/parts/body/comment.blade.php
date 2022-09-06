<div class="row {{ darkMode($darkMode)}}" >
    <div class="col-12 col-md-8">
        {{ Breadcrumbs::render() }}
        <h2>{{ Breadcrumbs::title() }}</h2>
        @yield('CONTENT')
    </div>
    <div class="col">
        @yield('COMMENT')
    </div>
</div>
