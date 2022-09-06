<div class="row {{ darkMode($darkMode)}}">
    <div class="col-12 col-lg-3">
        @yield('LIST')
    </div>
    <div class="col mt-3 mt-lg-0">
        {{ Breadcrumbs::render() }}
        <h2>{{ Breadcrumbs::title() }}</h2>
        @yield('CONTENT')
    </div>
</div>
