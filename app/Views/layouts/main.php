<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakers Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">Sneakers Store</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/"><i class="fa-solid fa-house"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products"><i class="fa-solid fa-shoe-prints"></i> Products</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="/cart">
                            <i class="fa-solid fa-cart-shopping"></i> Cart
                            <?php if (isset($_SESSION['cart_count']) && $_SESSION['cart_count'] > 0): ?>
                                <span class="badge bg-danger"><?php echo $_SESSION['cart_count']; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/profile"><i class="fa-solid fa-user"></i> Profile</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/login"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/register"><i class="fa-solid fa-user-plus"></i> Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="bg-light py-2 mb-4">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-4 mb-2 mb-md-0">
                    <i class="fa-solid fa-truck text-primary"></i>
                    <span class="ms-2">Free Shipping</span>
                </div>
                <div class="col-md-4 mb-2 mb-md-0">
                    <i class="fa-solid fa-rotate-left text-primary"></i>
                    <span class="ms-2">Easy Returns</span>
                </div>
                <div class="col-md-4">
                    <i class="fa-solid fa-shield-halved text-primary"></i>
                    <span class="ms-2">Secure Payment</span>
                </div>
            </div>
        </div>
    </div>

    <main class="container py-4">
        <?php if (isset($_SESSION['flash_message'])): ?>
            <div class="alert alert-<?php echo $_SESSION['flash_type']; ?> alert-dismissible fade show">
                <?php 
                echo $_SESSION['flash_message'];
                unset($_SESSION['flash_message']);
                unset($_SESSION['flash_type']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php include $content; ?>
    </main>

    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><i class="fa-solid fa-store"></i> About Us</h5>
                    <p>Your premier destination for the latest and greatest sneakers.</p>
                </div>
                <div class="col-md-4">
                    <h5><i class="fa-solid fa-link"></i> Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/products" class="text-light"><i class="fa-solid fa-shoe-prints"></i> Products</a></li>
                        <li><a href="/contact" class="text-light"><i class="fa-solid fa-envelope"></i> Contact</a></li>
                        <li><a href="/terms" class="text-light"><i class="fa-solid fa-file-contract"></i> Terms & Conditions</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5><i class="fa-solid fa-address-card"></i> Contact Us</h5>
                    <p><i class="fa-solid fa-envelope"></i> Email: info@sneakersstore.com<br>
                    <i class="fa-solid fa-phone"></i> Phone: (555) 123-4567</p>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col text-center">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Sneakers Store. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/js/main.js"></script>
</body>
</html> 