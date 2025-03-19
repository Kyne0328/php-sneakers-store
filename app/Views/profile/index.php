<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container mt-5 pt-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                        <h3 class="mt-3"><?php echo $user['name']; ?></h3>
                        <p class="text-muted"><?php echo $user['email']; ?></p>
                    </div>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger">
                            <?php 
                            echo $_SESSION['error'];
                            unset($_SESSION['error']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                            echo $_SESSION['success'];
                            unset($_SESSION['success']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <form action="/php-sneakers-store/public/profile/update" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                        </div>
                        <hr>
                        <h5>Change Password</h5>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Current Password</label>
                            <input type="password" class="form-control" id="current_password" name="current_password">
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h4 class="card-title mb-4">Order History</h4>
                    <?php if (empty($orders)): ?>
                        <div class="alert alert-info">
                            You haven't placed any orders yet. 
                            <a href="/php-sneakers-store/public/products">Start shopping</a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Date</th>
                                        <th>Items</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td>#<?php echo $order['id']; ?></td>
                                            <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            <td><?php echo $order['total_items']; ?></td>
                                            <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
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
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-info view-order" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#viewOrderModal"
                                                        data-id="<?php echo $order['id']; ?>"
                                                        data-created-at="<?php echo $order['created_at']; ?>"
                                                        data-status="<?php echo $order['status']; ?>"
                                                        data-total-amount="<?php echo $order['total_amount']; ?>">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Order Modal -->
<div class="modal fade" id="viewOrderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details #<span id="order_id"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6>Order Information</h6>
                        <p>
                            <strong>Order Date:</strong> <span id="created_at"></span><br>
                            <strong>Order Status:</strong> <span id="order_status"></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Shipping Address</h6>
                        <p id="shipping_address"></p>
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
                        <tbody id="order_items">
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                <td>₱<span id="subtotal"></span></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Shipping:</strong></td>
                                <td id="shipping_cost"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td>₱<span id="total_amount"></span></td>
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
    .table tbody tr:hover {
        background-color: rgba(0,0,0,.02);
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view order button clicks
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            // Populate the view modal with order data
            document.getElementById('order_id').textContent = data.id;
            document.getElementById('created_at').textContent = new Date(data.createdAt).toLocaleString();
            
            // Set order status with badge
            const statusBadge = document.createElement('span');
            statusBadge.className = `badge bg-${getStatusColor(data.status)}`;
            statusBadge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
            document.getElementById('order_status').innerHTML = '';
            document.getElementById('order_status').appendChild(statusBadge);

            // Fetch order details
            fetch(`/php-sneakers-store/public/profile/order/${data.id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(orderData => {
                // Update shipping address
                document.getElementById('shipping_address').innerHTML = `
                    ${orderData.street_address}<br>
                    ${orderData.city}, ${orderData.state} ${orderData.postal_code}
                `;

                // Update order items
                const tbody = document.getElementById('order_items');
                tbody.innerHTML = '';

                if (orderData.items && orderData.items.length > 0) {
                    orderData.items.forEach(item => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${item.image}" 
                                         alt="${item.name}"
                                         class="img-thumbnail me-2" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                    <span>${item.name}</span>
                                </div>
                            </td>
                            <td>${item.size || 'N/A'}</td>
                            <td>₱${parseFloat(item.price).toFixed(2)}</td>
                            <td>${item.quantity}</td>
                            <td>₱${(parseFloat(item.price) * item.quantity).toFixed(2)}</td>
                        `;
                        tbody.appendChild(row);
                    });
                } else {
                    tbody.innerHTML = '<tr><td colspan="5" class="text-center">No items found for this order.</td></tr>';
                }

                // Update totals
                const subtotal = parseFloat(data.totalAmount);
                document.getElementById('subtotal').textContent = subtotal.toFixed(2);
                
                const shippingCost = subtotal >= 100 ? 'Free' : '₱10.00';
                document.getElementById('shipping_cost').textContent = shippingCost;
                
                const total = subtotal >= 100 ? subtotal : subtotal + 10;
                document.getElementById('total_amount').textContent = total.toFixed(2);
            })
            .catch(error => {
                console.error('Error fetching order details:', error);
                document.getElementById('order_items').innerHTML = 
                    '<tr><td colspan="5" class="text-center text-danger">Error loading order details. Please try again.</td></tr>';
            });
        });
    });
});

function getStatusColor(status) {
    switch(status) {
        case 'pending': return 'warning';
        case 'processing': return 'info';
        case 'shipped': return 'primary';
        case 'delivered': return 'success';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}
</script>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 