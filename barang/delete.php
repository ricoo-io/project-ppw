<?php
require_once "../dbcontroller.php";
$db = new dbcontroller;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql_barang = "DELETE FROM t_barang WHERE f_id = $id";
    $db->runSQL($sql_barang);

    header("Location: select.php");
}
?>