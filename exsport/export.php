<?php
require_once "../dbcontroller.php";
$db = new dbcontroller;

$sql = "SELECT t_orders.f_id AS id_order, t_orders.f_tanggal_pembelian AS tanggal, 
    t_orders.f_status AS status_order, t_orders.f_shipment AS metode_pengiriman, 
    t_orders.f_payment AS metode_pembayaran, t_orders.f_shipping_address AS alamat_pengiriman, 
    t_orders.f_total_harga AS total, t_user.f_nama AS nama, t_user.email AS email 
    FROM t_orders INNER JOIN t_user ON t_orders.f_iduser = t_user.f_id 
    ORDER BY t_orders.f_id DESC";
$row = $db->getALL($sql);
$no = 1;
?>
<html>
<head>
  <title>Order Barang</title>
  <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.dataTables.min.css">
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
</head>

<body>
<div class="container">
	<h2>Order</h2>
    <div class="button mb-2">
        <a href="../order/select.php"><button type="button" class="btn btn-outline-primary">Kembali</button></a>
    </div>
		<div class="data-tables datatable-dark">
			<table id="mauexport">
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
	
<script>
$(document).ready(function() {
    $('#mauexport').DataTable( {
        dom: 'Bfrtip',
        buttons: [
            'copy','csv','excel', 'pdf', 'print'
        ]
    } );
} );

</script>

<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js"></script>

	

</body>

</html>