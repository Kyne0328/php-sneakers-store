<?php

class UserController {
    public function index() {
        try {
            $search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $query = "SELECT * FROM users";
            
            if (!empty($search)) {
                $search = "%{$search}%";
                $query .= " WHERE name LIKE ? OR email LIKE ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$search, $search]);
            } else {
                $stmt = $this->db->prepare($query);
                $stmt->execute();
            }
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            require_once __DIR__ . '/../../Views/admin/users.php';
        } catch (PDOException $e) {
            $_SESSION['error'] = "Error fetching users: " . $e->getMessage();
            header('Location: /php-sneakers-store/public/admin');
            exit;
        }
    }
} 