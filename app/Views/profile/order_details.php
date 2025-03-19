<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Order Details #<?php echo $order['id']; ?></h2>
        <a href="/php-sneakers-store/public/profile" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Back to Profile
        </a>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title mb-4">Customer Information</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <p><strong>Name:</strong> <?php echo $order['customer_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $order['customer_email']; ?></p>
                    <p><strong>Order Date:</strong> <?php echo date('n/j/Y, g:i:s A', strtotime($order['created_at'])); ?></p>
                </div>
                <div class="col-md-6">
                    <h5>Shipping Address</h5>
                    <p>
                        <?php echo $order['street_address']; ?><br>
                        <?php echo $order['city']; ?>, 
                        <?php echo $order['state']; ?> 
                        <?php echo $order['postal_code']; ?><br>
                        <?php echo $order['country']; ?><br>
                        <?php echo $order['phone']; ?>
                    </p>
                </div>
            </div>

            <h4 class="mt-4 mb-3">Order Items</h4>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>PRODUCT</th>
                            <th>SIZE</th>
                            <th>PRICE</th>
                            <th>QUANTITY</th>
                            <th>SUBTOTAL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($order_items)): ?>
                            <tr>
                                <td colspan="5" class="text-center">No items found for this order.</td>
                            </tr>
                        <?php else: ?>
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
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4" class="text-end"><strong>Total:</strong></td>
                            <td><strong>₱<?php echo number_format($order['total_amount'], 2); ?></strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details #<?php echo $order['id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p>
                            <strong>Order Date:</strong> <?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?><br>
                            <strong>Order Status:</strong> 
                                <span class="badge bg-<?php 
                                    echo match($order['status']) {
                                        'pending' => 'warning',
                                        'processing' => 'info',
                                        'shipped' => 'primary',
                                        'delivered' => 'success',
                                        'cancelled' => 'danger',
                                        default => 'secondary'
                                    };
                                ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Shipping Address</h6>
                        <p>
                            <?php echo $order['street_address']; ?><br>
                            <?php echo $order['city']; ?>, 
                            <?php echo $order['state']; ?> 
                            <?php echo $order['postal_code']; ?>
                        </p>
                    </div>
                </div>

                <h6>Order Items</h6>
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        border: none;
    }
    
    .table th {
        font-weight: 500;
        color: #495057;
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .img-thumbnail {
        border-radius: 4px;
    }
    
    .table tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 