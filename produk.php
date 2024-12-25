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
        
        // Check if adding new quantity would exceed stock
        if (($currentQuantity + $quantity) > $product['f_quantity']) {
            $remaining = $product['f_quantity'] - $currentQuantity;
            if ($remaining <= 0) {
                $notification = "This item is already in your cart with maximum stock!";
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
        <p id="subtotal">Subtotal: Rp <?php echo number_format($product['f_harga'], 0, ',', '.'); ?></p>
        
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
    
<script>
  const productPrice = <?php echo $product['f_harga']; ?>;
  const stock = <?php echo $product['f_quantity']; ?>;
  let quantity = 1;

  // Get current cart quantity for this product
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
    
    // Check if new quantity plus cart quantity would exceed stock
    if (action === 'increase' && (currentCartQuantity + newQuantity) > stock) {
        const remaining = stock - currentCartQuantity;
        if (remaining <= 0) {
            alert('Maximum stock for this item is already in your cart!');
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