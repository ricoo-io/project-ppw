<?php
session_start();

require_once('dbcontroller.php');   
$db = new dbcontroller();

$user_id = $_SESSION['iduser'];

$sql = "SELECT f_nama, email, f_id, f_telp, f_alamat, f_jeniskelamin, f_tgl_lahir FROM t_user WHERE f_id=$user_id";
$db->runSQL($sql);
$row = $db->getITEM($sql);
$_SESSION['phone'] = $row['f_telp'];
$_SESSION['address'] = $row['f_alamat'];
$_SESSION['gender'] = $row['f_jeniskelamin'];
$_SESSION['dob'] = $row['f_tgl_lahir'];

if (isset($_POST['submitprofile'])) {
    $name = $_POST['username'];
    // $email = $_POST['email'];
    $telp = $_POST['phone'];
    $alm = $_POST['loc'];
    $jns = $_POST['gender'];
    $tgl = $_POST['dob'];

    $sql_update = "UPDATE t_user SET f_nama='$name', f_telp='$telp', f_alamat='$alm', f_jeniskelamin='$jns', f_tgl_lahir='$tgl' WHERE f_id=$user_id";
    $db->runSQL($sql_update);

    $_SESSION['name'] = $name;
    // $_SESSION['email'] = $email;
    $_SESSION['phone'] = $telp; 
    $_SESSION['address'] = $alm;
    $_SESSION['gender'] = $jns; 
    $_SESSION['dob'] = $tgl;

    header("Location: profile.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
    $_SESSION['search'] = trim($_POST['search']);
    header('Location: search.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/profile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">
    <link rel="icon" href="public/images/logo2.png" type="image/gif" sizes="16x16">
    <title>KnowDays | Profile</title> 
</head>

<body>
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

    <div class="container"> 
        <div class="sidebar">
            <div class="profile-container">
                <div class="profile-header">
                    <img src="public/images/contoh.png" alt="Profile Photo" class="profile-photo">
                </div>
                
                <div class="profile-name">
                    <h2><?php echo $_SESSION['name']; ?></h2>
                    <div class="profile-detail">
                        <p><?php echo $_SESSION['email']; ?></p>
                    </div>
                </div>
    
                <div class="tengah">
                    <h3>Profile Details</h3>
                    <div class="profile-detail">
                        <?php if (!empty($_SESSION['phone'])): ?>
                            <p><strong>Phone:</strong> <?php echo $_SESSION['phone']; ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['address'])): ?>
                            <p><strong>Address:</strong> <?php echo $_SESSION['address']; ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['gender'])): ?>
                            <p><strong>Gender:</strong> <?php echo $_SESSION['gender']; ?></p>
                        <?php endif; ?>
                        
                        <?php if (!empty($_SESSION['dob'])): ?>
                            <p><strong>Date of Birth:</strong> <?php echo $_SESSION['dob']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if ($_SESSION['email'] == 'admin@gmail.com'): ?>
                        <a href="kelolaproduk.php">Kelola Produk</a>
                <?php endif; ?>

            </div>
        </div>

        <div class="main-content">
            <div class="profile-section">
                <h2>Profil Saya</h2>
                <form method="POST">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required value="<?php echo $row['f_nama'] ?>">
                    
                    <!-- <label for="email">Email</label>
                    <input type="email" id="email" name="email"> -->
                    
                    <label for="phone">Nomor Telepon</label>
                    <input type="tel" id="phone" name="phone" required value="<?php echo $row['f_telp'] ?>">
                    
                    <label for="loc">Alamat</label>
                    <input type="text" id="loc" name="loc"required value="<?php echo $row['f_alamat'] ?>">
                    
                    <label for="gender">Jenis Kelamin</label>
                    <input type="text" id="gender" name="gender"required value="<?php echo $row['f_jeniskelamin'] ?>">
                    
                    <label for="dob">Tanggal Lahir</label>
                    <input type="date" id="dob" name="dob" required value="<?php echo $row['f_tgl_lahir'] ?>">
                    
                    <button type="submit" name="submitprofile">Simpan</button>
                </form>
            </div>
        </div>
        <!-- <div class="profile-picture">
                <img src="public/images/contoh.png" alt="Profile Picture">
                <input type="file" id="upload" name="Pilih Gambar">
        </div> -->
    </div>
</body>
</html>
