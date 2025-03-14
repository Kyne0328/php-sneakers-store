<?php
session_start();

// Initialize cart if it doesn't exist
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle add to cart action
if (isset($_GET['action']) && $_GET['action'] === 'add' && isset($_GET['product'])) {
    $product = $_GET['product'];
    if (!isset($_SESSION['cart'][$product])) {
        $_SESSION['cart'][$product] = 1;
    } else {
        $_SESSION['cart'][$product]++;
    }
    header('Location: cart.php');
    exit;
}

// Handle remove from cart action
if (isset($_GET['action']) && $_GET['action'] === 'remove' && isset($_GET['product'])) {
    $product = $_GET['product'];
    if (isset($_SESSION['cart'][$product])) {
        unset($_SESSION['cart'][$product]);
    }
    header('Location: cart.php');
    exit;
}

include 'includes/header.php';
?>

<h2>Shopping Cart</h2>

<?php if (empty($_SESSION['cart'])): ?>
    <p>Your cart is empty.</p>
    <a href="products.php" class="btn">Continue Shopping</a>
<?php else: ?>
    <table class="cart-table">
        <thead>
            <tr>
                <th>Product</th>
                <th>Quantity</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($_SESSION['cart'] as $product => $quantity): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product); ?></td>
                    <td><?php echo $quantity; ?></td>
                    <td>
                        <a href="cart.php?action=remove&product=<?php echo urlencode($product); ?>" class="btn">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="cart-actions">
        <a href="products.php" class="btn">Continue Shopping</a>
        <a href="#" class="btn">Checkout</a>
    </div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?> 