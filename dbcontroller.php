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

    public function getLastInsertId()
    {
        return mysqli_insert_id($this->koneksi);
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

    public function addToCart($userId, $productId, $quantity, $size, $color)
    {  
        $sql = "SELECT f_quantity FROM t_cart WHERE f_iduser = ? AND f_idbarang = ? AND f_ukuran = ? AND f_warna = ?";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "iiss", $userId, $productId, $size, $color);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
    
        if ($row = mysqli_fetch_assoc($result)) {
            $newQuantity = $row['f_quantity'] + $quantity;
            $updateSQL = "UPDATE t_cart SET f_quantity = ? WHERE f_iduser = ? AND f_idbarang = ? AND f_ukuran = ? AND f_warna = ?";
            $updateStmt = mysqli_prepare($this->koneksi, $updateSQL);
            mysqli_stmt_bind_param($updateStmt, "iiiss", $newQuantity, $userId, $productId, $size, $color);
            mysqli_stmt_execute($updateStmt);
        } else {
            $insertSQL = "INSERT INTO t_cart (f_iduser, f_idbarang, f_quantity, f_ukuran, f_warna) VALUES (?, ?, ?, ?, ?)";
            $insertStmt = mysqli_prepare($this->koneksi, $insertSQL);
            mysqli_stmt_bind_param($insertStmt, "iiiss", $userId, $productId, $quantity, $size, $color);
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

    public function beginTransaction() {
        mysqli_begin_transaction($this->koneksi);
    }

    public function commit() {
        mysqli_commit($this->koneksi);
    }

    public function rollback() {
        mysqli_rollback($this->koneksi);
    }

    public function createOrder($userId, $totalAmount, $shippingMethod, $paymentMethod, $address) {
        $this->beginTransaction();
        try {
            $status = 'Packaging';
            $tanggal = date('Y-m-d H:i:s'); 
            $sql = "INSERT INTO t_orders (f_iduser, f_tanggal_pembelian, f_status, f_total_harga, f_shipment, f_payment, f_shipping_address) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($this->koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "ississs", $userId, $tanggal, $status, $totalAmount, $shippingMethod, $paymentMethod, $address); // Changed first 's' to 'i' for userId
            mysqli_stmt_execute($stmt);
            
            $orderId = mysqli_insert_id($this->koneksi);

            $sql = "SELECT t_cart.*, t_barang.f_harga, t_barang.f_diskon
                    FROM t_cart 
                    JOIN t_barang ON t_cart.f_idbarang = t_barang.f_id 
                    WHERE t_cart.f_iduser = ?";
            $stmt = mysqli_prepare($this->koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            while ($item = mysqli_fetch_assoc($result)) {
                $price = $item['f_harga'] * (1 - ($item['f_diskon'] / 100));
                $sql = "INSERT INTO t_orderdetails (f_idorder, f_idbarang, f_quantity, f_ukuran, f_warna, f_harga) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($this->koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "iiissi", 
                    $orderId, 
                    $item['f_idbarang'],
                    $item['f_quantity'],
                    $item['f_ukuran'],
                    $item['f_warna'],
                    $price
                );
                mysqli_stmt_execute($stmt);

                $sql = "UPDATE t_barang SET f_quantity = f_quantity - ? WHERE f_id = ?";
                $stmt = mysqli_prepare($this->koneksi, $sql);
                mysqli_stmt_bind_param($stmt, "ii", $item['f_quantity'], $item['f_idbarang']);
                mysqli_stmt_execute($stmt);
            }

            $sql = "DELETE FROM t_cart WHERE f_iduser = ?";
            $stmt = mysqli_prepare($this->koneksi, $sql);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);

            $this->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->rollback();
            throw $e;
        }
    }

    public function addReview($userId, $productId, $rating, $review, $ukuran, $warna)
    {
        $tanggal = date('Y-m-d H:i:s'); 
        $sql = "INSERT INTO t_review (f_iduser, f_idbarang, f_rating, f_review, f_ukuran, f_warna, f_tanggal) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($this->koneksi, $sql);
        mysqli_stmt_bind_param($stmt, "iisssss", $userId, $productId, $rating, $review, $ukuran, $warna, $tanggal);
        
        return mysqli_stmt_execute($stmt);
    }
}