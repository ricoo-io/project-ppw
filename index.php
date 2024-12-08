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
        t_barang.f_quantity,
        t_barang.f_idukuran,  
        t_ukuran.f_ukuran,    
        t_kategori.f_kategori, 

        GROUP_CONCAT(t_colors.f_colour) AS colors
        FROM 
        t_barang
        LEFT JOIN 
            barang_color ON t_barang.f_id = barang_color.f_idbarang
        LEFT JOIN 
            t_colors ON barang_color.f_idwarna = t_colors.f_id
        LEFT JOIN 
            t_ukuran ON t_barang.f_idukuran = t_ukuran.f_id 
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
        $quantity = 1; 
        $db->addToCart($user_id, $productId, $quantity);
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

    <div class="tag">
        <h2>Categories</h2>
        <a href="" class="button">See All</a>
    </div>

    <div class="container">
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
                        <div class="color-product">
                            <?php 
                            $colorImages = explode(',', $product['colors']);
                            foreach ($colorImages as $colorImage): 
                            ?>
                                <img src="public/images/<?php echo htmlspecialchars(trim($colorImage)); ?>" alt="Color Image">
                            <?php endforeach; ?>
                        </div>

                        <div class="quantity">
                                <p>stock: <?php echo htmlspecialchars($product['f_quantity']); ?></p>
                        </div>
        
                        <div class="desk">
                                <h5><?php echo htmlspecialchars($product['f_pakaian']); ?></h5>
                            <div class="size">
                                <p>size: <?php echo htmlspecialchars($product['f_ukuran']); ?></p>
                            </div>
                        </div>

                        <div class="rattingprice">
                            <div class="ratting">
                                <?php for ($i = 0; $i < $product['f_rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>

                            <div class="price">
                                <p>Rp <?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
                            </div>
                        </div>

                        <div class="btn-cart-details">
                            <div class="cart">
                            <form method="POST" action="">
                                <input type="hidden" name="product_id" value="<?php echo $product['f_id']; ?>">
                                <button type="submit" name="add_to_cart">
                                    <i class="fas fa-shopping-cart"></i> Add to Cart
                                </button>
                            </form>
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


    <footer>
        <p class="copy">
            Copyright <i class="far fa-copyright"> PPW 2024 KnowDays | Kelompok 8</i>
        </p>
    </footer>

</body>
</html>