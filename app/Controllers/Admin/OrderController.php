<?php

namespace App\Controllers\Admin;

use App\Config\Database;
use PDO;
use PDOException;

class OrderController {
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
        try {
            // First, get all orders with user and address information
            $query = "
                SELECT o.*, u.name as user_name, u.email as user_email,
                       a.street as shipping_address, a.city as shipping_city, 
                       a.state as shipping_state, a.zip as shipping_zip
                FROM orders o
                JOIN users u ON o.user_id = u.id
                JOIN addresses a ON o.address_id = a.id
                ORDER BY o.created_at DESC
            ";
            $stmt = $this->db->query($query);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Prepare the statement for getting order items once
            $itemsStmt = $this->db->prepare("
                SELECT oi.*, p.name, p.image, p.price
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");

            // Get order items for each order
            foreach ($orders as &$order) {
                // Initialize items array
                $order['items'] = [];
                
                // Get items for this order
                $itemsStmt->execute([$order['id']]);
                $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                
                if ($items) {
                    $order['items'] = $items;
                }
            }

            require_once __DIR__ . '/../../Views/admin/orders.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching orders: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/admin');
            exit;
        }
    }

    public function updateStatus() {
        try {
            if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
                throw new \Exception('Missing required fields');
            }

            $order_id = $_POST['order_id'];
            $status = $_POST['status'];

            // Validate status
            $valid_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
            if (!in_array($status, $valid_statuses)) {
                throw new \Exception('Invalid status');
            }

            // Begin transaction
            $this->db->beginTransaction();

            // Update order status
            $stmt = $this->db->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$status, $order_id]);

            // If order is cancelled, restore product stock
            if ($status === 'cancelled') {
                $stmt = $this->db->prepare("
                    SELECT product_id, quantity 
                    FROM order_items 
                    WHERE order_id = ?
                ");
                $stmt->execute([$order_id]);
                $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($items as $item) {
                    $stmt = $this->db->prepare("
                        UPDATE products 
                        SET stock = stock + ? 
                        WHERE id = ?
                    ");
                    $stmt->execute([$item['quantity'], $item['product_id']]);
                }
            }

            $this->db->commit();

            $_SESSION['success'] = "Order status updated successfully";
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
        }

        header('Location: /php-sneakers-store/public/admin/orders');
        exit;
    }
} 