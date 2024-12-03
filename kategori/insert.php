<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;
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
            <a href="#"><img src="../public/images/logo_nobg2.png" alt="" style="height: 50px; "></a>
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
            <a href="select.php"><button class="sidebar-button">Kategori</button></a>
            <a href="../barang/select.php"><button class="sidebar-button">Barang</button></a>
            <a href="../profile/select.php"><button class="sidebar-button">Profile</button></a>
        </div>
    </div>


    <div class="main-content">
        <div class="content-header">
            <h2>Insert Kategori</h2>
        </div>

        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <input type="text" id="kategori" name="kategori" required placeholder="Masukkan kategori" class="form-input">
                </div>

                <div class="form-group">
                    <button type="submit" name="simpan" class="btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

<?php
if (isset($_POST['simpan'])) {
    $kat = $_POST['kategori'];

    $sql = "INSERT INTO t_kategori VALUES (NULL,'$kat',NULL)";
    $db->runSQL($sql);

    header("Location: select.php");
    exit();
}
?>
