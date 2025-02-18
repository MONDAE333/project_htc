<?php
date_default_timezone_set('Asia/Bangkok');  // ตั้งเวลาเป็น Time Zone ของไทย

session_start();
include 'condb.php'; // เชื่อมต่อฐานข้อมูล
include 'config.php'; // นำเข้าคอนฟิกไฟล์สำหรับ ACCESS_TOKEN

$citizen_id = $_SESSION['citizen_id'];
// $total_price = $_POST['total_price'];
$total_price = "1.00";  // ตัวอย่างราคาทั้งหมด
$payment_method = $_POST['payment_method'];
$status = 'pending';
$slip_file = NULL;   
$verified_at = NULL;


// ตรวจสอบการอัปโหลดสลิป
if ($payment_method == 'transfer') {
    if (isset($_FILES['slip']) && $_FILES['slip']['error'] == 0) {
        $upload_dir = 'assets/uploads/slips/';
        $slip_file = $upload_dir . basename($_FILES['slip']['name']);

        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/png'];
        if (in_array($_FILES['slip']['type'], $allowed_types)) {
            // ตรวจสอบขนาดไฟล์
            if ($_FILES['slip']['size'] > 2000000) {  // 2MB
                $_SESSION['error_message'] = "ไฟล์ขนาดใหญ่เกินไป กรุณาอัปโหลดไฟล์ที่มีขนาดไม่เกิน 2MB.";
                header("Location: confirmation.php");
                exit();
            }
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

    // เรียกใช้งาน EasySlip API เพื่อการตรวจสอบสลิป
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://developer.easyslip.com/api/v1/verify',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($slip_file)), // ส่งไฟล์สลิป
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . ACCESS_TOKEN, // ใช้ ACCESS_TOKEN ที่ได้จากไฟล์ config
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);

    // ตรวจสอบผลลัพธ์จาก API
    if ($response === false) {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการตรวจสอบสลิป";
        header("Location: confirmation.php");
        exit();
    }

    // แปลงผลลัพธ์จาก JSON เป็นอาเรย์
    $result = json_decode($response, true);

    // ตรวจสอบสถานะของ API
    if ($result['status'] == 200) {
        $status = "completed";  // ✅ ถ้ายอดเงินถูกต้อง
        $verified_at = date("Y-m-d H:i:s");

        // ดึงข้อมูลสำคัญจาก API
        $transRef = $result['data']['transRef'];
        $amount = $result['data']['amount']['amount'];
        $receiver_name = $result['data']['receiver']['account']['name']['th'];

        // ตรวจสอบยอดเงิน
        if (floatval($amount) != floatval($total_price)) {
            $_SESSION['error_message'] = "ยอดเงินไม่ตรง กรุณาตรวจสอบสลิปของคุณ!";
            header("Location: confirmation.php");
            exit();
        }
        // ตรวจสอบชื่อผู้รับ
        if (strpos($receiver_name, "นายกฤตพล ว") === false) {
            $_SESSION['error_message'] = "ชื่อผู้รับไม่ตรงกับที่คาดหวัง กรุณาตรวจสอบข้อมูล!";
            header("Location: confirmation.php");
            exit();
        }
    } elseif ($result['status'] == 404 && isset($result['message']) && $result['message'] == 'slip_not_found') {
        $_SESSION['error_message'] = "อาจเกิดจากสลิปปลอม หรือข้อมูลที่ผิดพลาดจากธนาคารผู้ออกสลิป โปรดติดต่อเจ้าหน้าที่ในวันมารับของ";
        header("Location: confirmation.php");
        exit();
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการตรวจสอบสลิป กรุณาลองใหม่อีกครั้ง!";
        header("Location: confirmation.php");
        exit();
    }
}else {
    // กรณีจ่ายด้วยเงินสด ให้กำหนดค่า trans_ref เป็น NULL
    $transRef = NULL;
}

// เช็คว่า citizen_id มีอยู่แล้วหรือไม่
$check_query = "SELECT payment_id FROM payment_status WHERE citizen_id = ?";
$stmt_check = $conn->prepare($check_query);
$stmt_check->bind_param("s", $citizen_id);
$stmt_check->execute();
$stmt_check->store_result();
$exists = $stmt_check->num_rows > 0;
$stmt_check->close();

if ($exists) {
    // อัปเดตข้อมูลถ้ามีอยู่แล้ว
    $update_query = "UPDATE payment_status SET payment_method = ?, slip_file = ?, status = ?, trans_ref = ?, verified_at = ?, updated_at = NOW() WHERE citizen_id = ?";
    $stmt_update = $conn->prepare($update_query);
    $stmt_update->bind_param("ssssss", $payment_method, $slip_file, $status, $transRef, $verified_at, $citizen_id);
    
    if ($stmt_update->execute()) {
        $_SESSION['success_message'] = "อัปเดตข้อมูลสำเร็จ!";
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการอัปเดต: " . $stmt_update->error;
    }
    $stmt_update->close();
} else {
    // เพิ่มข้อมูลใหม่ถ้าไม่มี
    $insert_query = "INSERT INTO payment_status (citizen_id, payment_method, slip_file, status, trans_ref, verified_at) 
                     VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("ssssss", $citizen_id, $payment_method, $slip_file, $status, $transRef, $verified_at);
    
    if ($stmt_insert->execute()) {
        $_SESSION['success_message'] = "ตรวจสอบและชำระเงินสำเร็จ!";
    } else {
        $_SESSION['error_message'] = "เกิดข้อผิดพลาดในการบันทึก: " . $stmt_insert->error;
    }
    $stmt_insert->close();
}

$conn->close();
header("Location: confirmation.php");
exit();
?>
