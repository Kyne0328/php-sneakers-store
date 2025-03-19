<!-- Admin Header Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="/php-sneakers-store/public/admin">
            <span>Admin Panel</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNavbar">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>" href="/php-sneakers-store/public/admin">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'products' ? 'active' : ''; ?>" href="/php-sneakers-store/public/admin/products">
                        <i class="bi bi-box"></i> Products
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'orders' ? 'active' : ''; ?>" href="/php-sneakers-store/public/admin/orders">
                        <i class="bi bi-cart"></i> Orders
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $current_page === 'users' ? 'active' : ''; ?>" href="/php-sneakers-store/public/admin/users">
                        <i class="bi bi-people"></i> Users
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Profile
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                        <li>
                            <a class="dropdown-item" href="/php-sneakers-store/public">
                                <i class="bi bi-shop"></i> View Store
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="/php-sneakers-store/public/profile">
                                <i class="bi bi-person"></i> My Profile
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="/php-sneakers-store/public/logout">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    body {
        padding-top: 56px;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .navbar {
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
        padding: 0.5rem 1rem;
        z-index: 1030;
    }

    .navbar-brand {
        font-size: 1.1rem;
        font-weight: 500;
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        padding: 0.4rem 0.8rem;
        transition: all 0.2s ease;
        font-size: 0.95rem;
    }

    .nav-link:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.2);
        font-weight: 500;
    }

    .nav-link i {
        margin-right: 6px;
        font-size: 1rem;
    }

    .dropdown-menu {
        border: none;
        box-shadow: 0 2px 4px rgba(0,0,0,.1);
        padding: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.4rem 1rem;
        font-size: 0.95rem;
    }

    .dropdown-item i {
        margin-right: 6px;
        width: 16px;
        text-align: center;
    }

    .dropdown-item:hover {
        background-color: #f8f9fa;
    }

    .dropdown-item.text-danger:hover {
        background-color: #dc3545;
        color: #fff !important;
    }

    @media (max-width: 991.98px) {
        .navbar-collapse {
            background-color: #343a40;
            padding: 0.8rem;
            border-radius: 0.25rem;
            margin-top: 0.5rem;
        }

        .nav-link {
            padding: 0.5rem 1rem;
        }

        .dropdown-menu {
            background-color: transparent;
            border: none;
            box-shadow: none;
            padding-left: 1rem;
        }

        .dropdown-item {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.5rem 1rem;
        }

        .dropdown-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .dropdown-item.text-danger {
            color: #dc3545 !important;
        }

        .dropdown-item.text-danger:hover {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545 !important;
        }
    }
</style> 