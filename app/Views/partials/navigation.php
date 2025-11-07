<?php
// Check if user is logged in and get user info
$auth = new \App\Core\Auth();
$isLoggedIn = $auth->check();
$currentUser = $isLoggedIn ? $auth->user() : null;
$isAdmin = $isLoggedIn && $auth->isAdmin();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand fw-bold" href="/">
            <i class="fas fa-calculator me-2"></i>Bishwo Calculator
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <?php if ($isLoggedIn): ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">
                            <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/calculators">
                            <i class="fas fa-calculator me-1"></i>Calculators
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/history">
                            <i class="fas fa-history me-1"></i>History
                        </a>
                    </li>
                    <?php if ($isAdmin): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-cog me-1"></i>Admin
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="/admin">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a></li>
                                <li><a class="dropdown-item" href="/admin/users">
                                    <i class="fas fa-users me-2"></i>Users
                                </a></li>
                                <li><a class="dropdown-item" href="/admin/settings">
                                    <i class="fas fa-cog me-2"></i>Settings
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="/admin/plugins">
                                    <i class="fas fa-plug me-2"></i>Plugins
                                </a></li>
                                <li><a class="dropdown-item" href="/admin/themes">
                                    <i class="fas fa-palette me-2"></i>Themes
                                </a></li>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <?= htmlspecialchars($currentUser['first_name'] ?? $currentUser['email'] ?? 'User') ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/profile">
                                <i class="fas fa-user me-2"></i>Profile
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="/logout" class="d-inline">
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-sign-out-alt me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            <?php else: ?>
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/login">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/register">Register</a>
                    </li>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Mobile menu overlay for better UX -->
<style>
@media (max-width: 991.98px) {
    .navbar-collapse {
        background: rgba(52, 152, 219, 0.95);
        border-radius: 8px;
        margin-top: 10px;
        padding: 15px;
    }
    
    .navbar-nav .nav-link {
        padding: 10px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    
    .navbar-nav .nav-link:last-child {
        border-bottom: none;
    }
    
    .dropdown-menu {
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(255,255,255,0.2);
    }
    
    .dropdown-item {
        color: #fff;
    }
    
    .dropdown-item:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }
}

.navbar-brand {
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.navbar-brand:hover {
    transform: scale(1.05);
}

.nav-link {
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    transform: translateY(-1px);
}

.nav-link.active {
    background: rgba(255,255,255,0.1);
    border-radius: 5px;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    border-radius: 10px;
    margin-top: 10px;
}

.dropdown-item {
    transition: all 0.2s ease;
    padding: 8px 20px;
}

.dropdown-item:hover {
    background: #f8f9fa;
    transform: translateX(5px);
}

.dropdown-divider {
    margin: 5px 0;
}

.navbar-toggler {
    border: 2px solid rgba(255,255,255,0.3);
}

.navbar-toggler:focus {
    box-shadow: none;
}

.bg-primary {
    background: linear-gradient(135deg, #3498db, #2980b9) !important;
}

/* Animation for active nav items */
.nav-item .nav-link::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    width: 0;
    height: 2px;
    background: #fff;
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-item .nav-link:hover::after {
    width: 80%;
}
</style>
