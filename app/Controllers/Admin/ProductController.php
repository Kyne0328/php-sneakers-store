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
            $required = ['name', 'description', 'price', 'stock'];
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
                INSERT INTO products (name, description, price, image, stock) 
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $imageUrl,
                $_POST['stock']
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
                SET name = ?, description = ?, price = ?, image = ?, stock = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $imageUrl,
                $_POST['stock'],
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

            // Delete product
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);

            $_SESSION['success'] = 'Product deleted successfully';
        } catch (\Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }
} 