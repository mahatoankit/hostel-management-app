<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
        <a class="navbar-brand" href="/../hostel-management-app/">Hostel Management</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto"> <!-- Align items to the right -->
                <li class="nav-item">
                    <a class="nav-link" href="/../hostel-management-app/">Home</a>
                </li>
                <!-- Login Link (Visible when user is not logged in) -->
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/../hostel-management-app/auth/login.php">Login</a>
                    </li>
                <?php endif; ?>
                <!-- Logout Link (Visible when user is logged in) -->
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="/../hostel-management-app/auth/logout.php">Logout</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>