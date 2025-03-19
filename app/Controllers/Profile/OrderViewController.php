<?php

namespace App\Controllers\Profile;

class OrderViewController {
    private $configFile;

    public function __construct() {
        $this->configFile = __DIR__ . '/../../Views/profile/orders.xml';
    }

    public function getOrderDetails($orderId) {
        // Load XML configuration
        $config = simplexml_load_file($this->configFile);
        
        // Get order data from database
        $order = $this->getOrderFromDatabase($orderId);
        
        if (!$order) {
            header('HTTP/1.1 404 Not Found');
            echo json_encode(['error' => 'Order not found']);
            return;
        }
        
        // Format order data according to XML configuration
        $response = [
            'id' => $order['id'],
            'created_at' => date('M d, Y H:i', strtotime($order['created_at'])),
            'status' => [
                'text' => ucfirst($order['status']),
                'color' => $this->getStatusColor($order['status'], $config)
            ],
            'shipping_address' => [
                'street' => $order['street_address'],
                'city' => $order['city'],
                'state' => $order['state'],
                'postal_code' => $order['postal_code']
            ],
            'items' => array_map(function($item) {
                return [
                    'name' => $item['name'],
                    'image' => $item['image'],
                    'price' => number_format($item['price'], 2),
                    'quantity' => $item['quantity'],
                    'subtotal' => number_format($item['price'] * $item['quantity'], 2)
                ];
            }, $this->getOrderItems($orderId))
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
    
    private function getStatusColor($status, $config) {
        foreach ($config->view->statusColors->color as $color) {
            if ((string)$color['status'] === $status) {
                return (string)$color;
            }
        }
        return 'secondary';
    }
    
    private function getOrderFromDatabase($orderId) {
        // Implement database query to get order details
        // This is a placeholder - implement actual database query
        return [
            'id' => $orderId,
            'created_at' => date('Y-m-d H:i:s'),
            'status' => 'pending',
            'street_address' => '123 Main St',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => '12345'
        ];
    }
    
    private function getOrderItems($orderId) {
        // Implement database query to get order items
        // This is a placeholder - implement actual database query
        return [
            [
                'name' => 'Sample Product',
                'image' => '/images/sample.jpg',
                'price' => 99.99,
                'quantity' => 1
            ]
        ];
    }
} 