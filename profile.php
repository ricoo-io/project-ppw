<?php
session_start();

require_once('dbcontroller.php');   
$db = new dbcontroller();

$user_id = $_SESSION['iduser'];

$sql = "SELECT f_nama, email, f_id, f_telp, f_alamat, f_jeniskelamin, f_tgl_lahir, f_poto, f_peran FROM t_user WHERE f_id=$user_id";
$db->runSQL($sql);
$row = $db->getITEM($sql);
$_SESSION['phone'] = $row['f_telp'];
$_SESSION['address'] = $row['f_alamat'];
$_SESSION['gender'] = $row['f_jeniskelamin'];
$_SESSION['dob'] = $row['f_tgl_lahir'];
$_SESSION['poto'] = $row['f_poto'];
$role = $row['f_peran'];


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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['myfile']) && $_FILES['myfile']['error'] === UPLOAD_ERR_OK) {
        $fileName = $_FILES['myfile']['name']; 
        $tempPath = $_FILES['myfile']['tmp_name']; 
        $uploadDir = 'public/images/'; 
        $destinationPath = $uploadDir . $fileName; 

        $sql_update = "UPDATE t_user SET f_poto = '$fileName' WHERE f_id = $user_id";
        $_SESSION['poto'] = $fileName;
        $db->runSQL($sql_update);

        move_uploaded_file($tempPath, $destinationPath);
    }
    header("Location: profile.php");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_search'])) {
    $_SESSION['search'] = trim($_POST['search']);
    header('Location: search.php');
    exit;
}

if (isset($_POST['submit-password'])) {
    $oldPassword = md5($_POST['old-password']);
    $newPassword = md5($_POST['new-password']);
    $confirmPassword = md5($_POST['confirm-password']);

    if ($newPassword !== $confirmPassword) {
        $_SESSION['password_error'] = 'New password and confirmation do not match.';
        $_SESSION['show_popup'] = true; 
    } else {
        $sql = "SELECT f_password FROM t_user WHERE f_id=$user_id";
        $row = $db->getITEM($sql);

        if ($oldPassword !== $row['f_password']) {
            $_SESSION['password_error'] = 'Old password is incorrect.';
            $_SESSION['show_popup'] = true; 
        } else {
            $sql_update = "UPDATE t_user SET f_password='$newPassword' WHERE f_id=$user_id";
            $db->runSQL($sql_update);
            $_SESSION['password_success'] = 'Password updated successfully!';
            unset($_SESSION['show_popup']); 
            unset($_SESSION['password_error']);
        }
    }
    header("Location: profile.php");
    exit();
}

if (isset($_POST['unset']) && $_POST['unset'] == '1') {
    unset($_SESSION['password_error']);
    unset($_SESSION['show_popup']);
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
                    <img src="public/images/<?php echo $_SESSION['poto']; ?>" alt="Profile Picture">
                </div>
                
                <div class="profile-name">
                    <h2><?php echo $_SESSION['name']; ?></h2>
                    <div class="profile-detail">
                        <p><?php echo $_SESSION['email']; ?></p>
                    </div>
                </div>
                <div class="line"></div>
            </div>

            <div class="sidebar-menu">
                <a href="profile.php" class="active">Profile Details</a>
                <a href="wishlist.php">Wishlist</a>
                <a href="Orders.php">My Orders</a>

                <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="kelolaproduk.php">Kelola Produk</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="main-content">
            <h2>Profile Details</h2>
            <div class="profile-section">
                <form method="POST" class="profile-form">
                    <div class="input-group">
                        <label for="email">Email</label>
                        <p><?php echo $_SESSION['email']; ?></p>
                    </div>

                    <div class="input-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" required value="<?php echo $row['f_nama'] ?>">
                    </div>

                    <div class="input-group">
                        <label for="dob">Tanggal Lahir</label>
                        <div class="date"><input type="date" id="dob" name="dob" required value="<?php echo $row['f_tgl_lahir'] ?>"></div>
                    </div>

                    <div class="input-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="tel" id="phone" name="phone" required value="<?php echo $row['f_telp'] ?>">
                    </div>
                        
                    <div class="input-group-gender">
                        <div class="gender">
                            <label for="gender">Jenis Kelamin</label>
                        </div>
                        <div class="option">
                            <div>
                                <input type="radio" id="gender" name="gender" value="Male"<?php if ($row['f_jeniskelamin']=='Male') echo ' checked="checked"';?>>
                                <label for="gender">Male</label>
                            </div>
                            <div>
                                <input type="radio" id="gender2" name="gender" value="Female"<?php if ($row['f_jeniskelamin']=='Female') echo ' checked="checked"';?>>
                                <label for="gender2">Female</label>
                            </div>
                            <div>
                                <input type="radio" id="gender3" name="gender" value="Rather not disclose"<?php if ($row['f_jeniskelamin']=='Rather not disclose') echo ' checked="checked"';?>>
                                <label for="gender3">Rather not disclose</label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="input-group">
                        <label for="loc">Alamat</label>
                        <input type="text" id="loc" name="loc"required value="<?php echo $row['f_alamat'] ?>">
                    </div>

                    <div class="submit">
                        <button type="submit" name="submitprofile">Save Changes</button>
                    </div>
                </form>
                <div class="profile-picture-password">
                    <div class="profile-picture">
                        <Form method="POST" enctype="multipart/form-data">
                            <h2 style="font-size: 24px;">Profile Picture</h2>
        
                            <img src="public/images/<?php echo htmlspecialchars ($row['f_poto']) ?>" alt="<?php echo htmlspecialchars ($row['f_poto']) ?>">
                            <button type="button" class="upload-button">
                                <i class="fas fa-upload"></i> Change
                                <input type="file" id="myfile" name="myfile" onchange="this.form.submit();">
                            </button>
                        </Form>
                    </div>    

                    <div class="password">
                        <button type="button" id="change-password">Change Password</button>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="popup" style="display: <?php echo isset($_SESSION['show_popup']) && $_SESSION['show_popup'] ? 'flex' : 'none'; ?>;">
        <div class="popup-content">
            <h2>Change Password</h2>
            <i class="fas fa-times close"></i>

            <?php if (isset($_SESSION['password_error'])): ?>
            <p class="error-message"><?php echo $_SESSION['password_error']; ?></p>
            <?php endif; ?>

            <form method="POST">
                
                <input type="password" id="old-password" placeholder="Old Password" name="old-password" required >
                <input type="password" id="new-password" placeholder="New Password" name="new-password" required>
                <input type="password" id="confirm-password" placeholder="Confirm New Password" name="confirm-password" required>
                
                <button type="submit" name="submit-password">Save Changes</button>
            </form>

            <form id="unset-session-form" method="POST" style="display:none;">
                <input type="hidden" name="unset" value="1">
            </form>
        </div>  
    </div>


</body>
</html>

<script>
        document.getElementById('change-password').addEventListener('click', function() {
            document.querySelector('.popup').style.display = 'flex';
        });

        document.querySelector('.close').addEventListener('click', function() {
            document.querySelector('.popup').style.display = 'none';
            document.getElementById('unset-session-form').submit();
        });

</script>
