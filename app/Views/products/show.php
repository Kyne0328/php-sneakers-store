<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/php-sneakers-store/public/">Home</a></li>
            <li class="breadcrumb-item"><a href="/php-sneakers-store/public/products">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo $product['name']; ?></li>
        </ol>
    </nav>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" class="img-fluid rounded" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3"><?php echo htmlspecialchars($product['name']); ?></h1>
                    <p class="text-muted mb-4"><?php echo htmlspecialchars($product['description']); ?></p>
                    
                    <div class="mb-4">
                        <h3 class="text-primary mb-0">$<?php echo number_format($product['price'], 2); ?></h3>
                        <small class="text-muted">Free shipping on orders over $100</small>
                    </div>

                    <div class="mb-4">
                        <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                            <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                        </span>
                        <?php if ($product['stock'] > 0): ?>
                            <small class="text-muted ms-2"><?php echo $product['stock']; ?> units available</small>
                        <?php endif; ?>
                    </div>

                    <?php if ($product['stock'] > 0): ?>
                        <div class="mb-4">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <form action="/php-sneakers-store/public/cart/add" method="POST">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-auto">
                                            <label for="quantity" class="form-label">Quantity:</label>
                                        </div>
                                        <div class="col-auto">
                                            <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="<?php echo min(10, $product['stock']); ?>">
                                        </div>
                                        <div class="col-auto">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-cart-plus"></i> Add to Cart
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Please <a href="/php-sneakers-store/public/login" class="alert-link">login</a> to add items to your cart.
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Product Features</h5>
                            <ul class="list-unstyled mb-0">
                                <li><i class="bi bi-check text-success me-2"></i>Premium quality materials</li>
                                <li><i class="bi bi-check text-success me-2"></i>Comfortable fit</li>
                                <li><i class="bi bi-check text-success me-2"></i>Durable construction</li>
                                <li><i class="bi bi-check text-success me-2"></i>Stylish design</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($related_products)): ?>
    <div class="mt-5">
        <h3 class="mb-4">You May Also Like</h3>
        <div class="row">
            <?php foreach ($related_products as $related): ?>
                <div class="col-md-4 mb-4">
                    <a href="/php-sneakers-store/public/product/<?php echo $related['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 product-card">
                            <img src="<?php echo htmlspecialchars($related['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($related['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title text-dark"><?php echo htmlspecialchars($related['name']); ?></h5>
                                <p class="card-text text-muted"><?php echo htmlspecialchars(substr($related['description'], 0, 100)) . '...'; ?></p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 mb-0 text-dark">$<?php echo number_format($related['price'], 2); ?></span>
                                    <span class="badge bg-success">In Stock</span>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 