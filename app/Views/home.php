<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sneakers Store</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/php-sneakers-store/public/css/style.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="/php-sneakers-store/public/">
                <i class="fas fa-running me-2"></i>Sneakers Store
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/php-sneakers-store/public/"><i class="fas fa-home me-1"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/php-sneakers-store/public/products"><i class="fas fa-shoe-prints me-1"></i>Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/php-sneakers-store/public/cart"><i class="fas fa-shopping-cart me-1"></i>Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/php-sneakers-store/public/login"><i class="fas fa-sign-in-alt me-1"></i>Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/php-sneakers-store/public/register"><i class="fas fa-user-plus me-1"></i>Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section text-center py-5 mb-4">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Step into Style</h1>
                <p class="lead fw-normal text-white-50 mb-0">Discover our latest collection of premium sneakers</p>
            </div>
        </div>
    </div>

    <!-- Featured Products Section -->
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            <!-- Featured Product 1 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img class="card-img-top" src="https://via.placeholder.com/300x200" alt="Featured Sneaker">
                    <div class="card-body">
                        <h5 class="card-title">Air Max Supreme</h5>
                        <p class="card-text">Premium comfort with stylish design.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">$199.99</span>
                            <button class="btn btn-primary">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Featured Product 2 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img class="card-img-top" src="https://via.placeholder.com/300x200" alt="Featured Sneaker">
                    <div class="card-body">
                        <h5 class="card-title">Ultra Boost Pro</h5>
                        <p class="card-text">Maximum performance for athletes.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">$179.99</span>
                            <button class="btn btn-primary">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Featured Product 3 -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <img class="card-img-top" src="https://via.placeholder.com/300x200" alt="Featured Sneaker">
                    <div class="card-body">
                        <h5 class="card-title">Classic Runner</h5>
                        <p class="card-text">Timeless style meets modern comfort.</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 mb-0">$149.99</span>
                            <button class="btn btn-primary">View Details</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <i class="fas fa-shipping-fast fa-3x mb-3 text-primary"></i>
                        <h5>Free Shipping</h5>
                        <p class="text-muted">On orders over $100</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4 mb-md-0">
                    <div class="text-center">
                        <i class="fas fa-undo fa-3x mb-3 text-primary"></i>
                        <h5>30 Days Return</h5>
                        <p class="text-muted">Money back guarantee</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-center">
                        <i class="fas fa-headset fa-3x mb-3 text-primary"></i>
                        <h5>24/7 Support</h5>
                        <p class="text-muted">Customer service excellence</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
                    <span>&copy; 2024 Sneakers Store. All rights reserved.</span>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="me-3"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="me-3"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 