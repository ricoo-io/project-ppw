<?php
require_once "../dbcontroller.php";
$db = new dbcontroller;

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "DELETE FROM t_barang WHERE f_id=$id ORDER BY f_id DESC LIMIT 1";

    $db->runSQL($sql);

    header("Location: select.php");
}
