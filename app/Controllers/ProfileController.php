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
} 