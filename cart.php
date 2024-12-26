<?php
session_start();
require_once('dbcontroller.php');
$db = new dbcontroller();

$iduser = $_SESSION['iduser'];

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$sql = "SELECT t_cart.f_id, t_barang.f_pakaian, t_barang.f_gambar, t_barang.f_harga, 
        t_cart.f_quantity, t_cart.f_total_harga, t_cart.f_iduser, t_cart.f_idbarang, 
        t_barang.f_diskon, t_cart.f_ukuran, t_cart.f_warna, t_barang.f_quantity as stock
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="public\images\logo.png" type="image/gif" sizes="16x16">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/cart.css">
    <title>KnowDays | Your Cart</title>
</head>
<body>
    <nav>
        <div class="img">
            <a href="index.php"><img src="public\images\logo_nobg.png" alt="" style="height: 50px;"></a>
        </div>
        <form method="POST" class="search-container">
            <input type="text" id="search" name="search" placeholder="Search..">
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
                            <div class="product-image">
                                <a href="produk.php?id=<?php echo urlencode($item['f_idbarang']);?>">
                                    <img src="public/images/<?php echo htmlspecialchars($item['f_gambar']); ?>" alt="<?php echo htmlspecialchars($item['f_pakaian']); ?>">
                                </a>
                            </div>
                            <div class="item-details">
                                <h4><?= htmlspecialchars($item['f_pakaian']) ?></h4>
                                <div class="product-color">
                                <img src="public/images/<?= htmlspecialchars($item['f_warna']); ?>" alt="color">
                                    <p>Size: <?= htmlspecialchars($item['f_ukuran']); ?></p>
                                </div>
                            </div>
                            <div class="cart-item-controls">
                            <?php if ($item['f_diskon'] > 0): ?>
                                <span class="original-price">Rp<?php echo number_format($item['f_harga'], 0, ',', '.'); ?></span>
                                <div>
                                <span class="discount-badge-2">-<?php echo $item['f_diskon']; ?>%</span>
                                    <span class="discounted-price" data-original-price="Rp<?php echo $item['f_harga']; ?>">Rp<?php echo number_format($item['f_harga'] * (1 - $item['f_diskon']/100), 0, ',', '.'); ?></span>
                                </div>
                            <?php else: ?>
                                <span class="discounted-price">Rp<?php echo number_format($item['f_harga'], 0, ',', '.'); ?></span>
                            <?php endif; ?>
                                <form method="POST" action="cart.php" class="quantity-form">
                                    <input type="hidden" name="cart_id" value="<?= $item['f_id'] ?>">
                                    <input type="hidden" id="stock-<?= $item['f_id'] ?>" value="<?= $item['stock'] ?>">
                                    <?php $isInWishlist = $db->isInWishlist($_SESSION['iduser'], $item['f_idbarang']); ?>
                                        <i id="heart-icon-<?php echo $item['f_idbarang']; ?>" 
                                            class="fas fa-heart" 
                                            style="color: <?php echo $isInWishlist ? "rgb(255, 0, 0)" : "rgb(155, 155, 155)"; ?>; font-size: 25px; cursor: pointer;"
                                            onclick="toggleWishlist(<?php echo $_SESSION['iduser']; ?>, <?php echo $item['f_idbarang']; ?>)">
                                        </i>
                                    <button type="submit" name="delete" class="delete-btn">🗑</button>
                                    <button type="button" class="decrement-btn" onclick="updateQuantity(<?= $item['f_id'] ?>, -1, <?= $item['f_harga'] ?>)">-</button>
                                    <input type="number" style="text-align: center" id="quantity-<?= $item['f_id'] ?>" value="<?= $item['f_quantity'] ?>" min="1" max="<?= $item['stock'] ?>" readonly>
                                    <button type="button" class="increment-btn" onclick="updateQuantity(<?= $item['f_id'] ?>, 1, <?= $item['f_harga'] ?>)">+</button>
                                </form>
                            </div>
                        </div>
                    <?php } 
                } ?>
            </div>
            <div class="cart-summary">
                <div class="summary-title">Summary</div>
                <div class="summary-item">
                    <div id="total-items"><?= $totalQuantity ?> Items</div>
                    <?php if (!empty($cartItems)) { ?>
                        <?php foreach ($cartItems as $item) {
                            $totalPrice += ($item['f_harga']*(1 - $item['f_diskon']/100)) * $item['f_quantity'];
                        }?>
                    <div id="total-price">Total: Rp <?= number_format($totalPrice, 0, ',', '.') ?></div>
                    <?php } ?>
                </div>
                <div class="checkout-buttons">
                    <a href="<?php echo !empty($cartItems) ? 'Checkout.php' : 'javascript:void(0)'; ?>" 
                       class="<?php echo !empty($cartItems) ? 'checkout-enabled' : 'checkout-disabled'; ?>">
                        Check Out
                    </a>
                </div>
            </div>
        </div>
    </div>

<script>


function toggleWishlist(userId, itemId) {
        const icon = document.getElementById(`heart-icon-${itemId}`);
        const isInWishlist = icon.style.color === "rgb(255, 0, 0)"; 

        fetch('toggle-wishlist.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                userId: userId,
                itemId: itemId,
                action: isInWishlist ? 'remove' : 'add'
            }),
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                icon.style.color = isInWishlist ? "rgb(155, 155, 155)" : "rgb(255, 0, 0)";
                location.reload();
            } else {
                alert('Failed to update wishlist: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function updateQuantity(cartId, change, price) {
    const quantityInput = document.getElementById(`quantity-${cartId}`);
    const stockInput = document.getElementById(`stock-${cartId}`);
    const stock = parseInt(stockInput.value);

    const cartItem = document.querySelector(`#quantity-${cartId}`).closest('.cart-item');
    const discountElement = cartItem.querySelector('.discount-badge-2');
    const discount = discountElement ? parseFloat(discountElement.textContent.replace('-', '').replace('%', '')) / 100 : 0;

    let newQuantity = parseInt(quantityInput.value) + change;

    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > stock) {
        alert('Cannot exceed available stock!');
        newQuantity = stock;
    }

    quantityInput.value = newQuantity;

    const discountedPrice = price * (1 - discount);
    const itemTotalPrice = discountedPrice * newQuantity;

    const itemTotalElement = cartItem.querySelector('.item-total-price');
    if (itemTotalElement) {
        itemTotalElement.textContent = `Rp ${itemTotalPrice.toLocaleString('id-ID')}`;
    }

    let totalItems = 0;
    let totalPrice = 0;

    document.querySelectorAll('.cart-item').forEach(item => {
        const quantity = parseInt(item.querySelector('input[type="number"]').value);

        const itemDiscountElement = item.querySelector('.discount-badge-2');
        const itemDiscount = itemDiscountElement ? parseFloat(itemDiscountElement.textContent.replace('-', '').replace('%', '')) / 100 : 0;

        const originalPrice = parseFloat(item.querySelector('.discounted-price').dataset.originalPrice.replace(/[^\d.]/g, ''));
        const finalPrice = originalPrice * (1 - itemDiscount);

        totalItems += quantity;
        totalPrice += finalPrice * quantity;
    });

    document.getElementById('total-items').textContent = `${totalItems} Items`;
    document.getElementById('total-price').textContent = `Total: Rp ${totalPrice.toLocaleString('id-ID')}`;

    fetch('cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `update=true&cart_id=${cartId}&quantity=${newQuantity}`
    });
}

</script>
</body>
</html>
