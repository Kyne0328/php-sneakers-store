<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/admin_sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <!-- Statistics Cards -->
            <div class="row g-4 mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-primary text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Orders</h5>
                            <h2 class="card-text mb-0"><?php echo $stats['total_orders']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-success text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Revenue</h5>
                            <h2 class="card-text mb-0">₱<?php echo number_format($stats['total_revenue'], 2); ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-info text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Total Products</h5>
                            <h2 class="card-text mb-0"><?php echo $stats['total_products']; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="card bg-warning text-white h-100">
                        <div class="card-body">
                            <h5 class="card-title">Low Stock Items</h5>
                            <h2 class="card-text mb-0"><?php echo $stats['low_stock']; ?></h2>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Recent Orders</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recent_orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo htmlspecialchars($order['user_name']); ?></td>
                                                <td>₱<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $order['status'] === 'completed' ? 'success' : 
                                                            ($order['status'] === 'pending' ? 'warning' : 'secondary'); 
                                                    ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Products -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title mb-0">Top Selling Products</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Total Sold</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($top_products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['name']); ?></td>
                                                <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                                <td><?php echo $product['stock']; ?></td>
                                                <td><?php echo $product['total_quantity'] ?? 0; ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

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
</style>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 