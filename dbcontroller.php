<?php
class dbcontroller
{
    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = '';
    private $database = 'projectuts';
    private $koneksi;

    public function __construct()
    {
        $this->koneksi = $this->koneksiDB();
    }

public function getAffectedRows()
{
    return mysqli_affected_rows($this->koneksi);
}

    public function koneksiDB()
    {
        $koneksi = mysqli_connect($this->host, $this->user, $this->password, $this->database);
        return $koneksi;
    }

    public function getALL($sql)
    {
        $result = mysqli_query($this->koneksi, $sql);

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        if (!empty($data)) {
            return $data;
        }
    }

    public function getITEM($sql)
    {
        $result = mysqli_query($this->koneksi, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row;
    }

    public function rowCOUNT($sql)
    {
        $result = mysqli_query($this->koneksi, $sql);
        $count = mysqli_num_rows($result);

        return $count;
    }

    public function runSQL($sql)
    {
        $result = mysqli_query($this->koneksi, $sql);
    }

    public function addToCart($userId, $productId, $quantity)
    {  
        $sql = "SELECT f_quantity FROM t_cart WHERE f_iduser = ? AND f_idbarang = ?";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $newQuantity = $row['f_quantity'] + $quantity;
            $updateSQL = "UPDATE t_cart SET f_quantity = ? WHERE f_iduser = ? AND f_idbarang = ?";
            $updateStmt = mysqli_prepare($this->koneksi, $updateSQL);
            mysqli_stmt_bind_param($updateStmt, "iii", $newQuantity, $userId, $productId);
            mysqli_stmt_execute($updateStmt);
        } else {
            
            $insertSQL = "INSERT INTO t_cart (f_iduser, f_idbarang, f_quantity) VALUES (?, ?, ?)";
            $insertStmt = mysqli_prepare($this->koneksi, $insertSQL);
            mysqli_stmt_bind_param($insertStmt, "iii", $userId, $productId, $quantity);
            mysqli_stmt_execute($insertStmt);
        }
    }

    public function addToWishlist($userId, $productId)
    {
        $sql = "INSERT INTO t_wishlist (f_iduser, f_idbarang) VALUES (?, ?)";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
        return mysqli_stmt_execute($stmt);
    }

    public function removeFromWishlist($userId, $productId)
    {
        $sql = "DELETE FROM t_wishlist WHERE f_iduser = ? AND f_idbarang = ?";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
        return mysqli_stmt_execute($stmt);
    }

    public function isInWishlist($userId, $productId)
    {
        $sql = "SELECT COUNT(*) AS count FROM t_wishlist WHERE f_iduser = ? AND f_idbarang = ?";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $userId, $productId);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] > 0;
    }
}