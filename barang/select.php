<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;
    session_start();

    $user = $_SESSION['email'];
    if (isset($_GET['log'])) {
        session_destroy();
        header("location:login.php");
    }

    $jumlahdata = $db->rowCOUNT("SELECT f_id FROM t_barang");
    $banyak = 20;
    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT DISTINCT t_barang.f_id AS id_barang, t_kategori.f_kategori AS kategori,
            t_barang.f_pakaian AS pakaian, t_barang.f_gambar AS gambar, t_barang.f_harga AS harga, 
            t_barang.f_quantity AS quantity,t_barang.f_rating AS rating,
            t_barang.f_detail AS detail,
            GROUP_CONCAT(DISTINCT t_colors.f_colour SEPARATOR ', ') AS warna,
            GROUP_CONCAT(DISTINCT t_ukuran.f_ukuran SEPARATOR ', ') AS ukuran
            FROM t_barang
            INNER JOIN t_kategori ON t_barang.f_idkategori = t_kategori.f_id
            LEFT JOIN barang_color ON t_barang.f_id = barang_color.f_idbarang
            LEFT JOIN t_colors ON barang_color.f_idwarna = t_colors.f_id
            LEFT JOIN barang_ukuran ON t_barang.f_id = barang_ukuran.f_idbarang
            LEFT JOIN t_ukuran ON barang_ukuran.f_idukuran = t_ukuran.f_id
            GROUP BY t_barang.f_id
            ORDER BY t_barang.f_id DESC LIMIT $mulai, $banyak";
    $row = $db->getALL($sql);
    $no = 1 + $mulai;
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
            <a class="navbar-brand ps-2" href="..\index.php"><img src="../public/images/logobaru.png" alt="" style="height: 48px;"></a>
            <!-- Sidebar Toggle-->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
           
            <!-- Navbar-->
            <ul class="navbar-nav ms-auto ms-md-8 me-3 me-lg-4">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="?log=logout">Logout</a></li>
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

                            <a class="nav-link" href="../diskon/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-tag"></i></div>
                                Discount
                            </a>
                            
                        </div>
                    </div>
                </nav>
            </div>
            
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Product</h1>

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item">Product</li>
                            <li class="breadcrumb-item active">Select</li>
                        </ol>

                        <div class="button mb-2">
                            <a href="insert.php"><button type="button" class="btn btn-outline-primary">Insert</button></a>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Product
                            </div>
                            
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Kategori</th>
                                            <th>Ukuran</th>
                                            <th>Product</th>
                                            <th>Gambar</th>
                                            <th>Harga</th>
                                            <th>Quantity</th>
                                            <th>Rating</th>
                                            <th>Detail</th>
                                            <th>Warna</th>
                                            <th>Update</th>
                                            <th>Delete</th>
                                            
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                    
                                    <?php if(!empty($row)) { ?>
                                        <?php foreach ($row as $r) : ?>
                                            <tr>
                                                <td><?php echo $no++?></td>
                                                <td><?php echo $r['kategori'] ?></td>
                                                <td><?php echo $r['ukuran'] ?></td>
                                                <td><?php echo $r['pakaian'] ?></td>
                                                <td><img style="width:85px" src="../public/images/<?php echo $r['gambar'] ?>" alt=""></td>
                                                <td><?php echo $r['harga'] ?></td>
                                                <td><?php echo $r['quantity'] ?></td>
                                                <td><?php echo $r['rating'] ?></td>
                                                <td><?php echo $r['detail'] ?></td>
                                                <td><?php echo $r['warna'] ?></td>
                                                <td><a href="update.php?id=<?php echo $r['id_barang']; ?>"><i class="fas fa-edit"></i></a></td>
                                                <td><a href="delete.php?id=<?php echo $r['id_barang']; ?>"><i class="fas fa-trash"></i></a></td>
                                            </tr>
                                        <?php endforeach ?>
                                    <?php } ?>
                                        
                                    </tbody>
                                </table>

                            </div>
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
