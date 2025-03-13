<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid p-0">
    <!-- Hero Section -->
    <div class="bg-dark text-white py-5" style="margin-top: 56px;">
        <div class="container my-5">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="display-4 fw-bold mb-4">Step into Style</h1>
                    <p class="lead mb-4">Discover our collection of premium sneakers for every occasion. From classic designs to the latest trends.</p>
                    <a href="/php-sneakers-store/public/products" class="btn btn-primary btn-lg">
                        Shop Now <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-md-6">
                    <img src="/php-sneakers-store/public/images/hero-sneakers.jpg" alt="Featured Sneakers" class="img-fluid rounded shadow">
                </div>
            </div>
        </div>
    </div>

    <!-- Featured Products -->
    <div class="container my-5">
        <h2 class="mb-4">Featured Products</h2>
        <div class="row">
            <?php foreach ($featured_products as $product): ?>
                <div class="col-md-4 mb-4">
                    <a href="/php-sneakers-store/public/product/<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 product-card">
                            <img src="<?php echo $product['image']; ?>" class="card-img-top" alt="<?php echo $product['name']; ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-dark"><?php echo $product['name']; ?></h5>
                                <p class="card-text text-muted flex-grow-1"><?php echo $product['description']; ?></p>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <span class="h5 mb-0 text-dark">$<?php echo number_format($product['price'], 2); ?></span>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                        <form action="/php-sneakers-store/public/cart/add" method="POST">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <a href="/php-sneakers-store/public/login" class="btn btn-outline-primary">
                                            <i class="bi bi-box-arrow-in-right"></i> Login to Buy
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card border-0 text-center">
                    <div class="card-body">
                        <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Free Shipping</h5>
                        <p class="card-text text-muted">Free shipping on orders over $100</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 text-center">
                    <div class="card-body">
                        <i class="fas fa-undo fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Easy Returns</h5>
                        <p class="card-text text-muted">30-day return policy</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 text-center">
                    <div class="card-body">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h5 class="card-title">Secure Payment</h5>
                        <p class="card-text text-muted">Safe & secure checkout</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 