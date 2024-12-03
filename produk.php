<?php
session_start();
require_once "dbcontroller.php"; 

$user_id = $_SESSION['iduser'];
$notification ='';
if (isset($_GET['id'])) {
    $productId = intval($_GET['id']);

    $db = new dbcontroller();
   
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
                t_kategori ON t_barang.f_idkategori = t_kategori.f_id 
            WHERE 
                t_barang.f_id = $productId
            GROUP BY 
                t_barang.f_id";
        $product = $db->getITEM($sql);

        if (!$product) {
            echo "Product not found.";
            exit;
        }
    } else {
        echo "No product selected.";
        exit;
    }

    if (isset($_POST['add_to_cart'])) {
      $productId = $_POST['product_id'];
      $quantity = 1; 
      $db->addToCart($user_id, $productId, $quantity);
      $notification = "Product added to cart!";
  }
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($product['f_pakaian']); ?></title>
  <link rel="stylesheet" href="css/produk.css">
  <link rel="stylesheet" href="css/index.css">
  <link rel="icon" href="public\images\logo.png" type="image/gif" sizes="16x16">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

  <header class="header">
    <nav>
      <div class="img">
          <a href="index.php"><img src="public/images/logo_nobg.png" alt="" style="height: 50px;"></a>
      </div>

      <div class="search-container">
          <input type="text" placeholder="Search..">
          <i class="fas fa-search"></i>
      </div>
      
      <ul>
          <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
          <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
          <li><a href="profile.php"><i class="fas fa-user"></i> User</a></li>
          <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="cart-wrapper">
    <div class="product-details">
      <div class="product-image">
        <img src="public/images/<?php echo htmlspecialchars($product['f_gambar']); ?>" alt="Gambar Item" />
      </div>
      <div class="product-info">
        <h4><?php echo htmlspecialchars($product['f_pakaian']); ?></h4>
          <div class="rattingprice">
              <div class="ratting">
                  <?php for ($i = 0; $i < $product['f_rating']; $i++): ?>
                      <i class="fas fa-star"></i>
                  <?php endfor; ?>
              </div>
          </div>
          <p style="font-weight: bold; font-size: 24px;padding-top: 12px;color: black;">Rp<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
        
        <div class="options">
            <div class="option">
              <label>Kategori:</label>
              <div class="option-buttons">
                <button><?php echo htmlspecialchars($product['f_kategori']);?></button>
              </div>
            </div>  
            <div class="option">
              <label>Pilihan Warna:</label>
                <div class="color-product">
                    <?php 
                    $colorImages = explode(',', $product['colors']);
                    foreach ($colorImages as $colorImage): 
                    ?>
                        <img src="public/images/<?php echo htmlspecialchars(trim($colorImage)); ?>" alt="Color Image">
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="option">
              <label>Ukuran:</label>
              <div class="option-buttons">
                <button><?php echo htmlspecialchars($product['f_ukuran']);?></button>
              </div>
            </div>
        </div>          
        <p style="font-weight: bold;">Detail Produk</p>
        <p><?php echo $product['f_detail']; ?></p>
      </div>
    </div>

    <div class="cart-summary">
      <h3>Atur Jumlah dan Catatan</h3>
      <div class="quantity-controls">
        <button>-</button>
        <span>1</span>
        <button>+</button>
      </div>
      <p>Subtotal: Rp <?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
      <form method="POST" action="">
          <input type="hidden" name="product_id" value="<?php echo $product['f_id']; ?>">
          <button type="submit" name="add_to_cart" class="checkout-btn">
              <i class="fas fa-shopping-cart"></i> + Add to Cart
          </button>
      </form>
      <button class="buy-now-btn">Beli Langsung</button>
      
      <?php if (!empty($notification)): ?>
        <p class="notification"><?php echo $notification; ?></p> 
        <meta http-equiv="refresh" content ="2; url=cart.php"/>
      <?php endif; ?>


      <div class="footer-icons">
          <span>💬 Chat</span>
          <span>🤍 Wishlist</span>
          <span>🔗 Bagikan</span>
      </div>
    </div>
  </div>

</body>
</html>