<?php
session_start();  // เริ่มต้น session

// เก็บข้อมูลที่ผู้ใช้กรอกลงใน session
$_SESSION['citizen_id'] = $_POST['citizen_id'];
$_SESSION['prefix'] = $_POST['prefix'];
$_SESSION['first_name'] = $_POST['first_name'];
$_SESSION['last_name'] = $_POST['last_name'];
$_SESSION['phone_number'] = $_POST['phone_number'];
$_SESSION['major'] = $_POST['major'];
$_SESSION['education_level'] = $_POST['education_level'];

// เชื่อมต่อฐานข้อมูล
include 'condb.php';

// รับข้อมูลจากฟอร์ม
$citizen_id = $_POST['citizen_id'];
$education_level = $_POST['education_level'];

// ตรวจสอบว่ารหัสบัตรประชาชนมีในฐานข้อมูลแล้วหรือไม่
$query_check = "SELECT * FROM bookings WHERE citizen_id = '$citizen_id'";
$result_check = $conn->query($query_check);

if ($result_check->num_rows > 0) {
    // แสดงป๊อปอัพแจ้งเตือนแล้วกลับไปยังฟอร์ม
    echo "<script>
        alert('รหัสบัตรประชาชนนี้เคยถูกบันทึกไว้แล้ว กรุณาตรวจสอบข้อมูลของคุณ');
        window.history.back(); // ย้อนกลับไปยังหน้าฟอร์ม
    </script>";
    exit();
} else {
    // ตรวจสอบระดับการศึกษา
    if ($_SESSION['education_level'] == "ปวช.") {
        // ถ้าเป็น "ปวช." ให้ไปยังเส้นทางนี้
        if ($_SESSION['prefix'] == "นาย") {
            header('Location: voc_cert_male.php');
        } elseif ($_SESSION['prefix'] == "น.ส.") {
            header('Location: voc_cert_female.php');
        }
    } elseif ($_SESSION['education_level'] == "ปวส.") {
        // ถ้าเป็น "ปวส." ให้ไปยังเส้นทางนี้
        if ($_SESSION['prefix'] == "นาย") {
            header('Location: high_voc_cert_male.php');
        } elseif ($_SESSION['prefix'] == "น.ส.") {
            header('Location: high_voc_cert_female.php');
        }
    }
    exit();
}

$conn->close();

