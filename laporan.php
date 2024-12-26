<?php
    
    require_once "dbcontroller.php";
    $db = new dbcontroller;
    session_start();

    $user = $_SESSION['email'];
    if (isset($_GET['log'])) {
        session_destroy();
        header("location:login.php");
    }

    $queryCategorySales = "
                        SELECT t_kategori.f_kategori AS kategori, 
                            SUM(t_orderdetails.f_quantity) AS total_terjual
                        FROM t_orderdetails
                        JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                        JOIN t_kategori ON t_barang.f_idkategori = t_kategori.f_id
                        JOIN t_orders ON t_orderdetails.f_idorder = t_orders.f_id
                        WHERE MONTH(t_orders.f_tanggal_pembelian) = MONTH(CURRENT_DATE()) 
                        AND YEAR(t_orders.f_tanggal_pembelian) = YEAR(CURRENT_DATE())
                        GROUP BY t_kategori.f_kategori";
                        $categorySales = $db->getALL($queryCategorySales);

    $queryProductSales = "SELECT t_barang.f_pakaian AS produk, SUM(t_orderdetails.f_quantity) AS jumlah_terjual, 
                            SUM(t_orderdetails.f_quantity * t_orderdetails.f_harga) AS total_pendapatan
                            FROM t_orderdetails
                            JOIN t_orders ON t_orderdetails.f_idorder = t_orders.f_id
                            JOIN t_barang ON t_orderdetails.f_idbarang = t_barang.f_id
                            WHERE MONTH(t_orders.f_tanggal_pembelian) = MONTH(CURRENT_DATE()) 
                            AND YEAR(t_orders.f_tanggal_pembelian) = YEAR(CURRENT_DATE())
                            GROUP BY t_barang.f_pakaian";
                            $productSalesData = $db->getALL($queryProductSales);
                            $productSales = $productSalesData['product_sales'] ?? 0;
    
    $queryOrderStatus = " SELECT t_orders.f_status AS status_pesanan, COUNT(t_orders.f_id) AS jumlah_pesanan,
                        SUM(t_orders.f_total_harga) AS total_pendapatan FROM t_orders
                        GROUP BY t_orders.f_status";
                        $orderStatusData = $db->getALL($queryOrderStatus);


?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Laporan - SB Admin</title>
        <link rel="icon" href="public/images/logo2.png" type="image/gif" sizes="16x16">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styless.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>

    <body class="sb-nav-fixed">
        
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <a class="navbar-brand ps-2" href="kelolaproduk.php"><img src="public/images/logobaru.png" alt="" style="height: 48px;"></a>
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>
            <ul class="navbar-nav ms-auto ms-md-8 me-3 me-lg-4">
                <a class="nav-link" id="navbarDropdown" href="laporan.php" role="button"><i class="far fa-file-excel"></i> Laporan</a>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="?log=logout">Logout</a></li>
                        <li><a class="dropdown-item" href="index.php">Halaman Utama</a></li>
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
                            
                            <a class="nav-link" href="kelolaproduk.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                                Dashboard
                            </a>
                            
                            <a class="nav-link" href="kategori/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-list"></i></div>
                                Category
                            </a>
                            <a class="nav-link" href="barang/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-box"></i></div>
                                Product
                            </a>

                            <a class="nav-link" href="profile/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                User
                            </a>

                            <a class="nav-link" href="order/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-cart-shopping"></i></div>
                                Order
                            </a>

                            <a class="nav-link" href="orderdetail/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-luggage-cart"></i></div>
                                Detail Orders
                            </a>

                            <a class="nav-link" href="diskon/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-tag"></i></div>
                                Diskon
                            </a>
                            
                        </div>
                    </div>
                    <div class="sb-sidenav-footer">
                        <div class="small">Logged in as: <span><?php echo $_SESSION['email'] ?></span></div>
                    </div>
                </nav>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Laporan</h1>
                        
                        <li class="list-group-item mt-4">Kategori Sales: 
                            <table class="table table-bordered w-100">
                                <tr class="table-info">
                                    <th>Kategori</th>
                                    <th>Total Terjual</th>
                                </tr>

                                <?php if (!empty($categorySales)) { ?>

                                    <?php foreach ($categorySales as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['kategori']; ?></td>
                                            <td><?php echo $row['total_terjual']; ?></td>
                                        </tr>
                                    <?php } ?>

                                <?php } ?>

                            </table>
                        </li>

                        <li class="list-group-item mt-4">Produk Sales: 
                            <table class="table table-bordered w-100">
                                <tr class="table-info">
                                    <th>Produk</th>
                                    <th>Jumlah Terjual</th>
                                    <th>Total Pendapatan</th>
                                </tr>

                                <?php if (!empty($productSalesData)) { ?>

                                    <?php foreach ($productSalesData as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['produk']; ?></td>
                                            <td><?php echo $row['jumlah_terjual']; ?></td>
                                            <td><?php echo number_format($row['total_pendapatan'], 2); ?></td>
                                        </tr>
                                    <?php } ?>

                                <?php } ?>

                            </table>
                        </li>

                        <li class="list-group-item mt-4">Status Pesanan: 
                            <table class="table table-bordered w-100">
                                <tr class="table-info">
                                    <th>Status Pesanan</th>
                                    <th>Jumlah Pesanan</th>
                                    <th>Total Pendapatan</th>
                                </tr>

                                <?php if (!empty($orderStatusData)) { ?>

                                    <?php foreach ($orderStatusData as $row) { ?>
                                        <tr>
                                            <td><?php echo $row['status_pesanan']; ?></td>
                                            <td><?php echo $row['jumlah_pesanan']; ?></td>
                                            <td><?php echo number_format($row['total_pendapatan'], 2); ?></td>
                                        </tr>
                                    <?php } ?>

                                <?php } ?>

                            </table>
                        </li>
                          
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
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
