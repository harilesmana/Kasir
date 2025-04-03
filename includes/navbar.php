<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button id="sidebarToggle" class="btn btn-link text-white me-2">
            <i class="bi bi-list"></i>
        </button>
        <a class="navbar-brand" href="#">POS System</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/dashboard.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
                </li>
            </ul>
            <span class="navbar-text me-3">
                <i class="bi bi-person-circle me-1"></i>
                <?php echo htmlspecialchars($_SESSION['username']); ?>
                <span class="badge bg-<?php echo $_SESSION['role'] === 'admin' ? 'primary' : 'success'; ?> ms-1">
                    <?php echo ucfirst($_SESSION['role']); ?>
                </span>
            </span>
            <a href="/auth/logout.php" class="btn btn-outline-light">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>