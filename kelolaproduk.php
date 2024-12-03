<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Layout</title>
    <link rel="stylesheet" href="css/kelola.css">
    <link rel="icon" href="public/images/logo2.png" type="image/gif" sizes="16x16">
    <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>

<body>
    <nav>
        <div class="img">
            <a href="index.php"><img src="public\images\logo_nobg.png" alt="" style="height: 45px; "></a>
        </div>
        
        <ul>
            <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="logout.php" ><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <li><a href="profile.php"><i class="fas fa-user"></i> User</a></li>
        </ul>
        
    </nav>

    <div class="sidebar">
        <div class="sidebar-buttons">
            <a href="kategori/select.php"><button class="sidebar-button">Kategori</button></a>
            <a href="barang/select.php"><button class="sidebar-button">Barang</button></a>
            <a href="profile/select.php"><button class="sidebar-button">Profile</button></a>
        </div>
    </div>
    

    <div class="main-content">
        <div class="content-header">
            <h2>Selamat Datang di Halaman Kelola Produk (i ilove johar)</h2>
        </div>

    </div>
</body>
</html>