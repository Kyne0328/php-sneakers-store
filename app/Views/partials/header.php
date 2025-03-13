<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakers Store<?php echo (strpos($_SERVER['REQUEST_URI'], '/admin') !== false) ? ' - Admin Dashboard' : ''; ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/php-sneakers-store/public/css/style.css">
    
    <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false): ?>
        <link rel="stylesheet" href="/php-sneakers-store/public/css/admin.css">
    <?php endif; ?>
</head>
<body>
    <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false): ?>
        <!-- Admin Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
            <div class="container-fluid">
                <button class="btn btn-link text-white me-3" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <a class="navbar-brand" href="/php-sneakers-store/public/admin">
                    <i class="bi bi-shield-lock"></i> Admin Dashboard
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item me-3">
                            <a class="nav-link" href="/php-sneakers-store/public">
                                <i class="bi bi-shop"></i> View Store
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo $_SESSION['user']['name'] ?? 'Admin'; ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="/php-sneakers-store/public/logout">Logout</a></li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php else: ?>
        <!-- Main Store Navigation -->
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="/php-sneakers-store/public">Sneakers Store</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="/php-sneakers-store/public">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/php-sneakers-store/public/products">Products</a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="/php-sneakers-store/public/admin">
                                        <i class="bi bi-speedometer2"></i> Admin Dashboard
                                    </a>
                                </li>
                            <?php endif; ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/php-sneakers-store/public/cart">
                                    <i class="bi bi-cart"></i> Cart
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/php-sneakers-store/public/profile">
                                    <i class="bi bi-person"></i> Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/php-sneakers-store/public/logout">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a class="nav-link" href="/php-sneakers-store/public/login">
                                    <i class="bi bi-box-arrow-in-right"></i> Login
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="/php-sneakers-store/public/register">
                                    <i class="bi bi-person-plus"></i> Register
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>
    
    <div class="container mt-5 pt-4">
    <?php if (strpos($_SERVER['REQUEST_URI'], '/admin') !== false): ?>
    <!-- Admin-specific JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle modals
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', function () {
                    document.body.style.overflow = 'hidden';
                });
                modal.addEventListener('hidden.bs.modal', function () {
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                });
            });

            // Handle sidebar state
            const sidebarToggle = document.querySelector('[data-bs-toggle="collapse"][data-bs-target="#sidebarMenu"]');
            const body = document.querySelector('body');

            // Check and set initial state from localStorage
            if (localStorage.getItem('sidebarCollapsed') === 'true') {
                body.classList.add('sidebar-collapsed');
            }

            // Update state on toggle
            sidebarToggle.addEventListener('click', (e) => {
                e.preventDefault();
                body.classList.toggle('sidebar-collapsed');
                localStorage.setItem('sidebarCollapsed', body.classList.contains('sidebar-collapsed'));
            });

            // Close sidebar on mobile when clicking outside
            document.addEventListener('click', (e) => {
                if (window.innerWidth < 768 && 
                    !e.target.closest('#sidebarMenu') && 
                    !e.target.closest('[data-bs-toggle="collapse"]') && 
                    !body.classList.contains('sidebar-collapsed')) {
                    body.classList.add('sidebar-collapsed');
                    localStorage.setItem('sidebarCollapsed', 'true');
                }
            });
        });
    </script>
    <?php endif; ?>
    </div>
</body>
</html> 