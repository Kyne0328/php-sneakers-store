<?php

namespace App\Controllers;

use App\Config\Database;
use Exception;

class CheckoutController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();

        // Redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['error'] = 'Please login to checkout';
            header('Location: /php-sneakers-store/public/login');
            exit;
        }
    }

    public function index() {
        $cart_items = [];
        $total = 0;

        // Get cart items from database
        $stmt = $this->db->prepare("
            SELECT c.*, p.name, p.price, p.image, p.stock
            FROM cart c
            JOIN products p ON c.product_id = p.id
            WHERE c.user_id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $items = $stmt->fetchAll();

        // If no items in database cart, check session cart
        if (empty($items) && !empty($_SESSION['cart'])) {
            $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
            $stmt->execute(array_keys($_SESSION['cart']));
            $products = $stmt->fetchAll();

            foreach ($products as $product) {
                if (isset($_SESSION['cart'][$product['id']])) {
                    $quantity = $_SESSION['cart'][$product['id']];
                    $subtotal = $product['price'] * $quantity;
                    $total += $subtotal;

                    $cart_items[] = [
                        'id' => $product['id'],
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $quantity,
                        'subtotal' => $subtotal,
                        'image' => $product['image'],
                        'stock' => $product['stock']
                    ];
                }
            }
        } else {
            foreach ($items as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;

                $cart_items[] = [
                    'id' => $item['product_id'],
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $subtotal,
                    'image' => $item['image'],
                    'stock' => $item['stock']
                ];
            }
        }

        if (empty($cart_items)) {
            $_SESSION['error'] = 'Your cart is empty';
            header('Location: /php-sneakers-store/public/cart');
            exit;
        }

        // Get user's saved addresses
        $stmt = $this->db->prepare("
            SELECT * FROM addresses 
            WHERE user_id = ? 
            ORDER BY is_default DESC, created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $addresses = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/checkout/index.php';
    }

    public function process() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/checkout');
            exit;
        }

        // Validate form data
        $required_fields = ['first_name', 'last_name', 'address', 'city', 'state', 'zip', 'phone', 'card_number', 'expiry', 'cvv'];
        foreach ($required_fields as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['error'] = 'All fields are required';
                header('Location: /php-sneakers-store/public/checkout');
                exit;
            }
        }

        try {
            $this->db->beginTransaction();

            // Create or update address
            $stmt = $this->db->prepare("
                INSERT INTO addresses (user_id, street_address, city, state, postal_code, country, phone, is_default)
                VALUES (?, ?, ?, ?, ?, 'USA', ?, TRUE)
                ON DUPLICATE KEY UPDATE
                    street_address = VALUES(street_address),
                    city = VALUES(city),
                    state = VALUES(state),
                    postal_code = VALUES(postal_code),
                    phone = VALUES(phone),
                    is_default = TRUE
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['address'],
                $_POST['city'],
                $_POST['state'],
                $_POST['zip'],
                $_POST['phone']
            ]);
            $address_id = $this->db->lastInsertId() ?: $this->db->query("SELECT id FROM addresses WHERE user_id = {$_SESSION['user_id']} AND street_address = '{$_POST['address']}'")->fetchColumn();

            // Get cart items from database first
            $cart_items = $this->db->prepare("
                SELECT c.*, p.name, p.price, p.stock, s.size, s.stock as size_stock
                FROM cart c
                JOIN products p ON c.product_id = p.id
                JOIN sizes s ON c.size_id = s.id
                WHERE c.user_id = ?
            ");
            $cart_items->execute([$_SESSION['user_id']]);
            $items = $cart_items->fetchAll();

            // If no items in database cart, use session cart
            if (empty($items) && !empty($_SESSION['cart'])) {
                // Session cart now uses product_id_size_id keys
                $product_ids = [];
                $size_ids = [];
                
                foreach ($_SESSION['cart'] as $key => $quantity) {
                    list($product_id, $size_id) = explode('_', $key);
                    $product_ids[] = $product_id;
                    $size_ids[] = $size_id;
                }
                
                if (!empty($product_ids)) {
                    $product_placeholders = str_repeat('?,', count($product_ids) - 1) . '?';
                    $stmt = $this->db->prepare("SELECT * FROM products WHERE id IN ($product_placeholders)");
                    $stmt->execute($product_ids);
                    $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                    
                    $size_placeholders = str_repeat('?,', count($size_ids) - 1) . '?';
                    $size_stmt = $this->db->prepare("SELECT * FROM sizes WHERE id IN ($size_placeholders)");
                    $size_stmt->execute($size_ids);
                    $sizes = [];
                    
                    foreach ($size_stmt->fetchAll() as $size) {
                        $sizes[$size['id']] = $size;
                    }
                    
                    $items = [];
                    foreach ($_SESSION['cart'] as $key => $quantity) {
                        list($product_id, $size_id) = explode('_', $key);
                        
                        // Find the product
                        foreach ($products as $product) {
                            if ($product['id'] == $product_id && isset($sizes[$size_id])) {
                                $items[] = [
                                    'product_id' => $product['id'],
                                    'size_id' => $size_id,
                                    'name' => $product['name'],
                                    'price' => $product['price'],
                                    'quantity' => $quantity,
                                    'stock' => $product['stock'],
                                    'size' => $sizes[$size_id]['size'],
                                    'size_stock' => $sizes[$size_id]['stock']
                                ];
                                break;
                            }
                        }
                    }
                }
            }

            if (empty($items)) {
                throw new Exception('Your cart is empty');
            }

            // Calculate total and shipping
            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $shipping_fee = $subtotal >= 100 ? 0 : 10;
            $total = $subtotal + $shipping_fee;

            // Create order
            $create_order = $this->db->prepare("
                INSERT INTO orders (user_id, address_id, total_amount, shipping_fee, status, payment_status)
                VALUES (?, ?, ?, ?, 'pending', 'pending')
            ");
            $create_order->execute([
                $_SESSION['user_id'],
                $address_id,
                $total,
                $shipping_fee
            ]);
            $order_id = $this->db->lastInsertId();

            // Create order items and update product stock
            foreach ($items as $item) {
                // Check if enough stock in the specific size
                if ($item['size_stock'] < $item['quantity']) {
                    throw new Exception("Not enough stock for {$item['name']} in size {$item['size']}");
                }

                // Create order item
                $create_order_item = $this->db->prepare("
                    INSERT INTO order_items (order_id, product_id, size_id, quantity, price)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $create_order_item->execute([
                    $order_id,
                    $item['product_id'],
                    $item['size_id'],
                    $item['quantity'],
                    $item['price']
                ]);

                // Update product stock
                $update_stock = $this->db->prepare("
                    UPDATE products
                    SET stock = stock - ?
                    WHERE id = ?
                ");
                $update_stock->execute([$item['quantity'], $item['product_id']]);
                
                // Update size stock
                $update_size_stock = $this->db->prepare("
                    UPDATE sizes
                    SET stock = stock - ?
                    WHERE id = ?
                ");
                $update_size_stock->execute([$item['quantity'], $item['size_id']]);
            }

            // Clear cart
            $clear_cart = $this->db->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_cart->execute([$_SESSION['user_id']]);
            $_SESSION['cart'] = [];

            $this->db->commit();

            $_SESSION['success'] = 'Order placed successfully!';
            header('Location: /php-sneakers-store/public/profile');
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
            header('Location: /php-sneakers-store/public/checkout');
            exit;
        }
    }
} 