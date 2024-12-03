<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;

    $jumlahdata = $db->rowCOUNT("SELECT f_id FROM t_kategori");
    $banyak = 15;
    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT * FROM t_kategori ORDER BY f_id DESC LIMIT $mulai, $banyak";
    $row = $db->getALL($sql);
    $no = 1 + $mulai;
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
            <a href="../index.php"><img src="../public/images/logo_nobg2.png" alt="" style="height: 50px; "></a>
        </div>
        
        <ul>
            <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="../cart.php"><i class="fas fa-shopping-cart"></i> Cart</a></li>
            <li><a href="../logout.php" ><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            <li><a href="../profile.php"><i class="fas fa-user"></i> User</a></li>
        </ul>
        
    </nav>

    <div class="sidebar">
        <div class="sidebar-buttons">
            <a href="select.php"><button class="sidebar-button">Kategori</button></a>
            <a href="..\barang\select.php"><button class="sidebar-button">Barang</button></a>
            <a href="..\profile\select.php"><button class="sidebar-button">Profile</button></a>
        </div>
    </div>

    <div class="main-content">
        <div class="content-header">
            <h2>Kategori</h2>
            <button class="insert-button"><a href="insert.php">Insert</a></button>
        </div>

        <table class="tableskategori" style="width: 70%;">
            <thead>
                <tr>
                    <td>No</td>
                    <td>Kategori</td>
                    <td>Update</td>
                    <td>Delete</td>
                </tr>
            </thead>

            <tbody>
                <?php if(!empty($row)) { ?>
                    <?php foreach ($row as $r) : ?>
                        <tr>
                            <td><?php echo $no++?></td>
                            <td><?php echo $r['f_kategori'] ?></td>
                            <td><a href="update.php?id=<?php echo $r['f_id']; ?>"><i class="fas fa-edit"></i></a></td>
                            <td><a href="delete.php?id=<?php echo $r['f_id']; ?>"><i class="fas fa-trash"></i></a></td>
                        </tr>
                    <?php endforeach ?>
                <?php } ?>
            </tbody>
        </table>

    </div>

</body>
</html>
