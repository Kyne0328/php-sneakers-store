<?php

namespace App\Controllers;

use App\Models\User;
use App\Config\Database;

class AuthController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();
        
        // Initialize CSRF token if not exists
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }

    private function validateCSRF() {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $_SESSION['error'] = 'Invalid request';
            header('Location: /php-sneakers-store/public/login');
            exit;
        }
    }

    private function validatePassword($password) {
        // Password must be at least 8 characters long and contain at least one uppercase letter,
        // one lowercase letter, one number, and one special character
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number = preg_match('@[0-9]@', $password);
        $special = preg_match('@[^\w]@', $password);

        return strlen($password) >= 8 && $uppercase && $lowercase && $number && $special;
    }

    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    public function loginForm() {
        require_once __DIR__ . '/../Views/auth/login.php';
    }

    public function login() {
        $this->validateCSRF();
        
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /php-sneakers-store/public/login');
            exit;
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $this->sanitizeInput($user['name']);
            $_SESSION['is_admin'] = isset($user['is_admin']) ? (int)$user['is_admin'] : 0;
            
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            
            // Check if there's a redirect URL stored in session
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect_url = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect_url);
                exit;
            }
            
            // Default redirect based on user role
            header('Location: ' . ($_SESSION['is_admin'] ? '/php-sneakers-store/public/admin' : '/php-sneakers-store/public/'));
            exit;
        }

        $_SESSION['error'] = 'Invalid email or password';
        header('Location: /php-sneakers-store/public/login');
        exit;
    }

    public function registerForm() {
        require_once __DIR__ . '/../Views/auth/register.php';
    }

    public function register() {
        $this->validateCSRF();
        
        $name = $this->sanitizeInput($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'All fields are required';
            header('Location: /php-sneakers-store/public/register');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['error'] = 'Invalid email format';
            header('Location: /php-sneakers-store/public/register');
            exit;
        }

        if ($password !== $confirm_password) {
            $_SESSION['error'] = 'Passwords do not match';
            header('Location: /php-sneakers-store/public/register');
            exit;
        }

        if (!$this->validatePassword($password)) {
            $_SESSION['error'] = 'Password must be at least 8 characters long and contain at least one uppercase letter, one lowercase letter, one number, and one special character';
            header('Location: /php-sneakers-store/public/register');
            exit;
        }

        // Check if email already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $_SESSION['error'] = 'Email already exists';
            header('Location: /php-sneakers-store/public/register');
            exit;
        }

        // Create user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 0)");
        
        if ($stmt->execute([$name, $email, $hashedPassword])) {
            $_SESSION['success'] = 'Registration successful! Please login.';
            header('Location: /php-sneakers-store/public/login');
            exit;
        }

        $_SESSION['error'] = 'Registration failed';
        header('Location: /php-sneakers-store/public/register');
        exit;
    }

    public function logout() {
        // Clear all session data
        $_SESSION = array();

        // Destroy the session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }

        // Destroy the session
        session_destroy();
        header('Location: /php-sneakers-store/public/');
        exit;
    }
} 