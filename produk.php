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
            t_barang.f_diskon, 
            t_barang.f_rating,
            t_barang.f_quantity,
            t_ukuran.f_ukuran,    
            t_barang.f_detail,
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
        $quantity = intval($_POST['quantity']);
        $size = $_POST['size'];
        $color = $_POST['color'];
        
        $sql = "SELECT SUM(f_quantity) as current_quantity 
                FROM t_cart 
                WHERE f_iduser = $user_id 
                AND f_idbarang = $productId";
        $result = $db->getITEM($sql);
        $currentQuantity = $result['current_quantity'] ?? 0;
        
        if (($currentQuantity + $quantity) > $product['f_quantity']) {
            $remaining = $product['f_quantity'] - $currentQuantity;
            if ($remaining <= 0) {
                $notification = "Sorry, this item is out of stock!";
            } else {
                $notification = "Can only add {$remaining} more of this item with selected size and color!";
            }
        } else {
            $db->addToCart($user_id, $productId, $quantity, $size, $color);
            $notification = "Product added to cart!";
        }
    }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
  $_SESSION['search'] = trim($_POST['search']);
  header('Location: search.php');
  exit;
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
  </header>

  <div class="container3">
    <div class="cart-wrapper">
      <div class="product-details">
        <div class="product-image">
          <img src="public/images/<?php echo htmlspecialchars($product['f_gambar']); ?>" alt="Gambar Item" />
        </div>
        <div class="product-info">
          <h4><?php echo htmlspecialchars($product['f_pakaian']); ?></h4>

            <div class="discount-price">
                <?php if ($product['f_diskon'] > 0): ?>
                    <span class="original-price">Rp<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></span>
                    <span class="discounted-price">Rp<?php echo number_format($product['f_harga'] * (1 - $product['f_diskon']/100), 0, ',', '.'); ?></span>
                    <span class="discount-badge-2">-<?php echo $product['f_diskon']; ?>%</span>
                <?php else: ?>
                    <span class="discounted-price">Rp<?php echo number_format($product['f_harga'], 0, ',', '.'); ?></span>
                <?php endif; ?>
            </div>
          
          <div class="options">
              <div class="option">
                <label>Kategori:</label>
                <div class="option-buttons">
                  <button><?php echo htmlspecialchars($product['f_kategori']);?></button>
                </div>
              </div>  
          </div>          
          <p style="font-weight: bold;">Detail Produk</p>
          <p><?php echo $product['f_detail']; ?></p>
        </div>
      </div>

      <div class="cart-summary">
        <form method="POST" action="">
          <div class="options">
              <div class="option">
                  <label>Pilihan Warna:</label>
                  <div class="color-product2">
                      <?php 
                      $colorImages = explode(',', $product['colors']);
                      foreach ($colorImages as $colorImage): 
                      ?>
                          <label>
                              <input type="radio" name="color" value="<?php echo htmlspecialchars(trim($colorImage)); ?>" required>
                              <img src="public/images/<?php echo htmlspecialchars(trim($colorImage)); ?>" alt="Color Image">
                          </label>
                      <?php endforeach; ?>
                  </div>
              </div>
              <div class="option">
                  <label>Ukuran:</label>
                  <div class="option-buttons">
                      <?php 
                      $sizes = explode(',', $product['ukuran']);
                      foreach ($sizes as $ukuran): 
                      ?>
                          <label class="size-label">
                              <input type="radio" name="size" value="<?php echo htmlspecialchars(trim($ukuran)); ?>" required>
                              <span class="size-text"><?php echo htmlspecialchars(trim($ukuran)); ?></span>
                          </label>
                      <?php endforeach; ?>
                  </div>
              </div>
          </div>
          
          <div class="quantity-controls">
            <button onclick="updateQuantity('decrease')" class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-full hover:bg-gray-300">
                <span class="text-xl font-semibold">-</span>
            </button>
              <span id="quantity" class="text-lg text-gray-800">1</span>
            <button onclick="updateQuantity('increase')"
              class="w-8 h-8 flex items-center justify-center bg-gray-200 rounded-full hover:bg-gray-300">
                <span class="text-xl font-semibold">+</span>
            </button>

          </div>
          <p id="subtotal">Subtotal: Rp <?php echo number_format($product['f_diskon'] > 0 ? $product['f_harga'] * (1 - $product['f_diskon']/100) : $product['f_harga'], 0, ',', '.'); ?></p>
          
              <input type="hidden" name="product_id" value="<?php echo $product['f_id']; ?>">
              <input type="hidden" id="cart_quantity" name="quantity" value="1">
              <button type="submit" name="add_to_cart" class="checkout-btn">
                  <i class="fas fa-shopping-cart"></i> + Add to Cart
              </button>
        </form>
        <button class="buy-now-btn">Beli Langsung</button>
        
        <?php if (!empty($notification)): ?>
          <p class="notification"><?php echo $notification; ?></p> 
          <meta http-equiv="refresh" content ="2; url=cart.php"/>
        <?php endif; ?>

      </div>
    </div>

    <div class="review-container">
      <?php $sql = "SELECT AVG(f_rating) as avg_rating FROM t_review WHERE f_idbarang = $productId";
      $result = $db->getITEM($sql);
      $avgRating = $result['avg_rating'] ?? 0;
      $sql = "SELECT COUNT(f_id) as total_reviews FROM t_review WHERE f_idbarang = $productId";
      $result = $db->getITEM($sql);
      $totalReviews = $result['total_reviews'] ?? 0;
      ?>
        <h1>Product Reviews</h1>
        <div class="overall-rating">
            <span class="rating-value"><?php echo number_format($avgRating, 1); ?>/5</span>                    
            <span class="review-count">(<?php echo $totalReviews; ?> reviews)</span>
        </div>

        <?php $sql="SELECT t_review.*, t_user.f_nama, t_user.f_poto 
                    FROM 
                      t_review 
                    JOIN 
                      t_user ON t_review.f_iduser = t_user.f_id 
                    WHERE 
                      t_review.f_idbarang = $productId
                    ORDER BY 
                      t_review.f_tanggal DESC";
        $reviews = $db->getALL($sql);
        ?>
        <div class="review-section">
          <?php if (empty($reviews)): ?>
            <p>No reviews yet.</p>
          <?php else: ?>
            <?php foreach ($reviews as $review): ?>
              <div class="review">
                  <div class="profile-review">
                    <img src="public/images/<?php echo $review['f_poto']; ?>" alt="User Profile Picture">
                    <div>
                      <div class="username"><?php echo $review['f_nama']; ?></div>
                      <div class="rating">
                        <span class="stars"><?php echo str_repeat('★', $review['f_rating']); ?></span> 
                        <span class="rating-value"><?php echo $review['f_rating']; ?>/5</span>
                      </div>
                    </div>
                  </div>
                  
                  <div class="date">Reviewed on: <?php echo $review['f_tanggal']; ?></div>
                  <div class="comment"><?php echo $review['f_review']; ?>.</div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>

        </div>
    </div>

  </div>  
<script>
  const productPrice = <?php echo $product['f_diskon'] > 0 ? 
    $product['f_harga'] * (1 - $product['f_diskon']/100) : 
    $product['f_harga']; ?>;
  const stock = <?php echo $product['f_quantity']; ?>;
  let quantity = 1;

  const currentCartQuantity = <?php 
    $sql = "SELECT SUM(f_quantity) as qty FROM t_cart 
            WHERE f_iduser = $user_id 
            AND f_idbarang = $productId";
    $result = $db->getITEM($sql);
    echo $result['qty'] ?? 0;
  ?>;

  function updateQuantity(action) {
    event.preventDefault();
    const quantityElement = document.getElementById('quantity');
    const subtotalElement = document.getElementById('subtotal');
    const cartQuantityField = document.getElementById('cart_quantity');

    let newQuantity = parseInt(quantityElement.textContent) + (action === 'increase' ? 1 : -1);
    
    if (action === 'increase' && (currentCartQuantity + newQuantity) > stock) {
        const remaining = stock - currentCartQuantity;
        if (remaining <= 0) {
            alert('Sorry, this item is out of stock!');
        } else {
            alert(`Can only add ${remaining} more of this item!`);
        }
        return;
    }

    if (newQuantity < 1) newQuantity = 1;
    if (newQuantity > stock) {
        alert('Cannot exceed available stock!');
        newQuantity = stock;
    }

    quantityElement.textContent = newQuantity;
    subtotalElement.textContent = `Subtotal: Rp ${(newQuantity * productPrice).toLocaleString('id-ID', { minimumFractionDigits: 0 })}`;
    cartQuantityField.value = newQuantity;
  }
</script>

</body>
</html>