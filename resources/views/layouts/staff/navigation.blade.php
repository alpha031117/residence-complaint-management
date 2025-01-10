<style>
    /* Custom styling */
    .sidebar {
        width: 250px;
        height: 100vh;
        position: fixed;
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
    }
    .sidebar .nav-link {
        color: #333;
    }
    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: #fff;
        font-weight: 500;
    }
    .sidebar .user-section {
        position: absolute;
        bottom: 0;
        width: 100%;
        padding: 15px;
        border-top: 1px solid #dee2e6;
    }
    .sidebar .user-section img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }
    .status-indicator {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    .user-section img {
        border-radius: 50%;
    }
</style>

<div class="sidebar d-flex flex-column">
    <!-- Logo Section -->
    <div class="p-3 text-center border-bottom">
        <h5 class="mb-0">
            <i class="bi bi-bootstrap"></i> Staff Residence Iconic
        </h5>
    </div>

    <!-- Navigation Links -->
    <ul class="nav flex-column mt-3">
        <li class="nav-item">
            <a class="nav-link active d-flex align-items-center {{ Request::is('staff/dashboard') ? 'active' : '' }}" href="{{ route('staff.dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        </li>
        <li class="nav-item">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link btn btn-link">
                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                </button>
            </form>
        </li>
    </ul>

    <!-- Footer User Section -->
    <div class="user-section d-flex align-items-center justify-content-between">
        <!-- User Info -->
        <div class="d-flex align-items-center">
            <img src="https://via.placeholder.com/40" alt="User Profile Picture">
            <div class="ms-2">
                <strong>{{ $user->name }}</strong>
                <br>
                <small class="d-flex align-items-center">
                    <span class="status-indicator bg-success me-1"></span>
                    <span>Active</span>
                </small>
            </div>
        </div>
    </div>

</div>