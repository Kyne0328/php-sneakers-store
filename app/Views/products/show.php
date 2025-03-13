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

    <div class="card shadow">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <img src="<?php echo $product['image']; ?>" class="img-fluid rounded" alt="<?php echo $product['name']; ?>">
                </div>
                <div class="col-md-6">
                    <h1 class="mb-3"><?php echo $product['name']; ?></h1>
                    <p class="text-muted mb-4"><?php echo $product['description']; ?></p>
                    
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
                            <form action="/php-sneakers-store/public/cart/add" method="POST">
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <div class="row g-3 align-items-center">
                                    <div class="col-auto">
                                        <label for="quantity" class="form-label">Quantity:</label>
                                    </div>
                                    <div class="col-auto">
                                        <input type="number" id="quantity" name="quantity" class="form-control" value="1" min="1" max="10">
                                    </div>
                                    <div class="col-auto">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i>Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>

                    <div class="card bg-light">
                        <div class="card-body">
                            <h5 class="card-title">Product Features</h5>
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success me-2"></i>Premium quality materials</li>
                                <li><i class="fas fa-check text-success me-2"></i>Comfortable fit</li>
                                <li><i class="fas fa-check text-success me-2"></i>Durable construction</li>
                                <li><i class="fas fa-check text-success me-2"></i>Stylish design</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 