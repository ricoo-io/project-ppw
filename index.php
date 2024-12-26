<?php
session_start();
require_once "dbcontroller.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

$user_id = $_SESSION['iduser'];

$db = new dbcontroller();
$sql = "SELECT 
        t_barang.f_id,
        t_barang.f_pakaian, 
        t_barang.f_gambar, 
        t_barang.f_harga, 
        t_barang.f_rating,
        t_barang.f_diskon,
        t_barang.f_quantity,  
        t_ukuran.f_ukuran,    
        t_kategori.f_kategori, 

        GROUP_CONCAT(DISTINCT t_ukuran.f_ukuran ORDER BY FIELD(t_ukuran.f_ukuran, 'S', 'M', 'L', 'XL')) AS ukuran,
        GROUP_CONCAT(DISTINCT t_colors.f_colour) AS colors
        FROM 
        t_barang
        LEFT JOIN 
            barang_color ON t_barang.f_id = barang_color.f_idbarang
        LEFT JOIN 
            t_colors ON barang_color.f_idwarna = t_colors.f_id
        LEFT JOIN 
            barang_ukuran ON t_barang.f_id = barang_ukuran.f_idbarang 
        LEFT JOIN 
            t_ukuran ON barang_ukuran.f_idukuran = t_ukuran.f_id 
        LEFT JOIN 
            t_kategori ON t_barang.f_idkategori = t_kategori.f_id 
        GROUP BY 
            t_barang.f_id";

        $products = $db->getALL($sql);

$sql = "SELECT 
        f_id,
        f_kategori,
        f_gambar
        FROM t_kategori
        ORDER BY f_id"; 
        $categories = $db->getALL($sql);

if (isset($_POST['add_to_cart'])) {
        $productId = $_POST['product_id'];
        $size = $_POST['size'];
        $color = $_POST['color'];
        $quantity = 1; 
        $db->addToCart($user_id, $productId, $quantity,$size,$color);
        echo "<script>alert('Product added to cart!');</script>";
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
    $_SESSION['search'] = trim($_POST['search']);
    header('Location: search.php');
    exit;
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/index.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" href="public\images\logo2.png" type="image/gif" sizes="16x16">
    <title>KnowDays</title>
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

    <section class="banner">
        <div class="main-text">
            <div class="main-textleft">
                <div class="info">
                    <h2>Order Your Best <br> Fashion anytime</h2>
                    <p>Hey, fashion trending is waiting for you <br> Here we provide various types of fashion</p>
                </div>
            </div>
            <div class="main-textright">
                <img src="public\images\fashion.png" alt="">
            </div>
            
        </div>
    </section>

    <div class="container-1">
        <div class="content-1">
            <div class="tag">
                <h2>Categories</h2>
                <a href="kategori.php?id==0" class="button">See All</a>
            </div>

            <div class="box-container">
                
                <?php foreach (array_slice($categories, 0, 8)  as $category): ?>
                    <div class="box">
                        <a href="kategori.php?id=<?php echo urlencode($category['f_id']);?>">
                            <img src="public/images/<?php echo htmlspecialchars($category['f_gambar']); ?>" alt="<?php echo htmlspecialchars($category['f_gambar']); ?>">
                        </a>
                        <div class="info">
                            <a href="kategori.php?id=<?php echo urlencode($category['f_id']);?>" style="text-decoration: none;"><h3><?php echo htmlspecialchars($category['f_kategori']);?></h3></a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="like">
                <h2>You Might Like</h2>
            </div>

            <div class="container-cardd">
                <div class="card-container">
                    <?php foreach ($products as $product): ?>
                        <div class="card">
                            <a href="produk.php?id=<?php echo urlencode($product['f_id']);?>">
                                <img src="public/images/<?php echo htmlspecialchars($product['f_gambar']); ?>" alt="<?php echo htmlspecialchars($product['f_pakaian']); ?>">
                            </a>
                            <div class="card-content">
                    
                                <div class="color-wishlist">
                                    <div class="color-product">
                                        <?php 
                                        $colorImages = explode(',', $product['colors']);
                                        foreach ($colorImages as $colorImage): 
                                        ?>
                                            <img src="public/images/<?php echo htmlspecialchars(trim($colorImage)); ?>" alt="Color Image">
                                        <?php endforeach; ?>
                                    </div>

                                    
                                    <?php $isInWishlist = $db->isInWishlist($_SESSION['iduser'], $product['f_id']); ?>
                                    <i id="heart-icon-<?php echo $product['f_id']; ?>" 
                                        class="fas fa-heart" 
                                        style="color: <?php echo $isInWishlist ? "rgb(255, 0, 0)" : "rgb(155, 155, 155)"; ?>; font-size: 25px; cursor: pointer;"
                                        onclick="toggleWishlist(<?php echo $_SESSION['iduser']; ?>, <?php echo $product['f_id']; ?>)">
                                    </i>
                                </div>
                                <div class="sizes">
                                    <?php 
                                    $sizes = explode(',', $product['ukuran']);
                                    foreach ($sizes as $ukuran): 
                                    ?>
                                        <p><?php echo htmlspecialchars(trim($ukuran)); ?> </p>
                                    <?php endforeach; ?>
                                </div>
                                <div class="desk">
                                        <h5><?php echo htmlspecialchars($product['f_pakaian']); ?></h5>
                                </div>

                                <div class="rattingprice">
                                    <div class="ratting">
                                        <?php $sql = "SELECT AVG(f_rating) AS rating FROM t_review WHERE f_idbarang = " . $product['f_id'];
                                        $result = $db->getITEM($sql);
                                        $rating = $result['rating'] ?? 0; ?>
                                        <span style="display: inline-flex; align-items: center;">
                                            <i class="fas fa-star" style="color: rgb(252, 186, 3);"></i> 
                                            <p style="margin: 0; padding-left: 5px; color=#a1a1a1;">(<?php echo number_format($rating, 1); ?>)</p>
                                        </span>   
                                    </div>

                                    <div class="price">
                                        <?php if ($product['f_diskon'] > 0): ?>
                                            <div class="price-container">
                                                <p class="original-price">Rp <?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                                <p class="discounted-price">
                                                    Rp <?php 
                                                    $discounted_price = $product['f_harga'] * (1 - $product['f_diskon']/100);
                                                    echo number_format($discounted_price, 0, ',', '.'); 
                                                    ?>
                                                </p>
                                                <span class="discount-badge">-<?php echo $product['f_diskon']; ?>%</span>
                                            </div>
                                        <?php else: ?>
                                            <p>Rp <?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="btn-cart-details">
                                    <div class="cart">
                                        <button type="button" onclick='showPopup(<?php 
                                            echo json_encode([
                                                "id" => $product["f_id"],
                                                "name" => $product["f_pakaian"],
                                                "colors" => $product["colors"],
                                                "sizes" => $product["ukuran"]
                                            ], JSON_HEX_APOS | JSON_HEX_QUOT); 
                                        ?>)'>
                                            <i class="fas fa-shopping-cart"></i> Add to Cart
                                        </button>
                                    </div>
                                    <div class="details">
                                        <button><a href="produk.php?id=<?php echo urlencode($product['f_id']);?>"><i class="fas fa-shopping-bag"></i> Details</a></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="cartPopup" class="popup-2" style="display: none;">
        <div class="popup-content">
            <form id="cartForm" method="POST" action="">
                <input type="hidden" name="product_id" id="popup_product_id">
                <h3 id="popup_product_name"></h3>
                <div class="options">
                    <div class="option">
                        <label>Pilihan Warna:</label>
                        <div class="color-product2" id="popup_colors">
                           
                        </div>
                    </div>
                    <div class="option">
                        <label>Ukuran:</label>
                        <div class="option-buttons" id="popup_sizes">
                       
                        </div>
                    </div>
                </div>
                <div class="cart">
                    <button type="submit" name="add_to_cart" class="checkout-btn">
                        <i class="fas fa-shopping-cart"></i> Add to Cart
                    </button>
                    <button type="button" onclick="closePopup()" class="cancel-btn" style="background-color: #f44336;">
                        Cancel
                    </button>
                </div>
            </form>
        </div>  
    </div>

    <footer>
        <p class="copy">
            Copyright <i class="far fa-copyright"> PPW 2024 KnowDays | Kelompok 8</i>
        </p>
    </footer>


    
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
            } else {
                alert('Failed to update wishlist: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function showPopup(productData) {
        const popup = document.getElementById('cartPopup');
        const popupColors = document.getElementById('popup_colors');
        const popupSizes = document.getElementById('popup_sizes');
        const productIdInput = document.getElementById('popup_product_id');
        const productNameElement = document.getElementById('popup_product_name');

        productIdInput.value = productData.id;
        productNameElement.textContent = productData.name;

        popupColors.innerHTML = '';
        popupSizes.innerHTML = '';

        productData.colors.split(',').forEach((color, index) => {
            const colorTrim = color.trim();
            const label = document.createElement('label');
            const radioId = `color_${productData.id}_${index}`;
            
            label.innerHTML = `
                <input type="radio" id="${radioId}" name="color" value="${colorTrim}" required>
                <img src="public/images/${colorTrim}" alt="Color ${colorTrim}">
            `;
            popupColors.appendChild(label);
        });

        productData.sizes.split(',').forEach((size, index) => {
            const sizeTrim = size.trim();
            const label = document.createElement('label');
            const radioId = `size_${productData.id}_${index}`;
            
            label.className = 'size-label';
            label.innerHTML = `
                <input type="radio" id="${radioId}" name="size" value="${sizeTrim}" required>
                <span class="size-text">${sizeTrim}</span>
            `;
            popupSizes.appendChild(label);
        });

        popup.style.display = 'flex';
    }

    function closePopup() {
        document.getElementById('cartPopup').style.display = 'none';
    }

    window.onclick = function(event) {
        const popup = document.getElementById('cartPopup');
        if (event.target === popup) {
            closePopup();
        }
    }

</script>
</body>
</html>

