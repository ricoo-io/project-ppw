<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;

    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    
        $sql = "SELECT * FROM t_barang WHERE f_id=$id";
    
        $item = $db->getITEM($sql);
    
        $idk = $item['f_idkategori'];
        $iduk = $item['f_idukuran'];
        $gambarlama = $item['f_gambar'];
    }
    $katrow = $db->getALL("SELECT * FROM t_kategori ORDER BY f_kategori ASC");
    $ukrow = $db->getALL("SELECT * FROM t_ukuran ORDER BY f_ukuran ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Layout</title>
    <link rel="stylesheet" href="../css/kelola.css">
    <link rel="icon" href="../public/images/logo2.png" type="image/gif" sizes="16x16">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav>
        <div class="img">
            <a href="#"><img src="../public/images/logo_nobg2.png" alt="" style="height: 50px; width: 150px;"></a>
        </div>
        
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="../cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="../logout.php" ><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <li><a href="../profile.php" class="profile-icon"></a></li>
        </ul>
        
    </nav>

    <div class="sidebar">
        <div class="sidebar-buttons">
            <a href="..\kategori\select.php"><button class="sidebar-button">Kategori</button></a>
            <a href="select.php"><button class="sidebar-button">Barang</button></a>
            <a href="..\profile\select.php"><button class="sidebar-button">Profile</button></a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h2>Update Barang</h2>
        </div>

        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select class="form-input" name="kategori" id="">
                        <?php foreach ($katrow as $r) : ?>
                            <option <?php if ($r['f_id'] == $idk) {
                                echo "selected";
                            } ?> value="<?php echo $r['f_id'] ?>"><?php echo $r['f_kategori'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ukuran">Ukuran</label>
                    <select class="form-input" name="ukuran" id="">
                        <?php foreach ($ukrow as $u) : ?>
                            <option <?php if ($r['f_id'] == $iduk) {
                                echo "selected";
                            } ?> value="<?php echo $u['f_id'] ?>"><?php echo $u['f_ukuran'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pakaian">Pakaian</label>
                    <input type="text" id="pakaian" name="pakaian" required value="<?php echo $item['f_pakaian'] ?>" class="form-input">
                </div>

                <div class="form-group">
                    <label for="kategori">Gambar</label>
                    <input style="padding-top: 14px;" type="file" id="gambar" name="gambar" class="form-input">
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" required value="<?php echo $item['f_harga'] ?>" class="form-input">
                </div>

                <div class="form-group">
                    <label for="kquantity">Quantity</label>
                    <input type="number" id="ukuran" name="quantity" required value="<?php echo $item['f_quantity'] ?>" class="form-input">
                </div>

                <div class="form-group">
                    <label for="detail">Detail Produk</label>
                    <input type="text" id="ukuran" name="detail" required value="<?php echo $item['f_detail'] ?>" class="form-input">
                </div>

                <div class="form-group">
                    <button type="submit" name="simpan" class="btn-submit">Update</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
if (isset($_POST['simpan'])) {
    $idkat = $_POST['kategori'];
    $iduku = $_POST['ukuran'];
    $bar = $_POST['pakaian'];
    $gam = $item['gambar'];
    $temp = $_FILES['gambar']['tmp_name'];
    $har = $_POST['harga'];
    $qu = $_POST['quantity'];
    $desk= $_POST['detail'];

    if (!empty($temp)) {
        $gam = $_FILES['gambar']['name'];
        move_uploaded_file($temp, '../public/images/'.$gam);
    }
    $sql = "UPDATE t_barang SET f_idkategori=$idkat, f_idukuran=$iduku,f_pakaian='$bar', f_gambar='$gam', 
        f_harga=$har, f_quantity=$qu, f_detail='$desk' WHERE f_id='$id'";
    $db->runSQL($sql);

    echo "<script> window.location.assign('select.php'); </script>";
}
?>
