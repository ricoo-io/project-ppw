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
        t_cart.f_ukuran, t_cart.f_warna, t_barang.f_quantity as stock
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
    <title>Knowdays - Checkout</title>
    <link rel="stylesheet" href="css/checkout.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="public\images\logo2.png" type="image/gif" sizes="16x16">
    <script>
        function toggleEdit() {
            const form = document.getElementById('edit-form');
            form.style.display = form.style.display === 'block' ? 'none' : 'block';
        }

        function saveAddress(event) {
            event.preventDefault();
            const newAddress = document.getElementById('address-input').value;
            document.getElementById('address-text').innerText = newAddress;
            window.location.href='#'
            toggleEdit();
        }
    </script>
</head>
<body>
    <nav>
        <div class="img">
            <a href="index.php"><img src="public\images\logo_nobg.png" alt="" style="height: 50px; "></a>
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

    <div class="Container">
        <div class="leftsect">
            <div class="ship">
                <div class="box">
                    <div class="shipad"></div>
                        <div class="shipping-container">
                            <div class="shipping-address">
                                <strong>Shipping Address</strong>
                                <div class="adrs"><span id="address-text">jl. Johar Baru IV RT 04 RW 05, Johar Baru, Jakarta Pusat, DKI Jakarta</span></div>
                            </div>
                            <a class="edit-button" href="#popup" onclick="toggleEdit()">Change</a>
                            <div class="popup" id="popup">
                                <div class="popup__content">
                                    <div class="popup__header">
                                        <a href="#" class="popup__close" onclick="toggleEdit()">&times;</a>
                                    </div>
                                    <div class="popup__text">
                                        <form class="edit-form" id="edit-form" onsubmit="saveAddress(event)">
                                            <textarea id="address-input" rows="3" style="width: 100%;">jl. Johar Baru IV RT 04 RW 05, Johar Baru, Jakarta Pusat, DKI Jakarta</textarea>
                                            <button  type="submit" class="save-button"> Save </button>
                                        </form>
                                    </div>
                                </div>    
                            </div>
                        </div>
                </div>
            </div>
            <div class="plist">
                <div class="box">
                    <div class="product">
                        <img src="public/images/image 1.png" alt="Manolo Blahnik">
                        <div class="dp">
                            <div class="nq">
                                <p class="namap"> Manolo Blahnik</p>
                                <p class="qty"> QTY : 1</p>
                            </div>
                            <div><p class="harga">Rp20,739,300</p></div>
                        </div>
                    </div>
                    <div class="product">
                        <img src="public/images/image 1.png" alt="Manolo Blahnik">
                        <div class="dp">
                            <div class="nq">
                                <p class="namap"> Manolo Blahnik</p>
                                <p class="qty"> QTY : 1</p>
                            </div>
                            <div><p class="harga">Rp20,739,300</p></div>
                        </div>
                </div>
            </div>
            </div>
            <div class="sopti">
                <div class="box">
                    <p class="title"> Shipping Option </p>
                    <div class="dropdown">
                        <span>Reguler (Rp. 16.500) - ETA 5 Days</span>
                        <div class="dropdown-content">
                            <a href="#">Express (Rp. 20.000) - ETA 1 - 2 Days</a>
                            <a href="#">One Day (Rp. 25.000) - ETA 1 Days</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <aside>
            <div class="box">
                <div class="paymet">
                    <div class="method">
                        <p class="title">Payment Method</p>
                        <form class="payment-options">
                            <label class="option">
                              <input type="radio" name="payment" value="cod">COD
                            </label>
                            <label class="option">
                              <input type="radio" name="payment" value="virtual-account"> Virtual Account
                            </label>
                          </form>
                    </div>
                    <div class="Summary">
                        <p class="title"> Summary </p>
                        <div class="deha">
                            <div class="d">
                                <p> Total (3 item) </p>
                                <p> Shipping cost </p>
                                <p> Service Charge </p>
                            </div>
                            <div class="h">
                                <p> Rp. 6.200.000 </p>
                                <p> Rp. 16.500 </p>
                                <p> Rp. 2.500</p>
                            </div>
                        </div>
                    </div>
                    <div class="GrandTotal">
                        <p > Grand Total </p>
                        <p > RP. 6.219.000</p>
                    </div>
                    <div class="checkoutt">
                        <button class="checkout-button">Checkout</button>
                    </div>
                </div>
            </div>
        </aside>
    </div>
    <footer>
        <p class="copy">
            Copyright <i class="far fa-copyright"> PPW 2024 KnowDays | Kelompok 8</i>
        </p>
    </footer>
    