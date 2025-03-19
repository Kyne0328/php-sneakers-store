<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container-fluid">
    <div class="row">
        <?php require_once __DIR__ . '/../partials/admin_sidebar.php'; ?>

        <!-- Main content -->
        <main class="col-md-9 col-lg-10 px-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-4 pb-3 mb-3 border-bottom">
                <h1 class="h2">Products</h1>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-lg"></i> Add New Product
                </button>
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
                    <form action="/php-sneakers-store/public/admin/products" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                            <button class="btn btn-outline-primary" type="submit">
                                <i class="bi bi-search"></i>
                            </button>
                            <?php if (isset($_GET['search'])): ?>
                                <a href="/php-sneakers-store/public/admin/products" class="btn btn-outline-secondary">
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
                                    <th>ID</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Total Stock</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product['id']); ?></td>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        </td>
                                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                                        <td>₱<?php echo number_format($product['price'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($product['stock']); ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-product" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editProductModal"
                                                    data-id="<?php echo $product['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                                    data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                                    data-price="<?php echo $product['price']; ?>"
                                                    data-image="<?php echo htmlspecialchars($product['image']); ?>">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-info manage-sizes" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#manageSizesModal"
                                                    data-id="<?php echo $product['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                <i class="bi bi-rulers"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger delete-product" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#deleteProductModal"
                                                    data-id="<?php echo $product['id']; ?>"
                                                    data-name="<?php echo htmlspecialchars($product['name']); ?>">
                                                <i class="bi bi-trash"></i>
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

<!-- Delete Product Modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="delete_product_error" class="alert alert-danger d-none mb-3">
                    This product cannot be deleted because it has existing orders. To maintain order history integrity, products with orders cannot be deleted.
                </div>
                <p>Are you sure you want to delete "<span id="delete_product_name"></span>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="/php-sneakers-store/public/admin/products/delete" method="POST" id="deleteProductForm">
                    <input type="hidden" name="id" id="delete_product_id">
                    <button type="submit" class="btn btn-danger">Delete Product</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Product Modal -->
<div class="modal fade" id="editProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/php-sneakers-store/public/admin/products/update" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit_product_id">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="edit_price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                        <div class="form-text">Leave empty to keep current image. Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                        <div class="mt-2">
                            <label class="form-label">Current Image:</label>
                            <img id="edit_current_image" src="" alt="Current product image" class="img-thumbnail" style="max-width: 150px;">
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="bi bi-info-circle"></i> Total stock is automatically calculated based on the combined stock of all sizes. Use the size management tool to manage stock levels.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="/php-sneakers-store/public/admin/products/create" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Product Image</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                        <div class="form-text">Supported formats: JPG, PNG, GIF. Max size: 5MB</div>
                    </div>
                    <div class="alert alert-info">
                        <small><i class="bi bi-info-circle"></i> After adding the product, you can manage sizes and stock levels using the size management tool.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit product button clicks
    document.querySelectorAll('.edit-product').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;
            const price = this.dataset.price;
            const image = this.dataset.image;

            // Populate the edit modal with product data
            document.getElementById('edit_product_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_current_image').src = image;
        });
    });

    // Handle delete product button clicks
    document.querySelectorAll('.delete-product').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;

            // Reset error message
            document.getElementById('delete_product_error').classList.add('d-none');

            // Populate the delete modal with product data
            document.getElementById('delete_product_id').value = id;
            document.getElementById('delete_product_name').textContent = name;
        });
    });

    // Handle delete product form submission
    document.getElementById('deleteProductForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const response = await fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            });
            
            const result = await response.json();
            
            if (result.error) {
                document.getElementById('delete_product_error').classList.remove('d-none');
            } else {
                window.location.reload();
            }
        } catch (error) {
            document.getElementById('delete_product_error').classList.remove('d-none');
        }
    });
    
    // --- Size Management ---
    // References
    const sizesModal = document.getElementById('manageSizesModal');
    const sizesError = document.getElementById('sizes_error');
    const sizesSuccess = document.getElementById('sizes_success');
    const sizesTableBody = document.getElementById('sizes_table_body');
    const sizeAddForm = document.getElementById('add_size_form');
    const addSizeBtn = document.getElementById('add_new_size_btn');
    const cancelAddSizeBtn = document.getElementById('cancel_add_size');
    const addSizeProductIdInput = document.getElementById('add_size_product_id');
    const editSizeIdInput = document.getElementById('edit_size_id');
    const editSizeNameInput = document.getElementById('edit_size_name');
    const editSizeStockInput = document.getElementById('edit_size_stock');
    const deleteSizeIdInput = document.getElementById('delete_size_id');
    const deleteSizeNameSpan = document.getElementById('delete_size_name');
    
    let currentProductId = null;
    
    // Show/hide size form
    addSizeBtn.addEventListener('click', function() {
        sizeAddForm.classList.remove('d-none');
        addSizeBtn.classList.add('d-none');
    });
    
    cancelAddSizeBtn.addEventListener('click', function() {
        sizeAddForm.classList.add('d-none');
        addSizeBtn.classList.remove('d-none');
    });
    
    // Handle size management button clicks
    document.querySelectorAll('.manage-sizes').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const name = this.dataset.name;
            
            // Reset form and messages
            sizesError.classList.add('d-none');
            sizesSuccess.classList.add('d-none');
            sizeAddForm.classList.add('d-none');
            addSizeBtn.classList.remove('d-none');
            document.getElementById('sizeAddForm').reset();
            
            // Set product info
            document.getElementById('size_product_name').textContent = name;
            addSizeProductIdInput.value = id;
            currentProductId = id;
            
            // Load sizes
            loadSizes(id);
        });
    });
    
    // Load sizes for a product
    async function loadSizes(productId) {
        try {
            sizesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Loading sizes...</td></tr>';
            
            const response = await fetch(`/php-sneakers-store/public/admin/products/${productId}/sizes`);
            const data = await response.json();
            
            if (data.error) {
                sizesError.textContent = data.message;
                sizesError.classList.remove('d-none');
                sizesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Failed to load sizes</td></tr>';
                return;
            }
            
            // Render sizes
            if (data.sizes.length === 0) {
                sizesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">No sizes added yet</td></tr>';
            } else {
                sizesTableBody.innerHTML = '';
                
                data.sizes.forEach(size => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${size.size}</td>
                        <td>${size.stock}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary edit-size" 
                                    data-id="${size.id}"
                                    data-size="${size.size}"
                                    data-stock="${size.stock}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-size" 
                                    data-id="${size.id}"
                                    data-size="${size.size}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    `;
                    sizesTableBody.appendChild(tr);
                });
                
                // Add event listeners to edit and delete buttons
                sizesTableBody.querySelectorAll('.edit-size').forEach(btn => {
                    btn.addEventListener('click', function() {
                        editSizeIdInput.value = this.dataset.id;
                        editSizeNameInput.value = this.dataset.size;
                        editSizeStockInput.value = this.dataset.stock;
                        
                        new bootstrap.Modal(document.getElementById('editSizeModal')).show();
                    });
                });
                
                sizesTableBody.querySelectorAll('.delete-size').forEach(btn => {
                    btn.addEventListener('click', function() {
                        deleteSizeIdInput.value = this.dataset.id;
                        deleteSizeNameSpan.textContent = this.dataset.size;
                        
                        new bootstrap.Modal(document.getElementById('deleteSizeModal')).show();
                    });
                });
            }
        } catch (error) {
            console.error("Error loading sizes:", error);
            sizesTableBody.innerHTML = '<tr><td colspan="3" class="text-center">Failed to load sizes</td></tr>';
        }
    }
    
    // Add Size Form Submission
    document.getElementById('sizeAddForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('/php-sneakers-store/public/admin/sizes/add', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.error) {
                sizesError.textContent = result.message;
                sizesError.classList.remove('d-none');
                sizesSuccess.classList.add('d-none');
            } else {
                sizesSuccess.textContent = result.message;
                sizesSuccess.classList.remove('d-none');
                sizesError.classList.add('d-none');
                
                // Reset form and hide it
                this.reset();
                sizeAddForm.classList.add('d-none');
                addSizeBtn.classList.remove('d-none');
                
                // Reload sizes
                loadSizes(currentProductId);
            }
        } catch (error) {
            console.error("Error adding size:", error);
            sizesError.textContent = "An error occurred while adding the size";
            sizesError.classList.remove('d-none');
            sizesSuccess.classList.add('d-none');
        }
    });
    
    // Edit Size Form Submission
    document.getElementById('sizeEditForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('/php-sneakers-store/public/admin/sizes/update', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.error) {
                sizesError.textContent = result.message;
                sizesError.classList.remove('d-none');
                sizesSuccess.classList.add('d-none');
            } else {
                sizesSuccess.textContent = result.message;
                sizesSuccess.classList.remove('d-none');
                sizesError.classList.add('d-none');
                
                // Hide modal
                const editSizeModal = bootstrap.Modal.getInstance(document.getElementById('editSizeModal'));
                editSizeModal.hide();
                
                // Reload sizes
                loadSizes(currentProductId);
            }
        } catch (error) {
            console.error("Error updating size:", error);
            sizesError.textContent = "An error occurred while updating the size";
            sizesError.classList.remove('d-none');
            sizesSuccess.classList.add('d-none');
        }
    });
    
    // Delete Size Form Submission
    document.getElementById('sizeDeleteForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            const response = await fetch('/php-sneakers-store/public/admin/sizes/delete', {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.error) {
                sizesError.textContent = result.message;
                sizesError.classList.remove('d-none');
                sizesSuccess.classList.add('d-none');
            } else {
                sizesSuccess.textContent = result.message;
                sizesSuccess.classList.remove('d-none');
                sizesError.classList.add('d-none');
                
                // Hide modal
                const deleteSizeModal = bootstrap.Modal.getInstance(document.getElementById('deleteSizeModal'));
                deleteSizeModal.hide();
                
                // Reload sizes
                loadSizes(currentProductId);
            }
        } catch (error) {
            console.error("Error deleting size:", error);
            sizesError.textContent = "An error occurred while deleting the size";
            sizesError.classList.remove('d-none');
            sizesSuccess.classList.add('d-none');
        }
    });
});
</script>

<!-- Add custom CSS for admin layout -->
<style>
.sidebar {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 100;
    padding: 48px 0 0;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    font-weight: 500;
    color: #333;
    padding: 0.5rem 1rem;
    margin-bottom: 0.2rem;
}

.sidebar .nav-link.active {
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
}

.sidebar .nav-link:hover {
    background: rgba(255, 255, 255, 0.1);
}

main {
    padding-top: 48px;
}

@media (max-width: 767.98px) {
    .sidebar {
        position: static;
        padding-top: 0;
    }

    main {
        padding-top: 0;
    }
}

/* Fix modal flickering */
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

<!-- Manage Sizes Modal -->
<div class="modal fade" id="manageSizesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Sizes: <span id="size_product_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="sizes_error" class="alert alert-danger d-none"></div>
                <div id="sizes_success" class="alert alert-success d-none"></div>
                
                <div class="d-flex justify-content-between mb-3">
                    <h6>Available Sizes</h6>
                    <button type="button" class="btn btn-sm btn-primary" id="add_new_size_btn">
                        <i class="bi bi-plus-lg"></i> Add New Size
                    </button>
                </div>
                
                <div id="add_size_form" class="card mb-3 d-none">
                    <div class="card-body">
                        <h6 class="card-title">Add New Size</h6>
                        <form id="sizeAddForm">
                            <input type="hidden" id="add_size_product_id" name="product_id">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="add_size_name" class="form-label">Size (e.g. "US 8")</label>
                                    <input type="text" class="form-control" id="add_size_name" name="size" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="add_size_stock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="add_size_stock" name="stock" min="0" required>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary me-2" id="cancel_add_size">Cancel</button>
                                <button type="submit" class="btn btn-primary">Add Size</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="sizes_table">
                        <thead>
                            <tr>
                                <th>Size</th>
                                <th>Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sizes_table_body">
                            <tr>
                                <td colspan="3" class="text-center">Loading sizes...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Note: The product's total stock is automatically calculated from all size variants.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Size Modal -->
<div class="modal fade" id="editSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sizeEditForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_size_id" name="size_id">
                    <div class="mb-3">
                        <label for="edit_size_name" class="form-label">Size</label>
                        <input type="text" class="form-control" id="edit_size_name" name="size" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_size_stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="edit_size_stock" name="stock" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Size</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Size Modal -->
<div class="modal fade" id="deleteSizeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Delete Size</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete the size "<span id="delete_size_name"></span>"?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="sizeDeleteForm">
                    <input type="hidden" id="delete_size_id" name="size_id">
                    <button type="submit" class="btn btn-danger">Delete Size</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?> 