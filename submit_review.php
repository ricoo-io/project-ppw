<?php
session_start();
require_once('dbcontroller.php');
$db = new dbcontroller();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['iduser'];
    $productId = $_POST['productId'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $size = $_POST['size'];
    $color = $_POST['color'];

    $success = $db->addReview($userId, $productId, $rating, $review, $size, $color);

    if ($success) {
        
        $sql = "UPDATE t_orderdetails SET f_reviewed = 'yes'
                WHERE f_idbarang = $productId AND f_ukuran = '$size' AND f_warna = '$color'";
        $db->runSQL($sql);
        
        header("Location: orders.php?success=1");
    } else {
        header("Location: orders.php?error=1");
    }
    exit;
}

header("Location: orders.php");
exit;
?>
