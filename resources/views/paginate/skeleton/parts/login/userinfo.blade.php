@auth
    @if (request()->session()->has('impersonate'))
        <li class="nav-item">
            <a class="nav-link" title="user original" href="{{ route('nabre.admin.users.impersonate.create') }}">
                <i class="fa-solid fa-person-walking-arrow-loop-left"></i>
            </a>
        </li>
    @endif
    <span class="navbar-text">
        {{ \Auth::user()->email }}
    </span>
@endauth
