<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;
use PDOException;

class AdminController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();

        // Check if user is admin
        if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
            $_SESSION['error'] = 'Access denied. Admin privileges required.';
            header('Location: /php-sneakers-store/public/');
            exit;
        }
    }

    public function index() {
        // Get dashboard statistics
        $stats = [
            'total_orders' => $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
            'total_revenue' => $this->db->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders")->fetchColumn(),
            'total_products' => $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
            'total_users' => $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'low_stock' => $this->db->query("SELECT COUNT(*) FROM products WHERE stock < 10")->fetchColumn()
        ];

        // Get recent orders
        $recent_orders = $this->db->query("
            SELECT o.*, u.name as user_name, a.street_address, a.city, a.state, a.postal_code
            FROM orders o
            JOIN users u ON o.user_id = u.id
            JOIN addresses a ON o.address_id = a.id
            ORDER BY o.created_at DESC
            LIMIT 5
        ")->fetchAll();

        // Get top selling products
        $top_products = $this->db->query("
            SELECT p.*, COUNT(oi.id) as total_sold, SUM(oi.quantity) as total_quantity
            FROM products p
            LEFT JOIN order_items oi ON p.id = oi.product_id
            GROUP BY p.id
            ORDER BY total_quantity DESC
            LIMIT 5
        ")->fetchAll();

        require_once __DIR__ . '/../Views/admin/dashboard.php';
    }

    public function products() {
        // Get all products
        $products = $this->db->query("
            SELECT * FROM products 
            ORDER BY created_at DESC
        ")->fetchAll();

        require_once __DIR__ . '/../Views/admin/products.php';
    }

    public function orders() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            
            // Prepare array to hold all order data
            $allOrders = [];
            
            // Fetch orders based on search criteria or get all orders
            if (!empty($search)) {
                // Search case
                if (is_numeric($search)) {
                    // Search by ID
                    $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ?");
                    $stmt->execute([$search]);
                } else {
                    // Need to join with users for name search
                    $query = "
                        SELECT o.*
                        FROM orders o
                        JOIN users u ON o.user_id = u.id
                        WHERE (u.name LIKE ? 
                          OR LOWER(o.status) LIKE LOWER(?) 
                          OR LOWER(o.payment_status) LIKE LOWER(?))
                    ";
                    $stmt = $this->db->prepare($query);
                    $searchTerm = "%{$search}%";
                    $params = [$searchTerm, $searchTerm, $searchTerm];
                    
                    // Log the query for debugging
                    error_log("Search query: " . $query);
                    error_log("Search parameters: " . implode(", ", $params));
                    
                    $stmt->execute($params);
                }
            } else {
                // No search - get all orders
                $stmt = $this->db->prepare("SELECT * FROM orders ORDER BY id DESC");
                $stmt->execute();
            }
            
            $allOrders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Sort by ID in descending order
            usort($allOrders, function($a, $b) {
                return $b['id'] - $a['id'];
            });
            
            // Get additional info for each order
            $orders = [];
            foreach ($allOrders as $order) {
                // Get user name
                $userStmt = $this->db->prepare("SELECT name, email FROM users WHERE id = ?");
                $userStmt->execute([$order['user_id']]);
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);
                
                $order['user_name'] = $user ? $user['name'] : 'Unknown User';
                $order['user_email'] = $user ? $user['email'] : 'No Email';
                
                // Get address
                $addressStmt = $this->db->prepare("SELECT street_address, city, state, postal_code FROM addresses WHERE id = ?");
                $addressStmt->execute([$order['address_id']]);
                $address = $addressStmt->fetch(PDO::FETCH_ASSOC);
                
                if ($address) {
                    $order['shipping_address'] = $address['street_address'];
                    $order['shipping_city'] = $address['city'];
                    $order['shipping_state'] = $address['state'];
                    $order['shipping_zip'] = $address['postal_code'];
                } else {
                    $order['shipping_address'] = 'No Address';
                    $order['shipping_city'] = '';
                    $order['shipping_state'] = '';
                    $order['shipping_zip'] = '';
                }
                
                // Get order items
                $itemsStmt = $this->db->prepare("
                    SELECT oi.*, p.name, p.image, p.price, s.size
                    FROM order_items oi
                    JOIN products p ON oi.product_id = p.id
                    LEFT JOIN sizes s ON oi.size_id = s.id
                    WHERE oi.order_id = ?
                ");
                $itemsStmt->execute([$order['id']]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                
                $order['items'] = $items ?: [];
                
                $orders[] = $order;
            }
            
            require_once __DIR__ . '/../Views/admin/orders.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching orders: " . $e->getMessage();
            error_log("Orders search error: " . $e->getMessage());
            header('Location: /php-sneakers-store/public/admin');
            exit;
        }
    }

    public function users() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $role = isset($_GET['role']) ? trim($_GET['role']) : '';
            
            // Base query - Add COALESCE to handle NULL is_admin values
            $query = "SELECT id, name, email, COALESCE(is_admin, 0) as is_admin, created_at, updated_at FROM users";
            $params = [];
            
            // Build WHERE clause conditions
            $conditions = [];
            
            if (!empty($search)) {
                $conditions[] = "(name LIKE ? OR email LIKE ?)";
                $params[] = "%{$search}%";
                $params[] = "%{$search}%";
            }
            
            if (!empty($role)) {
                $conditions[] = "COALESCE(is_admin, 0) = ?";
                $params[] = $role === 'admin' ? 1 : 0;
            }
            
            // Add WHERE clause if there are conditions
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
            
            // Add ORDER BY
            $query .= " ORDER BY created_at DESC";
            
            // Execute query
            $stmt = $this->db->prepare($query);
            if (!empty($params)) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require_once __DIR__ . '/../Views/admin/users.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching users: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/admin');
            exit;
        }
    }

    public function addProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/admin/products');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO products (name, description, price, image, stock)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['image'],
                0 // Initialize stock as 0, will be updated when sizes are added
            ]);

            $_SESSION['success'] = 'Product added successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to add product';
            error_log($e->getMessage());
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }

    public function updateProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/admin/products');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE products 
                SET name = ?, description = ?, price = ?, image = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['name'],
                $_POST['description'],
                $_POST['price'],
                $_POST['image'],
                $_POST['id']
            ]);

            $_SESSION['success'] = 'Product updated successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to update product';
            error_log($e->getMessage());
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }

    public function deleteProduct() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/admin/products');
            exit;
        }

        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$_POST['id']]);

            $_SESSION['success'] = 'Product deleted successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to delete product';
            error_log($e->getMessage());
        }

        header('Location: /php-sneakers-store/public/admin/products');
        exit;
    }

    public function updateOrderStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/admin/orders');
            exit;
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE orders 
                SET status = ?, payment_status = ?, tracking_number = ?
                WHERE id = ?
            ");
            $stmt->execute([
                $_POST['status'],
                $_POST['payment_status'],
                $_POST['tracking_number'] ?? null,
                $_POST['order_id']
            ]);

            $_SESSION['success'] = 'Order status updated successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to update order status';
            error_log($e->getMessage());
        }

        header('Location: /php-sneakers-store/public/admin/orders');
        exit;
    }

    public function toggleAdmin() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /php-sneakers-store/public/admin/users');
            exit;
        }

        try {
            // Use the provided is_admin value directly
            $user_id = $_POST['user_id'];
            $is_admin = isset($_POST['is_admin']) ? (int)$_POST['is_admin'] : 0;
            
            // Update user admin status
            $stmt = $this->db->prepare("UPDATE users SET is_admin = ? WHERE id = ?");
            $stmt->execute([$is_admin, $user_id]);

            $_SESSION['success'] = 'User admin status updated successfully';
        } catch (\PDOException $e) {
            $_SESSION['error'] = 'Failed to update user admin status';
            error_log($e->getMessage());
        }

        header('Location: /php-sneakers-store/public/admin/users');
        exit;
    }
} 