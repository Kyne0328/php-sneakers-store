<?php

namespace App\Controllers;

use App\Config\Database;

class CartController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();
        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        
        // Initialize CSRF token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid request']);
            exit;
        }
    }

    private function validateProductId($product_id) {
        if (!is_numeric($product_id)) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        return $stmt->fetch() !== false;
    }

    private function validateQuantity($quantity) {
        return is_numeric($quantity) && $quantity > 0 && $quantity <= 10; // Limit to 10 items per product
    }

    private function syncCart() {
        if (isset($_SESSION['user_id'])) {
            // Move items from session cart to database
            foreach ($_SESSION['cart'] as $product_id => $quantity) {
                $stmt = $this->db->prepare("
                    INSERT INTO cart (user_id, product_id, quantity)
                    VALUES (?, ?, ?)
                    ON DUPLICATE KEY UPDATE quantity = quantity + ?
                ");
                $stmt->execute([$_SESSION['user_id'], $product_id, $quantity, $quantity]);
            }
            // Clear session cart after syncing
            $_SESSION['cart'] = [];
        }
    }

    public function index() {
        $cart_items = [];
        $total = 0;

        if (isset($_SESSION['user_id'])) {
            // Get cart items from database
            $stmt = $this->db->prepare("
                SELECT c.*, p.name, p.price, p.image
                FROM cart c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $items = $stmt->fetchAll();

            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                $cart_items[] = [
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'image' => $item['image']
                ];
            }
        } else {
            // Get cart items from session
            if (!empty($_SESSION['cart'])) {
                $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
                $stmt = $this->db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
                $stmt->execute(array_keys($_SESSION['cart']));
                $products = $stmt->fetchAll();

                foreach ($products as $product) {
                    $quantity = $_SESSION['cart'][$product['id']];
                    $subtotal = $product['price'] * $quantity;
                    $total += $subtotal;

                    $cart_items[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'image' => $product['image']
                    ];
                }
            }
        }

        require_once __DIR__ . '/../Views/cart/index.php';
    }

    public function add() {
        $this->validateCSRF();
        
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to add items to cart';
            $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
            header('Location: /php-sneakers-store/public/login');
            exit;
        }

        $product_id = filter_var($_POST['product_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_var($_POST['quantity'] ?? 1, FILTER_SANITIZE_NUMBER_INT);

        if (!$this->validateProductId($product_id)) {
            $_SESSION['error'] = 'Invalid product';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        if (!$this->validateQuantity($quantity)) {
            $_SESSION['error'] = 'Invalid quantity. Must be between 1 and 10';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Add to database cart
        try {
            $stmt = $this->db->prepare("
                INSERT INTO cart (user_id, product_id, quantity)
                VALUES (?, ?, ?)
                ON DUPLICATE KEY UPDATE quantity = LEAST(quantity + ?, 10)
            ");
            $stmt->execute([$_SESSION['user_id'], $product_id, $quantity, $quantity]);
            $_SESSION['success'] = 'Product added to cart successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to add product to cart';
            error_log($e->getMessage());
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }

    public function update() {
        $this->validateCSRF();
        
        $product_id = filter_var($_POST['product_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);
        $quantity = filter_var($_POST['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        if (!$this->validateProductId($product_id)) {
            $_SESSION['error'] = 'Invalid product';
            header('Location: /php-sneakers-store/public/cart');
            exit;
        }

        if (!$this->validateQuantity($quantity)) {
            $_SESSION['error'] = 'Invalid quantity. Must be between 1 and 10';
            header('Location: /php-sneakers-store/public/cart');
            exit;
        }

        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare("
                    UPDATE cart 
                    SET quantity = ? 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$quantity, $_SESSION['user_id'], $product_id]);
                $_SESSION['success'] = 'Cart updated successfully';
            } catch (\PDOException $e) {
                $_SESSION['error'] = 'Failed to update cart';
                error_log($e->getMessage());
            }
        } else {
            $_SESSION['cart'][$product_id] = $quantity;
            $_SESSION['success'] = 'Cart updated successfully';
        }

        header('Location: /php-sneakers-store/public/cart');
        exit;
    }

    public function remove() {
        $this->validateCSRF();
        
        $product_id = filter_var($_POST['product_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

        if (!$this->validateProductId($product_id)) {
            $_SESSION['error'] = 'Invalid product';
            header('Location: /php-sneakers-store/public/cart');
            exit;
        }

        if (isset($_SESSION['user_id'])) {
            try {
                $stmt = $this->db->prepare("
                    DELETE FROM cart 
                    WHERE user_id = ? AND product_id = ?
                ");
                $stmt->execute([$_SESSION['user_id'], $product_id]);
                $_SESSION['success'] = 'Product removed from cart';
            } catch (\PDOException $e) {
                $_SESSION['error'] = 'Failed to remove product from cart';
                error_log($e->getMessage());
            }
        } else {
            unset($_SESSION['cart'][$product_id]);
            $_SESSION['success'] = 'Product removed from cart';
        }

        header('Location: /php-sneakers-store/public/cart');
        exit;
    }
} 