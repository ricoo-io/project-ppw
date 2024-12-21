<?php
session_start();
require_once('dbcontroller.php');
$db = new dbcontroller();

$iduser = $_SESSION['iduser'];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$sql = "SELECT t_cart.f_id, t_barang.f_pakaian, t_barang.f_gambar, t_barang.f_harga, t_cart.f_quantity, t_cart.f_total_harga, t_cart.f_iduser, t_cart.f_idbarang
        FROM t_cart
        JOIN t_barang ON t_cart.f_idbarang = t_barang.f_id
        WHERE t_cart.f_iduser = $iduser";
$cartItems = $db->getALL($sql);

$totalPrice = 0;
$totalQuantity = 0;

function updateItemQuantity($cartId, $newQuantity) {
    global $db;
    $sql = "UPDATE t_cart SET f_quantity = $newQuantity, f_total_harga = f_quantity * (SELECT f_harga FROM t_barang WHERE f_id = f_idbarang) WHERE f_id = $cartId";
    $db->runSQL($sql);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update'])) {
        $cartId = $_POST['cart_id'];
        $newQuantity = $_POST['quantity'];
        updateItemQuantity($cartId, $newQuantity);
        header("Location: cart.php");  
    } elseif (isset($_POST['delete'])) {
        $cartId = $_POST['cart_id'];
        $sql = "DELETE FROM t_cart WHERE f_id = $cartId";
        $db->runSQL($sql);
        header("Location: cart.php");  
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
    $_SESSION['search'] = trim($_POST['search']);
    header('Location: search.php');
    exit;
}
?>


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="../public/images/logo2.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>KnowDays | Your Cart</title>
</head>
<body>
    <nav>
        <div class="img">
            <a href="index.php"><img src="public\images\logo_nobg.png" alt="" style="height: 50px;"></a>
            
        </div>

        
        <form method="POST"class="search-container">
            <input type="text" id="search" name="search" placeholder="Search.." >
            <button type="submit" name="submit_search"><img src="public\images\search.jpg" alt=""></button>
        </form>
        
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> User</a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
        
    </nav>
    
    <div class="container">
        <div class="title">
            <h1>Cart</h1>
        </div>

        <div class="container-cart">
            <div class="cart-items">
            
                <?php 
                if (empty($cartItems)) {
                ?>
                    <div class="empty-cart-message">
                        <h2 style="padding: 10px;">Your cart is empty!</h2>
                        <a href="index.php">Browse Our Products</a>
                    </div>
                <?php 
                } else {
                    foreach ($cartItems as $item) {
                        $totalQuantity += $item['f_quantity'];
                    ?>
                        <div class="cart-item">
                            <a href="produk.php?id=<?php echo urlencode($item['f_idbarang']);?>">
                                <img src="public/images/<?php echo htmlspecialchars($item['f_gambar']); ?>" alt="<?php echo htmlspecialchars($item['f_pakaian']); ?>">
                            </a>
                            <div class="item-details">
                                <h4><?= $item['f_pakaian'] ?></h4>
                                
                            </div>
                            <div class="cart-item-controls">
                                <p>Rp <?= number_format($item['f_harga'], 0, ',', '.') ?></p>
                                <form method="POST" action="cart.php">
                                    <input type="hidden" name="cart_id" value="<?= $item['f_id'] ?>">
                                    <button type="submit" name="delete" class="delete-btn">🗑</button>
                                   
                                    <input type="number" name="quantity" value="<?= $item['f_quantity'] ?>" min="1" style="width: 50px;">
                                    
                                    <button type="submit" name="update" class="update-btn">Update</button>
                                </form>
                            </div>
                        </div>
                    <?php } 
                } ?>
            </div>

            
                <div class="cart-summary">
                    <div class="summary-title">Summary</div>
                    <div class="summary-item">
                        <div><?= $totalQuantity ?> Items</div>
                        <?php if (!empty($cartItems)) { ?>
                            <?php foreach ($cartItems as $item) {
                                $totalPrice += $item['f_harga']*$item['f_quantity'];
                            }?>
                        <div>Total: Rp <?= number_format($totalPrice, 0, ',', '.') ?></div>
                        <?php } ?>
                    </div>
                    <div class="checkout-buttons">
                        <button class="btn">Check Out</button>
                        <a href="index.php"><button class="btn btn-secondary">Lanjut Belanja</button></a>
                    </div>
                </div>
            
        </div>
</body>
</html>
