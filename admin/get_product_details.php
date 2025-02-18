<?php
include 'condb.php';

if (isset($_GET['citizen_id'])) {
    $citizen_id = $_GET['citizen_id'];

    // ดึงข้อมูลการจองสินค้าจากฐานข้อมูล
    $sql = "SELECT b.product_name, b.size, b.price, b.quantity
            FROM bookings b
            WHERE b.citizen_id = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, 's', $citizen_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    $productDetails = [];
    $totalPrice = 0; // ตัวแปรสำหรับเก็บราคารวมทั้งหมด

    while ($row = mysqli_fetch_assoc($result)) {
        // คำนวณราคารวม
        $rowTotalPrice = $row['price'] * $row['quantity'];
        $totalPrice += $rowTotalPrice; // เพิ่มราคารวมของสินค้านั้นๆ

        // เก็บข้อมูลสินค้าพร้อมราคารวมใน array
        $productDetails[] = [
            'product_name' => $row['product_name'],
            'size' => $row['size'],
            'price' => $row['price'],
            'quantity' => $row['quantity'],
            'total_price' => $rowTotalPrice
        ];
    }

    // ดึงข้อมูลสลิปจากตาราง payment_status
    $sqlSlip = "SELECT slip_file FROM payment_status WHERE citizen_id = ?";
    $stmtSlip = mysqli_prepare($conn, $sqlSlip);
    mysqli_stmt_bind_param($stmtSlip, 's', $citizen_id);
    mysqli_stmt_execute($stmtSlip);
    $resultSlip = mysqli_stmt_get_result($stmtSlip);

    $slipFile = '';
    if ($rowSlip = mysqli_fetch_assoc($resultSlip)) {
        $slipFile = $rowSlip['slip_file'];
    }

    // ส่งข้อมูลสินค้าพร้อมราคารวมทั้งหมด และข้อมูลสลิป
    echo json_encode([
        'product_details' => $productDetails, 
        'total_price' => $totalPrice,
        'slip_file' => $slipFile
    ]);

    mysqli_stmt_close($stmt);
    mysqli_stmt_close($stmtSlip);
}
?>
