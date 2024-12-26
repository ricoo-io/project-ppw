<?php
session_start();
require_once('dbcontroller.php');   
$db = new dbcontroller();


if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || !isset($_SESSION['iduser'])) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['iduser'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['order_id'])) {
        $orderId = intval($_POST['order_id']);
        $sql = "UPDATE t_orders SET f_status = 'Completed' WHERE f_id = $orderId AND f_iduser = $user_id";
        $db->runSQL($sql);   
        header("Location: orders.php");
        exit;
    }
}


?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="css/orders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="public/images/logo2.png" type="image/gif" sizes="16x16">
    <title>KnowDays | Profile</title> 
</head>

<body>
    <nav>
        <div class="img">
            <a href="index.php"><img src="public/images/logo_nobg.png" alt="" style="height: 50px;"></a>
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
        <div class="sidebar">
            <div class="profile-container">
                <div class="profile-header">
                    <img src="public/images/<?php echo $_SESSION['poto']; ?>" alt="Profile Picture">
                </div>
                
                <div class="profile-name">
                    <h2><?php echo $_SESSION['name']; ?></h2>
                    <div class="profile-detail">
                        <p><?php echo $_SESSION['email']; ?></p>
                    </div>
                </div>
                <div class="line"></div>
            </div>

            <div class="sidebar-menu">
                <a href="profile.php" >Profile Details</a>
                <a href="wishlist.php" >Wishlist</a>
                <a href="Orders.php" class="active">My Orders</a>

                <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="kelolaproduk.php">Kelola Produk</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content">
            <h1>My Orders</h1>
            <div class="tabs">
                <button class="tab-button" onclick="openTab(event, 'all-orders')">All Orders</button>
                <button class="tab-button" onclick="openTab(event, 'packed')">Packaging</button>
                <button class="tab-button" onclick="openTab(event, 'shipped')">Shipping</button>
                <button class="tab-button" onclick="openTab(event, 'arrived')">Arrived</button>
                <button class="tab-button" onclick="openTab(event, 'completed')">Completed</button>
                <button class="tab-button" onclick="openTab(event, 'review')">Awaiting Review</button>
            </div>
            <div id="all-orders" class="tab-content">
            <?php
                $sql="SELECT * FROM t_orders WHERE f_iduser = $user_id ORDER BY f_tanggal_pembelian DESC";
                $orders = $db->getALL($sql);
                ?>
                <div class="order-list">
                    <?php if(empty($orders)): ?>
                        <h3 style="text-align: center;">No orders found</h3>
                        <?php else: ?>
                            <?php $i = 0; ?>
                            <?php foreach ($orders as $order):; ?>
                                <div class="order">
                                <?php
                                    $i+=1;
                                    $id = $order['f_id'];
                                    $sql = "SELECT t_orderdetails.*, t_barang.f_gambar,t_barang.f_pakaian
                                            FROM t_orderdetails
                                            JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                                            WHERE t_orderdetails.f_idorder = $id";
                                    $orderDetails = $db->getALL($sql);
                                    ?>
                                <div class="order-header">
                                    <h4>Order #<?php echo $order['f_id']; ?> | <?php echo $order['f_tanggal_pembelian']; ?></h4>
                                    <p>Status: <?php echo $order['f_status']; ?></p>
                                </div>
                                    <?php foreach ($orderDetails as $product): ?>
                                        <div class="product">
                                            <div class="product-image-placeholder">
                                                <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                            </div>
                                            <div class="product-details">
                                                <h3><?php echo $product['f_pakaian']; ?></h3>
                                                <div style="display: flex; gap: 10px" >
                                                    <img src="public/images/<?php echo htmlspecialchars($product['f_warna']); ?>" alt="warna produk">
                                                    <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                                    <p>|</p>
                                                    <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="packed" class="tab-content">
                <?php
                $sql="SELECT * FROM t_orders WHERE f_iduser = $user_id AND f_status = 'Packaging'";
                $orders = $db->getALL($sql);
                ?>
                <div class="order-list">
                    <?php if(empty($orders)): ?>
                        <h3 style="text-align: center;">No orders found</h3>
                        <?php else: ?>
                            <?php $i = 0; ?>
                            <?php foreach ($orders as $order):; ?>
                                <div class="order">
                                <?php
                                    $i+=1;
                                    $id = $order['f_id'];
                                    $sql = "SELECT t_orderdetails.*, t_barang.f_gambar,t_barang.f_pakaian
                                            FROM t_orderdetails
                                            JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                                            WHERE t_orderdetails.f_idorder = $id";
                                    $orderDetails = $db->getALL($sql);
                                    ?>
                                <div class="order-header">
                                    <h4>Order #<?php echo $order['f_id']; ?> | <?php echo $order['f_tanggal_pembelian']; ?></h4>
                                    <p>Status: <?php echo $order['f_status']; ?></p>
                                </div>
                                    <?php foreach ($orderDetails as $product): ?>
                                        <div class="product">
                                            <div class="product-image-placeholder">
                                                <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                            </div>
                                            <div class="product-details">
                                                <h3><?php echo $product['f_pakaian']; ?></h3>
                                                <div style="display: flex; gap: 10px" >
                                                    <img src="public/images/<?php echo htmlspecialchars($product['f_warna']); ?>" alt="warna produk">
                                                    <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                                    <p>|</p>
                                                    <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="shipped" class="tab-content">
                <?php
                $sql="SELECT * FROM t_orders WHERE f_iduser = $user_id AND f_status = 'Shipping'";
                $orders = $db->getALL($sql);
                ?>
                <div class="order-list">
                    <?php if(empty($orders)): ?>
                        <h3 style="text-align: center;">No orders found</h3>
                        <?php else: ?>
                            <?php $i = 0; ?>
                            <?php foreach ($orders as $order):; ?>
                                <div class="order">
                                <?php
                                    $i+=1;
                                    $id = $order['f_id'];
                                    $sql = "SELECT t_orderdetails.*, t_barang.f_gambar,t_barang.f_pakaian
                                            FROM t_orderdetails
                                            JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                                            WHERE t_orderdetails.f_idorder = $id";
                                    $orderDetails = $db->getALL($sql);
                                    ?>
                                <div class="order-header">
                                    <h4>Order #<?php echo $order['f_id']; ?> | <?php echo $order['f_tanggal_pembelian']; ?></h4>
                                    <p>Status: <?php echo $order['f_status']; ?></p>
                                </div>
                                    <?php foreach ($orderDetails as $product): ?>
                                        <div class="product">
                                            <div class="product-image-placeholder">
                                                <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                            </div>
                                            <div class="product-details">
                                                <h3><?php echo $product['f_pakaian']; ?></h3>
                                                <div style="display: flex; gap: 10px" >
                                                    <img src="public/images/<?php echo htmlspecialchars($product['f_warna']); ?>" alt="warna produk">
                                                    <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                                    <p>|</p>
                                                    <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="arrived" class="tab-content">
            <?php
                $sql="SELECT * FROM t_orders WHERE f_iduser = $user_id AND f_status = 'Arrived'";
                $orders = $db->getALL($sql);
                ?>
                <div class="order-list">
                    <?php if(empty($orders)): ?>
                        <h3 style="text-align: center;">No orders found</h3>
                        <?php else: ?>
                            <?php $i = 0; ?>
                            <?php foreach ($orders as $order):; ?>
                                <div class="order">
                                    <?php
                                    $i+=1;
                                    $id = $order['f_id'];
                                    $sql = "SELECT t_orderdetails.*, t_barang.f_gambar,t_barang.f_pakaian
                                            FROM t_orderdetails
                                            JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                                            WHERE t_orderdetails.f_idorder = $id";
                                    $orderDetails = $db->getALL($sql);
                                    ?>
                                    <div class="order-header">
                                        <h4>Order #<?php echo $order['f_id']; ?> | <?php echo $order['f_tanggal_pembelian']; ?></h4>
                                        <p>Status: <?php echo $order['f_status']; ?></p>
                                    </div>
                                    <?php foreach ($orderDetails as $product): ?>
                                        <div class="product">
                                            <div class="product-image-placeholder">
                                                <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                            </div>
                                            <div class="product-details">
                                                <h3><?php echo $product['f_pakaian']; ?></h3>
                                                <div style="display: flex; gap: 10px" >
                                                    <img src="public/images/<?php echo htmlspecialchars($product['f_warna']); ?>" alt="warna produk">
                                                    <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                                    <p>|</p>
                                                    <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                                </div>
                                            </div>
                                            <div class="product-info">
                                                <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <div class="confirm-arrival">
                                        <form action="" method="POST">
                                            <input type="hidden" name="order_id" value="<?php echo $id; ?>">
                                            <button type="submit">Confirm Arrival</button>
                                        </form>
                                    </div>
                                </div>
                                
                            <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="completed" class="tab-content">
                <?php
                $sql = "SELECT * FROM t_orders WHERE f_iduser = $user_id AND f_status = 'Completed'";
                $completedOrders = $db->getALL($sql);
                ?>
                <div class="order-list">
                    <?php if(empty($completedOrders)): ?>
                        <h3 style="text-align: center;">No completed orders</h3>
                    <?php else: ?>
                        <?php $i = 0; ?>
                        <?php foreach ($completedOrders as $order): ?>
                            <div class="order">
                                <?php
                                $i+=1;
                                $id = $order['f_id'];
                                $sql = "SELECT t_orderdetails.*, t_barang.f_gambar,t_barang.f_pakaian
                                        FROM 
                                            t_orderdetails
                                        JOIN 
                                            t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                                        WHERE 
                                            t_orderdetails.f_idorder = $id";
                                $orderDetails = $db->getALL($sql);
                                ?>
                                <div class="order-header">
                                    <h4>Order #<?php echo $order['f_id']; ?> | <?php echo $order['f_tanggal_pembelian']; ?></h4>
                                    <p>Status: <?php echo $order['f_status']; ?></p>
                                </div>
                                <?php foreach ($orderDetails as $product): ?>
                                    <div class="product">
                                        <div class="product-image-placeholder">
                                            <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                        </div>
                                        <div class="product-details">
                                            <h3><?php echo $product['f_pakaian']; ?></h3>
                                            <div style="display: flex; gap: 10px" >
                                                <img src="public/images/<?php echo htmlspecialchars($product['f_warna']); ?>" alt="warna produk">
                                                <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                                <p>|</p>
                                                <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                            </div>
                                        </div>
                                        <div class="product-info">
                                            <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div id="review" class="tab-content">
                <?php
                $reviewSql = "SELECT 
                    t_orders.f_tanggal_pembelian, 
                    t_orders.f_status,
                    t_orderdetails.f_ukuran,
                    t_orderdetails.f_warna,
                    t_orderdetails.f_quantity,
                    t_barang.f_gambar,
                    t_barang.f_pakaian,
                    t_barang.f_harga,
                    t_barang.f_id as barang_id
                FROM 
                    t_orders
                JOIN 
                    t_orderdetails ON t_orders.f_id = t_orderdetails.f_idorder
                JOIN 
                    t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                WHERE 
                    t_orders.f_iduser = $user_id
                    AND t_orders.f_status = 'Completed' 
                    AND (t_orderdetails.f_reviewed IS NULL OR t_orderdetails.f_reviewed = '')";

                $reviewOrders = $db->getALL($reviewSql);
                ?>
                <div class="order-list">
                    <?php if(empty($reviewOrders)): ?>
                        <h3 style="text-align: center;">No orders to review</h3>
                    <?php else: ?>
                        <?php foreach ($reviewOrders as $product): ?>
                            <div class="order">
                                <div class="order-header">
                                    <h4><?php echo $product['f_tanggal_pembelian']; ?></h4>
                                    <p>Status: <?php echo $product['f_status']; ?></p>
                                </div>
                                <div class="product">
                                    <div class="product-image-placeholder">
                                        <img src="public/images/<?php echo $product['f_gambar']; ?>" alt="Product Image">
                                    </div>
                                    <div class="product-details">
                                        <h3><?php echo $product['f_pakaian']; ?></h3>
                                        <div style="display: flex; gap: 10px" >
                                            <p>Size: <?php echo $product['f_ukuran']; ?></p>
                                            <p>|</p>
                                            <p>Quantity: <?php echo $product['f_quantity']; ?></p>
                                        </div>
                                    </div>
                                    <div class="product-info">
                                        <p>Rp.<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                        <div>
                                            <div class="review">
                                                <button type="button" onclick="openReviewModal('<?php echo $product['barang_id']; ?>', '<?php echo htmlspecialchars($product['f_ukuran']); ?>', '<?php echo htmlspecialchars($product['f_warna']); ?>')" class="review-btn">
                                                    Leave Review
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="reviewModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Write a Review</h2>
            <form id="reviewForm" action="submit_review.php" method="POST">
                <input type="hidden" id="productId" name="productId">
                <input type="hidden" id="productSize" name="size">
                <input type="hidden" id="productColor" name="color">
                <div class="rating">
                    <input type="radio" id="star5" name="rating" value="5" required>
                    <label for="star5">★</label>
                    <input type="radio" id="star4" name="rating" value="4">
                    <label for="star4">★</label>
                    <input type="radio" id="star3" name="rating" value="3">
                    <label for="star3">★</label>
                    <input type="radio" id="star2" name="rating" value="2">
                    <label for="star2">★</label>
                    <input type="radio" id="star1" name="rating" value="1">
                    <label for="star1">★</label>
                </div>
                <textarea name="review" id="reviewText" placeholder="Write your review here..." required></textarea>
                <button type="submit" class="submit-review">Submit Review</button>
            </form>
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

  
    function openTab(evt, tabName) {
        var i, tabcontent, tabbuttons;   
     
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        
        tabbuttons = document.getElementsByClassName("tab-button");
        for (i = 0; i < tabbuttons.length; i++) {
            tabbuttons[i].className = tabbuttons[i].className.replace(" active", "");
        }
        
        document.getElementById(tabName).style.display = "block";
        evt.currentTarget.className += " active";
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.tab-button').click();
    });

    function openReviewModal(productId, size, color) {
        const modal = document.getElementById("reviewModal");
        document.getElementById("productId").value = productId;
        document.getElementById("productSize").value = size;
        document.getElementById("productColor").value = color;
        modal.style.display = "block";
    }

    const modal = document.getElementById("reviewModal");
    
    const span = document.getElementsByClassName("close")[0];
    
    span.onclick = function() {
        modal.style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
</body>
</html>