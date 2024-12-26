<?php
session_start();
require_once('dbcontroller.php');
$db = new dbcontroller();

$sql = "SELECT COUNT(*) as count FROM t_cart WHERE f_iduser = " . $_SESSION['iduser'];
$result = $db->getITEM($sql);
if ($result['count'] == 0) {
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
    
        $shipping_method = $_POST['shipping_method'];
        $payment_method = $_POST['payment_method'];
        $total_amount = $_POST['grand_total'];
        $address = $_POST['address'];

        $orderId = $db->createOrder(
            $_SESSION['iduser'],
            $total_amount,
            $shipping_method,
            $payment_method,
            $address
        );

        header("Location: thankyou.php?order_id=$orderId");
        exit;

}

$iduser = $_SESSION['iduser'] ?? null;
if (!$iduser || !isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}


$sql = "SELECT t_cart.*, t_barang.f_pakaian, t_barang.f_gambar, t_barang.f_harga,t_barang.f_diskon,
        t_barang.f_quantity as stock
        FROM t_cart
        JOIN t_barang ON t_cart.f_idbarang = t_barang.f_id
        WHERE t_cart.f_iduser = $iduser";
    $cartItems = $db->getALL($sql,);   

$totalPrice = 0;
$totalQuantity = 0;
foreach ($cartItems as $item) {
    $totalQuantity += $item['f_quantity'];
    $totalPrice += ($item['f_harga']*(1 - $item['f_diskon']/100)) * $item['f_quantity'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowdays - Checkout</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="public/images/logo2.png" type="image/png" sizes="16x16">
</head>
<body>
<nav>
    <div class="img">
        <a href="index.php"><img src="public/images/logo_nobg.png" alt="Knowdays Logo" style="height: 50px;"></a>
    </div>
    <form method="POST" class="search-container">
        <input type="text" id="search" name="search" placeholder="Search.." aria-label="Search">
        <button type="submit" name="submit_search"><img src="public/images/search.jpg" alt="Search"></button>
    </form>
    <ul>
        <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
        <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
        <li><a href="profile.php"><i class="fas fa-user"></i> User</a></li>
        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</nav>

<div class="container">
    <div class="leftsect">
        <div class="box-address">
            <h2>Shipping Address</h2>
            <p id="address-text"><?php echo $_SESSION['address']?></p>
            <button class="button" onclick="toggleModal()">Change</button>
        </div>
       
        
        <div class="checkout-item">
            <?php foreach ($cartItems as $item): ?>
                <div class="product">
                    <div style="display: flex; align-items: center;">
                        <img src="public/images/<?php echo htmlspecialchars($item['f_gambar']); ?>" alt="<?php echo htmlspecialchars($item['f_pakaian']); ?>">
                        <div class="product-info">
                            <div>
                                <h3><?php echo htmlspecialchars($item['f_pakaian']); ?></h3>
                                <div class="product-details">
                                    <img src="public/images/<?php echo htmlspecialchars($item['f_warna']); ?>" alt="warna produk">
                                    <p>Size: <?php echo $item['f_ukuran']; ?></p>
                                    <p>|</p>
                                    <p>Quantity: <?php echo $item['f_quantity']; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p>Rp <?php echo number_format(($item['f_harga']* (1 - $item['f_diskon']/100))*$item['f_quantity'], 0, ',', '.'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="shipping">
            <h2>Shipping Method</h2>
            <select id="shipping" name="shipping_method" onchange="updateShipping(this.value)">
                <option value="Economy">Economy (Rp 16.500)</option>
                <option value="Regular">Regular (Rp 25.000)</option>
                <option value="Express">Express (Rp 40.000)</option>
            </select>
        </div>

    </div>

    <div class="rightsect">
        <div class="checkout-summary">
            <form method="POST">
                <h4>Payment Method</h4>
                <div class="payment-options">
                    <div class="payment-option">
                        <input type="radio" name="payment_method" id="COD" value="COD" checked>
                        <label for="COD">COD</label>
                    </div>
                    <div class="payment-option">
                        <input type="radio" name="payment_method" id="Virtual_Account" value="Virtual Account">
                        <label for="Virtual_Account">Virtual Account</label>
                    </div>
                </div>

                <div class="line"></div>
                <h4>Summary</h4>
                <div class="prices">
                    <p>Subtotal items </p>
                    <p>Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></p>
                </div>
                <div class="prices">
                    <p>Shipping Fee </p>
                    <p id="shipping-fee">Rp 16.500</p>
                </div>
                <div class="prices">
                    <p>Service Charge </p>
                    <p>Rp <?php echo number_format(5000, 0, ',', '.'); ?></p>
                </div>

                <div class="line"></div>
                <div class="grand-total">
                    <p>Grand Total: </p> 
                    <p id="grand-total">
                        Rp <?php echo number_format($totalPrice + 16500 + 5000, 0, ',', '.'); ?>
                    </p>
                </div>
                <input type="hidden" name="grand_total" id="grand_total_input" value="<?php echo $totalPrice + 16500 + 5000; ?>">
                <input type="hidden" name="address" id="address_input" value="<?php echo htmlspecialchars($_SESSION['address']); ?>">
                <input type="hidden" name="shipping_method" id="shipping_method_input" value="Economy">
                <button type="submit" name="checkout" class="checkout-button">Checkout</button>
                
                <?php if (isset($error)): ?>
                    <p class="error"><?php echo $error; ?></p>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <textarea id="address-input" rows="3"><?php echo $_SESSION['address']?></textarea>
        
        <button onclick="toggleModal()">Cancel</button>
        <button onclick="saveAddress()">Save</button>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('modal');
        modal.style.display = modal.style.display === 'flex' ? 'none' : 'flex';
    }

    function saveAddress() {
        const addressInput = document.getElementById('address-input').value;
        document.getElementById('address-text').textContent = addressInput;
        document.getElementById('address_input').value = addressInput;
        toggleModal();
    }

    function updateShipping(value) {
        const shippingFee = {
            'Economy': 16500,
            'Regular': 25000,
            'Express': 40000
        }[value];
        
        const subtotal = <?php echo $totalPrice; ?>;
        const serviceCharge = 5000;
        const total = subtotal + shippingFee + serviceCharge;

        document.getElementById('shipping-fee').textContent = 
            'Rp ' + shippingFee.toLocaleString('id-ID');
        document.getElementById('grand-total').textContent = 
            'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('grand_total_input').value = total;
        document.getElementById('shipping_method_input').value = value;
    }
</script>
</body>
</html>