<?php
    require_once "../dbcontroller.php";
    $db = new dbcontroller;
    session_start();

    if (isset($_GET['m']) && $_GET['m'] == 'update' && isset($_GET['id'])) {
        $id_order = $_GET['id'];
        
        $current_status_query = "SELECT f_status FROM t_orders WHERE f_id = $id_order";
        $current_status = $db->getITEM($current_status_query)['f_status'];
    
        if ($current_status == 'Packaging') {
            $new_status = 'Shipping'; 
        } elseif ($current_status == 'Shipping') {
            $new_status = 'Arrived'; 
        } elseif ($current_status == 'Arrived') {
            $new_status = 'Completed';
        }else {
            echo "Invalid status transition.";
            exit;
        }
    
        $update_sql = "UPDATE t_orders SET f_status = '$new_status' WHERE f_id = $id_order";
        $db->runSQL($update_sql);
        
        if ($db->getAffectedRows() > 0) {
            header("Location: select.php");
            exit();
        } else {
            echo "Error updating status.";
        }
    }
    
    
    $user = $_SESSION['email'];
    if (isset($_GET['log'])) {
        session_destroy();
        header("location:../login.php");
    }

    $jumlahdata = $db->rowCOUNT("SELECT f_id FROM t_orders");
    $banyak = 20;
    $halaman = ceil($jumlahdata / $banyak);

    if (isset($_GET['p'])) {
        $p = $_GET['p'];
        $mulai = ($p * $banyak) - $banyak;
    } else {
        $mulai = 0;
    }

    $sql = "SELECT t_orders.f_id AS id_order, t_orders.f_tanggal_pembelian AS tanggal, 
    t_orders.f_status AS status_order, t_orders.f_shipment AS metode_pengiriman, 
    t_orders.f_payment AS metode_pembayaran, t_orders.f_shipping_address AS alamat_pengiriman, 
    t_orders.f_total_harga AS total, t_user.f_nama AS nama, t_user.email AS email 
    FROM t_orders INNER JOIN t_user ON t_orders.f_iduser = t_user.f_id 
    ORDER BY t_orders.f_id DESC LIMIT $mulai, $banyak";
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
        <title>Order - SB Admin</title>
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
                            <a class="nav-link" href="../barang/select.php">
                                <div class="sb-nav-link-icon"><i class="fa-solid fa-box"></i></div>
                                Product
                            </a>

                            <a class="nav-link" href="../profile/select.php">
                                <div class="sb-nav-link-icon"><i class="fas fa-user"></i></div>
                                User
                            </a>

                            <a class="nav-link" href="select.php">
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
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Order</h1>

                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item">Order</li>
                            <li class="breadcrumb-item active">Select</li>
                        </ol>

                        <div class="button mb-2">
                            <a href="../exsport/export.php"><button type="button" class="btn btn-success">Export Data</button></a>
                        </div>
                        
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Order
                            </div>
                            
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Tanggal Pembelian</th>
                                            <th>Metode Pembayaran</th>
                                            <th>Metode Pengiriman</th>
                                            <th>Alamat</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    
                                    <tbody>
                                    
                                    <?php if(!empty($row)) { ?>
                                        <?php foreach ($row as $r) : ?>
                                            <tr>
                                                <td><?php echo $no++?></td>
                                                <td><?php echo $r['nama'] ?></td>
                                                <td><?php echo $r['email'] ?></td>
                                                <td><?php echo $r['tanggal'] ?></td>
                                                <td><?php echo $r['metode_pembayaran'] ?></td>
                                                <td><?php echo $r['metode_pengiriman'] ?></td>
                                                <td><?php echo $r['alamat_pengiriman'] ?></td>
                                                <td><?php echo $r['total'] ?></td>
                                                <td><?php
                                                        if ($r['status_order'] == 'Packaging') {
                                                            echo "<a href='?m=update&id=" . $r['id_order'] . "'><button type='button' class='btn btn-outline-danger'>Packaging</button></a>";
                                                        } elseif ($r['status_order'] == 'Shipping') {
                                                            echo "<a href='?m=update&id=" . $r['id_order'] . "'><button type='button' class='btn btn-outline-warning'>Shipping</button></a>";
                                                        } elseif ($r['status_order'] == 'Arrived') {
                                                            echo "<a href='?m=update&id=" . $r['id_order'] . "'><button type='button' class='btn btn-outline-info'>Arrived</button></a>";
                                                        } elseif ($r['status_order'] == 'Completed') {
                                                            echo "<a href='?m=update&id=" . $r['id_order'] . "'><button type='button' class='btn btn-outline-success'>Completed</button></a>";
                                                        }
                                                        ?></td>
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
