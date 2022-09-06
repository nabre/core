<div class="{{ darkMode($darkMode) }}">
    <nav class="navbar navbar-expand-lg bg-light">
        <div class="container-fluid">
            <ul class="navbar-nav">
            </ul>
            <ul class="navbar-nav d-flex">
                @include('Nabre::paginate.skeleton.parts.lang')
                @include('Nabre::paginate.skeleton.parts.login.userinfo')
                {!! \Nabre\Repositories\Menu\Generate::name('mainmenu', 'top') !!}
            </ul>
        </div>
    </nav>
</div>
