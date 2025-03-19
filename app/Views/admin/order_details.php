<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details #<?php echo $order['id']; ?></h2>
        <a href="/php-sneakers-store/public/admin/orders" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Orders
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Items</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Size</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order_items as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $item['image']; ?>" 
                                                     alt="<?php echo $item['name']; ?>"
                                                     class="img-thumbnail me-2" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <span><?php echo $item['name']; ?></span>
                                            </div>
                                        </td>
                                        <td><?php echo $item['size'] ?: 'N/A'; ?></td>
                                        <td>₱<?php echo number_format($item['price'], 2); ?></td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                    <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                    <td><?php echo $order['total_amount'] >= 100 ? 'Free' : '₱10.00'; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                    <td>
                                        <strong>
                                            ₱<?php echo number_format(
                                                $order['total_amount'] >= 100 
                                                    ? $order['total_amount'] 
                                                    : $order['total_amount'] + 10, 
                                                2
                                            ); ?>
                                        </strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Customer Information</h5>
                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $order['phone']; ?></p>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">Shipping Information</h5>
                    <p><strong>Address:</strong> <?php echo $order['shipping_address']; ?></p>
                    <p><strong>City:</strong> <?php echo $order['shipping_city']; ?></p>
                    <p><strong>State:</strong> <?php echo $order['shipping_state']; ?></p>
                    <p><strong>ZIP Code:</strong> <?php echo $order['shipping_zip']; ?></p>
                </div>
            </div>

            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title mb-4">Order Status</h5>
                    <form action="/php-sneakers-store/public/admin/update-order-status" method="POST">
                        <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                        <div class="mb-3">
                            <select name="status" class="form-select">
                                <?php 
                                $statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                                foreach ($statuses as $status):
                                    $selected = $status === $order['status'] ? 'selected' : '';
                                ?>
                                    <option value="<?php echo $status; ?>" <?php echo $selected; ?>>
                                        <?php echo ucfirst($status); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-1"></i>Update Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 