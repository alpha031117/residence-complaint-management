<nav class="navbar navbar-expand-lg navbar-light bg-body-primary">
    <div class="container-fluid justify-content-center">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item ms-3">
                    <a class="nav-link {{ Request::is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ Request::is('file_complaint') ? 'active' : '' }}" href="{{ route('file_complaint') }}">File Complaint</a>
                </li>
                <li class="nav-item ms-3">
                    <a class="nav-link {{ Request::is('profile') ? 'active' : '' }}" href="{{ route('myprofile') }}">My Account</a>
                </li>
                <li class="nav-item ms-3">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="nav-link btn btn-link">
                            Log Out
                        </button>
                    </form>
                </li>                
            </ul>
        </div>
    </div>
</nav>