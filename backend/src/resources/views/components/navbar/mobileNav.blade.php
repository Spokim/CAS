<div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">CVS</a>
    
    <!-- Create a button to toggle the menu -->
    <button class="navbar-toggler custom-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Define the navigation links -->
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="{{ route('home') }}">{{ 'Home' }}</a>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('work-shift') }}">{{ 'Report Work Shift' }}</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('past-work-shift') }}">{{ 'Past Work Shift' }}</a>
            </li>
        </ul>
        @if ((auth()->user()->register_privileges ?? 0) === 1)
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ route('create-news') }}">{{ 'Create News' }}</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('supervisor') }}">{{ 'Supervisor' }}</a></li>
            </ul>
        @endif
        @if ((auth()->user()->admin_privileges ?? 0) === 1)
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="{{ route('admin') }}">{{ 'Admin' }}</a></li>
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
</div>