<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;
    $notification='';
    $kategorirow = $db->getALL("SELECT * FROM t_kategori ORDER BY f_kategori ASC");
    $ukuranrow = $db->getALL("SELECT * FROM t_ukuran ORDER BY f_ukuran ASC");
?>



<?php
if (isset($_POST['simpan'])) {
    $idkat = $_POST['kategori'];
    $iduk = $_POST['ukuran'];
    $bar = $_POST['pakaian'];
    $gam = $_FILES['gambar']['name'];
    $temp = $_FILES['gambar']['tmp_name'];
    $har = $_POST['harga'];
    $qu = $_POST['quantity'];
    $desk= $_POST['detail'];

    if (empty($gam)) {
        echo "<h4>Gambar Kosong, Tolong isi Gambar</h4>";
    }else{
        $sql = "INSERT INTO t_barang VALUES (NULL,$idkat, $iduk ,'$bar', '$gam', $har, $qu, NULL ,'$desk')";
        move_uploaded_file($temp, '../public/images/'.$gam);
        $db->runSQL($sql);
        $notification='Barang Berhasil Ditambahkan';
    }
}
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
            <a href="../index.php"><img src="../public/images/logo_nobg2.png" alt="" style="height: 50px; width: 150px;"></a>
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
        <div class="sidebar-buttons">
            <a href="..\kategori\select.php"><button class="sidebar-button">Kategori</button></a>
            <a href="select.php"><button class="sidebar-button">Barang</button></a>
            <a href="..\profile\select.php"><button class="sidebar-button">Profile</button></a>
        </div>
        </div>
    </div>


    <div class="main-content">
        <div class="content-header">
            <h2>Insert Barang</h2>
        </div>

        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select class="form-input" name="kategori" id="">
                        <?php foreach ($kategorirow as $r) : ?>
                            <option value="<?php echo $r['f_id'] ?>"><?php echo $r['f_kategori'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="ukuran">Ukuran</label>
                    <select class="form-input" name="ukuran" id="">
                        <?php foreach ($ukuranrow as $u) : ?>
                            <option value="<?php echo $u['f_id'] ?>"><?php echo $u['f_ukuran'] ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="kategori">Pakaian</label>
                    <input type="text" id="pakaian" name="pakaian" required placeholder="Masukkan pakaian" class="form-input">
                </div>

                <div class="form-group">
                    <label for="gambar">Gambar</label>
                    <input style="padding-top: 14px;" type="file" id="gambar" name="gambar" class="form-input">
                </div>

                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" id="harga" name="harga" required placeholder="Masukkan Harga" class="form-input">
                </div>

                <div class="form-group">
                    <label for="kquantity">Quantity</label>
                    <input type="number" id="ukuran" name="quantity" required placeholder="Masukkan Quantity" class="form-input">
                </div>

                <div class="form-group">
                    <label for="detail">Detail Produk</label>
                    <input type="text" id="ukuran" name="detail" required placeholder="Masukkan Detail Produk" class="form-input">
                </div>
                <div class="form-group">
                    <button type="submit" name="simpan" class="btn-submit">Simpan</button>
                </div>

                <?php if (!empty($notification)): ?>
                        <p class="notification"><?php echo $notification; ?></p> 
                <?php endif; ?>

            </form>
        </div>
    </div>
</body>

</html>


