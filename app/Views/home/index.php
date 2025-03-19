<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="hero-section text-center py-5 mb-4" style="background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('/php-sneakers-store/public/images/hero-sneakers.jpg'); background-size: cover; background-position: center;">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Step into Style</h1>
                <p class="lead fw-normal text-white-50 mb-4">Discover our latest collection of premium sneakers</p>
                <a href="/php-sneakers-store/public/products" class="btn btn-primary btn-lg">
                    Shop Now <i class="fas fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <a href="/php-sneakers-store/public/product/<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 product-card shadow-sm">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-dark"><?php echo $product['name']; ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?php echo $product['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="h5 mb-0 text-dark">₱<?php echo number_format($product['price'], 2); ?></span>
                                    <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                                    </span>
                                </div>
                                <div class="text-center mt-2">
                                    <small class="text-muted">Click to view details & select size</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Features Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-truck fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">Free Shipping</h5>
                            <p class="card-text text-muted">Free shipping on orders over ₱100</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-arrow-repeat fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">Easy Returns</h5>
                            <p class="card-text text-muted">30-day return policy</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 text-center h-100 shadow-sm">
                        <div class="card-body">
                            <i class="bi bi-chat-dots fs-1 text-primary mb-3"></i>
                            <h5 class="card-title">24/7 Support</h5>
                            <p class="card-text text-muted">Customer service excellence</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 