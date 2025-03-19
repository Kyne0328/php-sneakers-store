<?php

namespace App\Controllers\Admin;

use App\Config\Database;
use PDO;
use PDOException;

class ProductController {
    private $db;
    private $uploadDir;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->uploadDir = __DIR__ . '/../../../public/uploads/products/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        session_start();

        // Check if user is admin
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Access denied. Admin privileges required.';
            header('Location: /php-sneakers-store/public/');
            exit;
        }
    }

    public function index() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $query = "SELECT * FROM products";
            
            if (!empty($search)) {
                $search = "%{$search}%";
                $query .= " WHERE name LIKE ? OR description LIKE ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$search, $search]);
            } else {
                $stmt = $this->db->query($query);
            }
            
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require_once __DIR__ . '/../../Views/admin/products.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching products: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/admin');
            exit;
        }
    }

    private function handleImageUpload($file) {
        try {
            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                throw new \Exception('Invalid file type. Only JPG, PNG and GIF are allowed.');
            }

            if ($file['size'] > $maxSize) {
                throw new \Exception('File is too large. Maximum size is 5MB.');
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $filepath = $this->uploadDir . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filepath)) {
                throw new \Exception('Failed to upload file.');
            }

            return '/php-sneakers-store/public/uploads/products/' . $filename;
        } catch (\Exception $e) {
            throw new \Exception('Image upload failed: ' . $e->getMessage());
        }
    }

    public function create() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            // Validate required fields
            $required = ['name', 'description', 'price'];
            foreach ($required as $field) {
                if (!isset($_POST[$field]) || empty($_POST[$field])) {
                    throw new \Exception("$field is required");
                }
            }

            // Handle image upload
            if (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
                throw new \Exception('Product image is required');
            }

            $imageUrl = $this->handleImageUpload($_FILES['image']);

            // Insert product
            $stmt = $this->db->prepare("
                INSERT INTO products (name, description, price, image) 
                VALUES (?, ?, ?, ?)
            ");

            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $imageUrl
            ]);

            $_SESSION['success'] = 'Product added successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }

    public function update() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }

            if (!isset($_POST['id'])) {
                throw new \Exception('Product ID is required');
            }

            // Get current product data
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                throw new \Exception('Product not found');
            }

            // Handle image upload if new image is provided
            $imageUrl = $product['image'];
            if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $imageUrl = $this->handleImageUpload($_FILES['image']);
                
                // Delete old image if it exists and is in our uploads directory
                if (strpos($product['image'], '/uploads/products/') !== false) {
                    $oldImagePath = __DIR__ . '/../../../public' . parse_url($product['image'], PHP_URL_PATH);
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
            }

            // Update product
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, image = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $imageUrl,
                $_POST['id']
            ]);

            $_SESSION['success'] = 'Product updated successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }

    public function delete() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
                throw new \Exception('Invalid request');
            }

            // Check if product has any orders
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM order_items 
                WHERE product_id = ?
            ");
            $stmt->execute([$_POST['id']]);
            $hasOrders = $stmt->fetchColumn() > 0;

            if ($hasOrders) {
                // Return JSON response for AJAX request
                header('Content-Type: application/json');
                echo json_encode(['error' => true, 'message' => 'Cannot delete product because it has existing orders']);
                exit;
            }

            // Get product image before deleting
            $stmt = $this->db->prepare("SELECT image FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($product && strpos($product['image'], '/uploads/products/') !== false) {
                // Delete image file if it exists in our uploads directory
                $imagePath = __DIR__ . '/../../../public' . parse_url($product['image'], PHP_URL_PATH);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            
            // First delete sizes for the product
            $stmt = $this->db->prepare("DELETE FROM sizes WHERE product_id = ?");
            $stmt->execute([$_POST['id']]);

            // Delete product
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);

            // Return JSON response for AJAX request
            header('Content-Type: application/json');
            echo json_encode(['error' => false, 'message' => 'Product deleted successfully']);
            exit;
        } catch (\Exception $e) {
            // Return JSON response for AJAX request
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    public function getSizes($productId) {
        try {
            // Validate product exists
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$product) {
                throw new \Exception('Product not found');
            }
            
            // Get sizes for the product
            $stmt = $this->db->prepare("SELECT * FROM sizes WHERE product_id = ? ORDER BY size ASC");
            $stmt->execute([$productId]);
            $sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode([
                'error' => false,
                'product' => $product,
                'sizes' => $sizes
            ]);
            exit;
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    public function addSize() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Validate required fields
            if (!isset($_POST['product_id']) || !isset($_POST['size']) || !isset($_POST['stock'])) {
                throw new \Exception('Product ID, size and stock are required');
            }
            
            $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
            $size = trim($_POST['size']);
            $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
            
            if (!$productId || $productId <= 0) {
                throw new \Exception('Invalid product ID');
            }
            
            if (empty($size)) {
                throw new \Exception('Size cannot be empty');
            }
            
            if ($stock === false || $stock < 0) {
                throw new \Exception('Stock must be a non-negative number');
            }
            
            // Check if product exists
            $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
            $stmt->execute([$productId]);
            if (!$stmt->fetch()) {
                throw new \Exception('Product not found');
            }
            
            // Check if size already exists for this product
            $stmt = $this->db->prepare("SELECT id FROM sizes WHERE product_id = ? AND size = ?");
            $stmt->execute([$productId, $size]);
            if ($stmt->fetch()) {
                throw new \Exception('This size already exists for this product');
            }
            
            // Insert the new size
            $stmt = $this->db->prepare("
                INSERT INTO sizes (product_id, size, stock)
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$productId, $size, $stock]);
            
            // Update total product stock
            $this->updateProductTotalStock($productId);
            
            header('Content-Type: application/json');
            echo json_encode([
                'error' => false,
                'message' => 'Size added successfully',
                'size_id' => $this->db->lastInsertId()
            ]);
            exit;
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    public function updateSize() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new \Exception('Invalid request method');
            }
            
            // Validate required fields
            if (!isset($_POST['size_id']) || !isset($_POST['size']) || !isset($_POST['stock'])) {
                throw new \Exception('Size ID, size name and stock are required');
            }
            
            $sizeId = filter_var($_POST['size_id'], FILTER_VALIDATE_INT);
            $size = trim($_POST['size']);
            $stock = filter_var($_POST['stock'], FILTER_VALIDATE_INT);
            
            if (!$sizeId || $sizeId <= 0) {
                throw new \Exception('Invalid size ID');
            }
            
            if (empty($size)) {
                throw new \Exception('Size cannot be empty');
            }
            
            if ($stock === false || $stock < 0) {
                throw new \Exception('Stock must be a non-negative number');
            }
            
            // Check if size exists
            $stmt = $this->db->prepare("SELECT product_id, size FROM sizes WHERE id = ?");
            $stmt->execute([$sizeId]);
            $currentSize = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$currentSize) {
                throw new \Exception('Size not found');
            }
            
            // Check if the new size would create a duplicate (if changing the size name)
            if ($currentSize['size'] !== $size) {
                $stmt = $this->db->prepare("
                    SELECT id FROM sizes 
                    WHERE product_id = ? AND size = ? AND id != ?
                ");
                $stmt->execute([$currentSize['product_id'], $size, $sizeId]);
                if ($stmt->fetch()) {
                    throw new \Exception('This size already exists for this product');
                }
            }
            
            // Update the size
            $stmt = $this->db->prepare("
                UPDATE sizes 
                SET size = ?, stock = ?
                WHERE id = ?
            ");
            $stmt->execute([$size, $stock, $sizeId]);
            
            // Update total product stock
            $this->updateProductTotalStock($currentSize['product_id']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'error' => false,
                'message' => 'Size updated successfully'
            ]);
            exit;
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    public function deleteSize() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['size_id'])) {
                throw new \Exception('Invalid request');
            }
            
            $sizeId = filter_var($_POST['size_id'], FILTER_VALIDATE_INT);
            
            if (!$sizeId || $sizeId <= 0) {
                throw new \Exception('Invalid size ID');
            }
            
            // Check if size exists and get the product ID
            $stmt = $this->db->prepare("SELECT product_id FROM sizes WHERE id = ?");
            $stmt->execute([$sizeId]);
            $size = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$size) {
                throw new \Exception('Size not found');
            }
            
            // Check if the size is used in any orders
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM order_items 
                WHERE size_id = ?
            ");
            $stmt->execute([$sizeId]);
            $hasOrders = $stmt->fetchColumn() > 0;
            
            if ($hasOrders) {
                throw new \Exception('Cannot delete this size because it is used in existing orders');
            }
            
            // Delete the size
            $stmt = $this->db->prepare("DELETE FROM sizes WHERE id = ?");
            $stmt->execute([$sizeId]);
            
            // Update total product stock
            $this->updateProductTotalStock($size['product_id']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'error' => false,
                'message' => 'Size deleted successfully'
            ]);
            exit;
            
        } catch (\Exception $e) {
            header('Content-Type: application/json');
            echo json_encode(['error' => true, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    private function updateProductTotalStock($productId) {
        // Calculate the sum of stock for all sizes of this product
        $stmt = $this->db->prepare("
            SELECT SUM(stock) as total_stock
            FROM sizes
            WHERE product_id = ?
        ");
        $stmt->execute([$productId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalStock = $result['total_stock'] ?? 0;
        
        // Update the product stock
        $stmt = $this->db->prepare("
            UPDATE products
            SET stock = ?
            WHERE id = ?
        ");
        $stmt->execute([$totalStock, $productId]);
    }
} 