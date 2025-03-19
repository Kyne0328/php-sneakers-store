<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/admin_sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3 border-bottom">
                <h1 class="h2">Orders</h1>
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

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Search Bar -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <form action="/php-sneakers-store/public/admin/orders" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search orders by ID, customer name, or status..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (isset($_GET['search'])): ?>
                                <a href="/php-sneakers-store/public/admin/orders" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-lg"></i> Clear
                                </a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                        <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                        <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                        <td>
                                            <span class="badge bg-<?php 
                                                switch($order['status']) {
                                                    case 'pending': echo 'warning'; break;
                                                    case 'processing': echo 'info'; break;
                                                    case 'shipped': echo 'primary'; break;
                                                    case 'delivered': echo 'success'; break;
                                                    case 'cancelled': echo 'danger'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo ucfirst($order['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($order['created_at'])); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-info view-order" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#viewOrderModal"
                                                    data-id="<?php echo $order['id']; ?>"
                                                    data-user-name="<?php echo htmlspecialchars($order['user_name'] ?? ''); ?>"
                                                    data-user-email="<?php echo htmlspecialchars($order['user_email'] ?? ''); ?>"
                                                    data-created-at="<?php echo $order['created_at'] ?? ''; ?>"
                                                    data-shipping-address="<?php echo htmlspecialchars($order['shipping_address'] ?? ''); ?>"
                                                    data-shipping-city="<?php echo htmlspecialchars($order['shipping_city'] ?? ''); ?>"
                                                    data-shipping-state="<?php echo htmlspecialchars($order['shipping_state'] ?? ''); ?>"
                                                    data-shipping-zip="<?php echo htmlspecialchars($order['shipping_zip'] ?? ''); ?>"
                                                    data-total-amount="<?php echo $order['total_amount'] ?? '0.00'; ?>"
                                                    data-items='<?php echo json_encode($order['items'] ?? []); ?>'>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-primary update-status" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#updateStatusModal"
                                                    data-id="<?php echo $order['id']; ?>"
                                                    data-order-number="<?php echo $order['id']; ?>"
                                                    data-status="<?php echo $order['status']; ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
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
                        <h6>Customer Information</h6>
                        <p>
                            <strong>Name:</strong> <span id="user_name"></span><br>
                            <strong>Email:</strong> <span id="user_email"></span><br>
                            <strong>Order Date:</strong> <span id="created_at"></span>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Shipping Address</h6>
                        <p>
                            <span id="shipping_address"></span><br>
                            <span id="shipping_city"></span>, 
                            <span id="shipping_state"></span> 
                            <span id="shipping_zip"></span>
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
                        <tbody id="order_items">
                        </tbody>
                        <tfoot>
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

<!-- Update Status Modal -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update Order Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/php-sneakers-store/public/admin/update-order-status" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="update_order_id">
                    <div class="mb-3">
                        <label class="form-label">Order Number</label>
                        <p id="update_order_number"></p>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Order Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle view order button clicks
    document.querySelectorAll('.view-order').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            // Populate the view modal with order data
            document.getElementById('order_id').textContent = data.id;
            document.getElementById('user_name').textContent = data.userName;
            document.getElementById('user_email').textContent = data.userEmail;
            document.getElementById('created_at').textContent = new Date(data.createdAt).toLocaleString();
            document.getElementById('shipping_address').textContent = data.shippingAddress;
            document.getElementById('shipping_city').textContent = data.shippingCity;
            document.getElementById('shipping_state').textContent = data.shippingState;
            document.getElementById('shipping_zip').textContent = data.shippingZip;
            document.getElementById('total_amount').textContent = parseFloat(data.totalAmount).toFixed(2);

            // Populate order items
            const items = JSON.parse(data.items);
            const tbody = document.getElementById('order_items');
            tbody.innerHTML = '';

            if (items && items.length > 0) {
                items.forEach(item => {
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
        });
    });

    // Handle update status button clicks
    document.querySelectorAll('.update-status').forEach(button => {
        button.addEventListener('click', function() {
            const data = this.dataset;
            
            // Populate the update modal with order data
            document.getElementById('update_order_id').value = data.id;
            document.getElementById('update_order_number').textContent = '#' + data.orderNumber;
            document.getElementById('status').value = data.status;
        });
    });
});
</script>

<style>
    .card {
        border: none;
        transition: transform 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
    }
    
    .card-header {
        border-bottom: 1px solid rgba(0,0,0,.125);
    }
    
    .table th {
        font-weight: 500;
        color: #495057;
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }

    @media (min-width: 768px) {
        main {
            margin-left: 0 !important;
            width: 100%;
            max-width: 1400px;
            margin: 0 auto !important;
            padding-left: 20px;
            padding-right: 20px;
        }
        
        .col-md-9.col-lg-10 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .container-fluid .row > .col-md-9.col-lg-10 {
            margin: 0 auto;
        }
    }

    .container-fluid {
        padding-left: 0;
        padding-right: 0;
    }

    .row {
        margin-left: 0;
        margin-right: 0;
        justify-content: center;
    }
    
    /* Modal styling */
    .modal-backdrop {
        opacity: 0.5 !important;
    }

    .modal {
        background: rgba(0, 0, 0, 0.5);
    }

    .modal-dialog {
        margin: 1.75rem auto;
        max-width: 500px;
    }
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 