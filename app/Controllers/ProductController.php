<?php

namespace App\Controllers;

use App\Config\Database;
use PDO;
use PDOException;

class ProductController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();
    }

    public function index() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $query = "SELECT * FROM products WHERE stock > 0";
            
            if (!empty($search)) {
                $search = "%{$search}%";
                $query .= " AND (name LIKE ? OR description LIKE ?)";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$search, $search]);
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }
            
            $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require_once __DIR__ . '/../Views/products/index.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching products: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/');
            exit;
        }
    }

    public function show($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $product = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$product) {
                $_SESSION['error'] = "Product not found.";
                header('Location: /php-sneakers-store/public/products');
                exit;
            }

            require_once __DIR__ . '/../Views/products/show.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching product: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/products');
            exit;
        }
    }
} 