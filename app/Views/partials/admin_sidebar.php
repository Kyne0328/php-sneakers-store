<!-- Admin Sidebar -->
<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-dark sidebar">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <img src="/php-sneakers-store/public/images/logo.png" alt="Logo" class="img-fluid mb-3" style="max-width: 150px;">
            <h5 class="text-white">Admin Panel</h5>
        </div>
        <ul class="nav flex-column">
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
        </ul>
    </div>
</nav>

<style>
    body {
        padding-left: 250px;
        transition: padding-left 0.3s ease;
    }

    body.sidebar-collapsed {
        padding-left: 0;
    }

    #sidebarMenu {
        position: fixed;
        top: 56px;
        left: 0;
        bottom: 0;
        width: 250px;
        background-color: #343a40;
        overflow-y: auto;
        transform: translateX(0);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    body.sidebar-collapsed #sidebarMenu {
        transform: translateX(-100%);
    }

    .nav-link {
        color: rgba(255, 255, 255, 0.8) !important;
        padding: 0.75rem 1rem;
        white-space: nowrap;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.1);
    }

    .nav-link.active {
        color: #fff !important;
        background-color: rgba(255, 255, 255, 0.2);
    }

    .nav-link i {
        width: 20px;
        text-align: center;
        margin-right: 8px;
    }

    @media (max-width: 767.98px) {
        body {
            padding-left: 0;
        }

        #sidebarMenu {
            transform: translateX(-100%);
        }

        body:not(.sidebar-collapsed) #sidebarMenu {
            transform: translateX(0);
        }

        body:not(.sidebar-collapsed)::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
    }
</style> 