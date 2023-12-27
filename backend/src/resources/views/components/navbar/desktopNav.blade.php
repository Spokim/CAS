    <a class="navbar-brand" href="{{ route('home') }}">
        {{ config('app.name', 'Laravel') }}
    </a>
    <div class="flex-grow-1 d-flex flex-column justify-content-around">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}"><span
                        class="{{ request()->is('home') ? 'active' : '' }}">Home</span></a>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('work-shift') }}"><span
                        class="{{ request()->is('work-shift') ? 'active' : '' }}">Report Work Shift</span></a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('past-work-shift') }}"><span
                        class="{{ request()->is('past-work-shift') ? 'active' : '' }}">Past Work Shift</span></a>
            </li>
        </ul>
        @if ((auth()->user()->register_privileges ?? 0) === 1)
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ route('create-news') }}"><span
                            class="{{ request()->is('create-news') ? 'active' : '' }}">Create News</span></a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('supervisor') }}"><span class="{{ request()->is('supervisor') ? 'active' : '' }}">Supervisor</span></a></li>
            </ul>
        @endif
        @if ((auth()->user()->admin_privileges ?? 0) === 1)
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin') }}"><span class="{{ request()->is('admin') ? 'active' : '' }}">Admin</span></a></li>
            </ul>
        @endif
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="btn btn-danger" href="{{ route('logout') }}"
                    onclick="event.preventDefault();
                                             document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </li>
        </ul>
    </div>
