<?php

namespace App\Controllers\Admin;

class SidebarController {
    private $configFile;

    public function __construct() {
        $this->configFile = __DIR__ . '/../../Views/admin/sidebar.xml';
    }

    public function toggleSidebar() {
        if (!isset($_SESSION['sidebar_collapsed'])) {
            $_SESSION['sidebar_collapsed'] = false;
        }
        
        $_SESSION['sidebar_collapsed'] = !$_SESSION['sidebar_collapsed'];
        
        header('Content-Type: application/json');
        echo json_encode(['collapsed' => $_SESSION['sidebar_collapsed']]);
    }

    public function getSidebarState() {
        header('Content-Type: application/json');
        echo json_encode([
            'collapsed' => $_SESSION['sidebar_collapsed'] ?? false
        ]);
    }

    public function handleModalState($action) {
        $response = [];
        
        switch ($action) {
            case 'show':
                $response = [
                    'overflow' => 'hidden',
                    'paddingRight' => ''
                ];
                break;
            case 'hide':
                $response = [
                    'overflow' => '',
                    'paddingRight' => ''
                ];
                break;
        }
        
        header('Content-Type: application/json');
        echo json_encode($response);
    }
} 