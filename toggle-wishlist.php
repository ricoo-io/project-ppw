<?php
session_start();
require_once "dbcontroller.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $userId = $_SESSION['iduser'];
    $itemId = $data['itemId'];
    $action = $data['action'];

    $db = new dbcontroller();

    if ($action === 'add') {
        $result = $db->addToWishlist($userId, $itemId);
    } elseif ($action === 'remove') {
        $result = $db->removeFromWishlist($userId, $itemId);
    }
}

if ($result) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update wishlist']);
}

?>