<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2 class="mb-3">Our Products</h2>
        </div>
        <div class="col-md-4">
            <form action="/php-sneakers-store/public/products" method="GET" class="d-flex">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                    <?php if (isset($_GET['search'])): ?>
                        <a href="/php-sneakers-store/public/products" class="btn btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Clear
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <div class="alert alert-info">
            <?php if (isset($_GET['search'])): ?>
                No products found matching "<?php echo htmlspecialchars($_GET['search']); ?>".
                <a href="/php-sneakers-store/public/products" class="alert-link">View all products</a>
            <?php else: ?>
                No products available at the moment.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <a href="/php-sneakers-store/public/product/<?php echo $product['id']; ?>" class="text-decoration-none">
                        <div class="card h-100 product-card">
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
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 