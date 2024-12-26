<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;
    session_start();

    $user = $_SESSION['email'];
    if (isset($_GET['log'])) {
        session_destroy();
        header("location:../login.php");
    }

    $kategorirow = $db->getALL("SELECT * FROM t_kategori ORDER BY f_kategori ASC");
    $ukuranrow = $db->getALL("SELECT * FROM t_ukuran ORDER BY f_ukuran ASC");
    $warnarow = $db->getALL("SELECT * FROM t_colors ORDER BY f_colour ASC");
?>

<?php
if (isset($_POST['simpan'])) {
    $idkat = $_POST['kategori'];
    $idukur = $_POST['ukuran'];
    $idwarna = $_POST['warna']; 
    $bar = $_POST['pakaian'];
    $gam = $_FILES['gambar']['name'];
    $temp = $_FILES['gambar']['tmp_name'];
    $har = $_POST['harga'];
    $qu = $_POST['quantity'];
    $ra = $_POST['rating'];
    $desk = $_POST['detail'];

    if (empty($gam)) {
        echo "<h4>Gambar Kosong, Tolong isi Gambar</h4>";
    } else {
        move_uploaded_file($temp, '../public/images/' . $gam);

        $sql_barang = "INSERT INTO t_barang (f_idkategori, f_pakaian, f_gambar, f_harga, f_quantity, f_rating, f_detail) 
                       VALUES ($idkat,'$bar', '$gam', $har, $qu, $ra, '$desk')";
        $db->runSQL($sql_barang);

        $id_barang = $db->getLastInsertId();

        if (!empty($idwarna) && is_array($idwarna)) {
            foreach ($idwarna as $idwar) {
                $sql_color = "INSERT INTO barang_color (f_idbarang, f_idwarna) VALUES ($id_barang, $idwar)";
                $db->runSQL($sql_color);
            }
        }

        if (!empty($idukur) && is_array($idukur)) {
            foreach ($idukur as $iduk) {
                $sql_ukuran = "INSERT INTO barang_ukuran (f_idbarang, f_idukuran) VALUES ($id_barang, $iduk)";
                $db->runSQL($sql_ukuran);
            }
        }

        header("Location: select.php");
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Product - SB Admin</title>
        <link rel="icon" href="public/images/logo2.png" type="image/gif" sizes="16x16">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="../css/styless.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Navbar Brand-->
            <a class="navbar-brand ps-2" href="..\kelolaproduk.php"><img src="../public/images/logobaru.png" alt="" style="height: 48px;"></a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
           
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-8 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="?log=logout">Logout</a></li>
                        <li><a class="dropdown-item" href="../index.php">Halaman Utama</a></li>
                    </ul>
                </li>
            </ul>
        </nav>

        <div id="layoutSidenav">
            <div id="layoutSidenav_nav">
                <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                    <div class="sb-sidenav-menu">
                        <div class="nav">
                            <div class="sb-sidenav-menu-heading">Core</div>
                            <a class="nav-link" href="..\kelolaproduk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <a class="nav-link" href="../kategori/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-list"></i></div>
                                Category
                            </a>
                            <a class="nav-link" href="select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-box"></i></div>
                                Product
                            </a>

                            <a class="nav-link" href="../profile/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                User
                            </a>

                            <a class="nav-link" href="../order/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                                Order
                            </a>

                            <a class="nav-link" href="../orderdetail/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-luggage-cart"></i></div>
                                Detail Orders
                            </a>

                            <a class="nav-link" href="../diskon/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tag"></i></div>
                                Diskon
                            </a>
                            
                        </div>
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-5">
                        <h1 class="mt-4">Insert Product</h1>

                        <div class="form-container">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="row">
                                <div class="form-group col-md-6">
                                    <label for="kategori">Kategori</label>
                                    <select class="form-input" name="kategori" id="">
                                        <?php foreach ($kategorirow as $r) : ?>
                                            <option value="<?php echo $r['f_id'] ?>"><?php echo $r['f_kategori'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="warna">Ukuran</label>
                                    <select class="form-input" name="ukuran[]" id="" multiple>
                                        <?php foreach ($ukuranrow as $u) : ?>
                                            <option value="<?php echo $u['f_id'] ?>"><?php echo $u['f_ukuran'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="warna">Warna</label>
                                    <select class="form-input" name="warna[]" id="" multiple>
                                        <?php foreach ($warnarow as $w) : ?>
                                            <option value="<?php echo $w['f_id'] ?>"><?php echo $w['f_colour'] ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="kategori">Pakaian</label>
                                    <input type="text" id="pakaian" name="pakaian" required placeholder="Masukkan pakaian" class="form-input">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="gambar">Gambar</label>
                                    <input style="padding-top: 14px;" type="file" id="gambar" name="gambar" class="form-input">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="harga">Harga</label>
                                    <input type="number" id="harga" name="harga" required placeholder="Masukkan Harga" class="form-input">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="kquantity">Quantity</label>
                                    <input type="number" id="ukuran" name="quantity" required placeholder="Masukkan Quantity" class="form-input">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="rating">Rating Product</label>
                                    <input type="number" id="rate" name="rating" required placeholder="Masukkan Rating" class="form-input">
                                </div>
                                </div>

                                <div class="form-group">
                                    <label for="detail">Detail Produk</label>
                                    <input type="text" id="ukuran" name="detail" required placeholder="Masukkan Detail Produk" class="form-input">
                                </div>
                                <div class="form-group">
                                    <button type="submit" name="simpan" class="btn-submit">Simpan</button>
                                </div>

                            </form>
                        </div>
                    </div>
                </main>

                <footer class="py-4 bg-light mt-auto">
                    <div class="container-fluid px-4">
                        <div class="d-flex align-items-center justify-content-between small">
                            <div class="text-muted">Copyright &copy; KnowDays PPW 2024</div>
                            <div>
                                <a href="#">Privacy Policy</a>
                                &middot;
                                <a href="#">Terms &amp; Conditions</a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="../js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="../js/datatables-simple-demo.js"></script>
    </body>
</html>
