<?php
session_start();
require_once "dbcontroller.php";

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
    $_SESSION['search'] = trim($_POST['search']);
    header('Location: search.php');
    exit;
}


$user_id = $_SESSION['iduser'];
$db = new dbcontroller();
$sql = "SELECT 
        f_id,
        f_kategori,
        f_gambar
        FROM t_kategori
        ORDER BY f_id"; 
        $categories = $db->getALL($sql);


$sort = isset($_GET['sort']) ? $_GET['sort'] : '';

$orderBy = ''; 
switch ($sort) {
    case 'price_desc':
        $orderBy = 't_barang.f_harga DESC';
        break;
    case 'price_asc':
        $orderBy = 't_barang.f_harga ASC';
        break;
    case 'size_asc':
        $orderBy = 't_ukuran.f_ukuran ASC';  
        break;
    case 'size_desc':
        $orderBy = 't_ukuran.f_ukuran DESC';
        break;
    case 'rating':
        $orderBy = 't_barang.f_rating DESC'; 
        break;
    default:
        $orderBy = 't_barang.f_id'; 
        break;
}

$categoryId = isset($_GET['category']) ? intval($_GET['category']) : Null;

if (isset($_GET['id'])) {
    $categoryId = intval($_GET['id']);
    $sql = "SELECT 
                    t_barang.f_id,
                    t_barang.f_pakaian, 
                    t_barang.f_gambar, 
                    t_barang.f_harga, 
                    t_barang.f_rating,
                    t_barang.f_quantity,
                    t_barang.f_detail,
                    t_barang.f_idukuran,  
                    t_barang.f_idkategori,
                    t_kategori.f_kategori,
                    t_ukuran.f_ukuran,    
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
                    t_kategori ON t_barang.f_idkategori = t_kategori.f_id";
                if ($categoryId !== null && $categoryId > 0) {
                    $sql .= " WHERE t_barang.f_idkategori = $categoryId";
                }
                $sql .= " GROUP BY t_barang.f_id
                        ORDER BY $orderBy";
        $products = $db->getALL($sql);


$sql = "SELECT 
f_kategori
FROM t_kategori 
WHERE f_id = $categoryId";
$categoryName = $db->getITEM($sql);

if (isset($_POST['add_to_cart'])) {
        $productId = $_POST['product_id'];
        $quantity = 1; 
        $db->addToCart($user_id, $productId, $quantity);
        echo "<script>alert('Product added to cart!');</script>";
    }
}
?>

<!DOCTYPE html>
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

    <div class="container-2">

        <div class="content-2">
            <div class="categories-menu">
                <h3 class="category-heading">Categories</h3> 
                <ul>
                    <li>
                        <a href="kategori.php?id=0" 
                        class="<?php echo (!isset($_GET['id']) || $_GET['id'] == 0) ? 'active' : ''; ?>">All</a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                        <li>
                            <a href="kategori.php?id=<?php echo urlencode($category['f_id']);?>" 
                            class="<?php echo isset($_GET['id']) && $_GET['id'] == $category['f_id'] ? 'active' : ''; ?>">
                            <?php echo htmlspecialchars($category['f_kategori']); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="container-cardd">
                <div class="page-detail">
                    <h1><?php echo !empty($categoryName['f_kategori']) ? $categoryName['f_kategori'] : "All"; ?></h1>
                    <form method="GET" action="" class="sort-form">
                        <input type="hidden" name="id" value="<?php echo $categoryId; ?>" />
                        <select name="sort" onchange="this.form.submit()" class="sort-select">
                            <option value="''" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == '') ? 'selected' : ''; ?>>Sort By</option>
                            <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price (High to Low)</option>
                            <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price (Low to High)</option>
                            <option value="size_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'size_asc') ? 'selected' : ''; ?>>Size (XL-S)</option>
                            <option value="size_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'size_desc') ? 'selected' : ''; ?>>Size (S-XL)</option>
                            <option value="rating" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'rating') ? 'selected' : ''; ?>>Top Rated</option>
                        </select>
                    </form>
                </div>
                <div class="card-container">
                    <?php if (empty($products)): ?>
                        <h style="font-size: 20px;">No products available. Stay toon, new products are coming soon!!</h>
                    <?php else: ?>
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
                                                <i class="fas fa-star" style="color: rgb(252, 186, 3);"></i>
                                            <?php endfor; ?>
                                            <?php if ($product['f_rating'] < 5): ?>
                                                <?php for ($i = 0; $i < 5 - $product['f_rating']; $i++): ?>
                                                    <i class="far fa-star" style="color: rgb(252, 186, 3);"></i>
                                                <?php endfor; ?>
                                            <?php endif; ?>
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
                    <?php endif; ?>  
                </div>
            </div> 
        </div>
    </div>


<script>
    function toggleWishlist(userId, itemId) {
        const icon = document.getElementById(`heart-icon-${itemId}`);
        const isInWishlist = icon.style.color === "rgb(255, 0, 0)"; // Check if already liked

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

</script>
</body>
</html>