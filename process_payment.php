<?php
session_start();
include 'condb.php'; // รวมการเชื่อมต่อฐานข้อมูล

$citizen_id = $_SESSION['citizen_id']; // รับค่า citizen_id จาก session

// ตรวจสอบว่ามีการส่งข้อมูลมา
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $payment_method = $_POST['payment_method']; // รับวิธีการชำระเงิน
    $status = ($payment_method == 'cash') ? 'paid' : 'pending'; // หากเป็นเงินสดให้สถานะเป็น paid
    $slip_file = NULL; // สลิปการโอนเงินเริ่มต้นเป็น NULL

    // ตรวจสอบการเลือกการชำระเงินและการอัปโหลดสลิป
    if ($payment_method == 'transfer') {
        // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
        if (isset($_FILES['slip']) && $_FILES['slip']['error'] == 0) {
            $upload_dir = 'assets/uploads/slips/';
            $slip_file = $upload_dir . basename($_FILES['slip']['name']);
            
            // ตรวจสอบประเภทไฟล์
            $allowed_types = ['image/jpeg', 'image/png'];
            if (in_array($_FILES['slip']['type'], $allowed_types)) {
                if (!move_uploaded_file($_FILES['slip']['tmp_name'], $slip_file)) {
                    $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปโหลดไฟล์.";
                    header("Location: confirmation.php");
                    exit();
                }
            } else {
                $_SESSION['error_message'] = "ประเภทไฟล์ไม่ถูกต้อง กรุณาอัปโหลดไฟล์รูปภาพ (JPEG หรือ PNG).";
                header("Location: confirmation.php");
                exit();
            }
        } else {
            $_SESSION['error_message'] = "กรุณาเลือกไฟล์สลิปสำหรับการโอนเงิน.";
            header("Location: confirmation.php");
            exit();
        }
    }

    // สั่งบันทึกข้อมูลการชำระเงินลงในฐานข้อมูล
    $stmt = $conn->prepare("INSERT INTO payment_status (citizen_id, payment_method, slip_file, status) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $citizen_id, $payment_method, $slip_file, $status);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "การชำระเงินสำเร็จ!";
        header("Location: confirmation.php");
        exit();
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการประมวลผลการชำระเงิน: " . $stmt->error;
        header("Location: confirmation.php");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
