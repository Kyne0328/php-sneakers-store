<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5 flex-grow-1">
    <h2 class="mb-4">Shopping Cart</h2>

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

    <?php if (empty($cart_items)): ?>
        <div class="alert alert-info">
            Your cart is empty. <a href="/php-sneakers-store/public/products">Continue shopping</a>
        </div>
    <?php else: ?>
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Size</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Subtotal</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="img-thumbnail me-3" style="width: 50px;">
                                            <span><?php echo $item['name']; ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo $item['size']; ?></td>
                                    <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                    <td>
                                        <form action="/php-sneakers-store/public/cart/update" method="POST" class="d-flex align-items-center">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="size_id" value="<?php echo $item['size_id']; ?>">
                                            <input type="number" name="quantity" value="<?php echo $item['quantity']; ?>" min="1" max="10" class="form-control form-control-sm" style="width: 70px;">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary ms-2">Update</button>
                                        </form>
                                    </td>
                                    <td>₱<?php echo number_format($item['subtotal'], 2); ?></td>
                                    <td>
                                        <form action="/php-sneakers-store/public/cart/remove" method="POST" class="d-inline">
                                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <input type="hidden" name="size_id" value="<?php echo $item['size_id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to remove this item?');">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td colspan="2"><strong>₱<?php echo number_format($total, 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="/php-sneakers-store/public/products" class="btn btn-outline-primary">Continue Shopping</a>
                    <a href="/php-sneakers-store/public/checkout" class="btn btn-primary">Proceed to Checkout</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }
    .container {
        flex: 1 0 auto;
    }
    footer {
        flex-shrink: 0;
    }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 