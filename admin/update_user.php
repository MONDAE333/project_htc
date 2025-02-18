<?php
session_start();
include "condb.php"; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเป็น super_admin หรือไม่
if ($_SESSION['user_level'] !== 'super_admin') {
    echo "คุณไม่มีสิทธิ์ดำเนินการนี้";
    exit();
}

// ตรวจสอบว่าได้รับข้อมูลจากฟอร์ม
if (isset($_POST['user_id']) && isset($_POST['status']) && isset($_POST['user_level'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    $user_level = $_POST['user_level'];

    // อัปเดตสถานะและระดับผู้ใช้
    $sql = "UPDATE user SET status = ?, user_level = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssi', $status, $user_level, $user_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "อัปเดตสถานะและระดับผู้ใช้สำเร็จ";
    } else {
        $_SESSION['message'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }

    // เปลี่ยนเส้นทางกลับไปที่หน้าจัดการผู้ใช้
    header("Location: manage_users.php");
    exit();
} else {
    echo "ข้อมูลไม่ครบถ้วน";
}
?>
