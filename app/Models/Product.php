<?php

namespace App\Models;

use App\Config\Database;

class Product {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllProducts() {
        $stmt = $this->db->query("SELECT * FROM products ORDER BY created_at DESC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function searchProducts($query) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE name LIKE ? OR description LIKE ?");
        $searchTerm = "%$query%";
        $stmt->execute([$searchTerm, $searchTerm]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getProductSizes($product_id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM sizes WHERE product_id = ? ORDER BY size");
            $stmt->execute([$product_id]);
            $sizes = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Log results
            error_log("Product #$product_id: Found " . count($sizes) . " sizes (including out of stock)");
            
            // Filter out-of-stock sizes if needed
            $in_stock_sizes = array_filter($sizes, function($size) {
                return $size['stock'] > 0;
            });
            
            error_log("Product #$product_id: " . count($in_stock_sizes) . " sizes in stock");
            
            return $in_stock_sizes;
        } catch (\PDOException $e) {
            error_log("Error fetching sizes for product #$product_id: " . $e->getMessage());
            return [];
        }
    }

    public function getSizeById($size_id) {
        $stmt = $this->db->prepare("SELECT * FROM sizes WHERE id = ?");
        $stmt->execute([$size_id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
} 