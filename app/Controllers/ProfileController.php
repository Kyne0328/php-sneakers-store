<?php

namespace App\Controllers;

use App\Config\Database;

class ProfileController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();

        // Redirect to login if not authenticated
        if (!isset($_SESSION['user_id'])) {
            header('Location: /php-sneakers-store/public/login');
            exit;
        }
    }

    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        // Get user's orders
        $stmt = $this->db->prepare("
            SELECT o.*, COUNT(oi.id) as total_items 
            FROM orders o 
            LEFT JOIN order_items oi ON o.id = oi.order_id 
            WHERE o.user_id = ? 
            GROUP BY o.id 
            ORDER BY o.created_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $orders = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/profile/index.php';
    }

    public function update() {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $current_password = $_POST['current_password'] ?? '';
        $new_password = $_POST['new_password'] ?? '';

        // Get current user data
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();

        // Validate current password if trying to change password
        if ($new_password && !password_verify($current_password, $user['password'])) {
            $_SESSION['error'] = 'Current password is incorrect';
            header('Location: /php-sneakers-store/public/profile');
            exit;
        }

        // Check if email is already taken by another user
        if ($email !== $user['email']) {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $_SESSION['error'] = 'Email is already taken';
                header('Location: /php-sneakers-store/public/profile');
                exit;
            }
        }

        // Update user data
        if ($new_password) {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ?");
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt->execute([$name, $email, $hashedPassword, $_SESSION['user_id']]);
        } else {
            $stmt = $this->db->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
            $stmt->execute([$name, $email, $_SESSION['user_id']]);
        }

        $_SESSION['user_name'] = $name;
        $_SESSION['success'] = 'Profile updated successfully';
        header('Location: /php-sneakers-store/public/profile');
        exit;
    }

    public function orderDetails($id) {
        // Validate order ID
        if (!is_numeric($id) || $id <= 0) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(400);
                echo json_encode(['error' => 'Invalid order ID']);
                exit;
            }
            $_SESSION['error'] = 'Invalid order ID';
            header('Location: /php-sneakers-store/public/profile');
            exit;
        }

        // Get order details
        $stmt = $this->db->prepare("
            SELECT o.*, a.street_address, a.city, a.state, a.postal_code, a.country, a.phone,
                   u.name as customer_name, u.email as customer_email
            FROM orders o
            JOIN addresses a ON o.address_id = a.id
            JOIN users u ON o.user_id = u.id
            WHERE o.id = ? AND o.user_id = ?
        ");
        $stmt->execute([$id, $_SESSION['user_id']]);
        $order = $stmt->fetch();

        if (!$order) {
            if ($this->isAjaxRequest()) {
                header('Content-Type: application/json');
                http_response_code(404);
                echo json_encode(['error' => 'Order not found']);
                exit;
            }
            $_SESSION['error'] = 'Order not found';
            header('Location: /php-sneakers-store/public/profile');
            exit;
        }

        // Get order items with size information
        $stmt = $this->db->prepare("
            SELECT oi.*, p.name, p.image, s.size
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            LEFT JOIN sizes s ON oi.size_id = s.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$id]);
        $order_items = $stmt->fetchAll();

        if ($this->isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode([
                'id' => $order['id'],
                'created_at' => $order['created_at'],
                'status' => $order['status'],
                'total_amount' => $order['total_amount'],
                'street_address' => $order['street_address'],
                'city' => $order['city'],
                'state' => $order['state'],
                'postal_code' => $order['postal_code'],
                'items' => $order_items
            ]);
            exit;
        }

        require_once __DIR__ . '/../Views/profile/order_details.php';
    }

    private function isAjaxRequest() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
} 