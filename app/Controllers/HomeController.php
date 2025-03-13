<?php

namespace App\Controllers;

use App\Config\Database;

class HomeController {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        session_start();
    }

    public function index() {
        // Get featured products (latest 6 products)
        $stmt = $this->db->query("
            SELECT * FROM products 
            WHERE stock > 0 
            ORDER BY id DESC 
            LIMIT 6
        ");
        $featured_products = $stmt->fetchAll();

        require_once __DIR__ . '/../Views/home/index.php';
    }
} 