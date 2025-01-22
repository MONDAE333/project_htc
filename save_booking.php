<?php
session_start();  // เริ่มต้น session

$citizen_id = $_SESSION['citizen_id'];
$prefix = $_SESSION['prefix'];
$first_name = $_SESSION['first_name'];
$last_name = $_SESSION['last_name'];
$phone_number = $_SESSION['phone_number'];
$major = $_SESSION['major'];
$education_level = $_SESSION['education_level'];

// กำหนดชื่อหน้า
$page_title = "บันทึกการจอง"; 

// เชื่อมต่อฐานข้อมูล
include 'condb.php';

// ตรวจสอบว่าเป็นการส่งแบบ POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // เตรียมตัวแปรสำหรับการบันทึกข้อมูล
    $booking_date = date("Y-m-d H:i:s"); // วันที่ปัจจุบัน

    // **เพิ่มข้อมูลในตาราง student_info**
    $query_student = "INSERT INTO student_info (citizen_id, prefix, first_name, last_name, phone_number, major, education_level)
                      VALUES ('$citizen_id', '$prefix', '$first_name', '$last_name', '$phone_number', '$major', '$education_level')";

    if ($conn->query($query_student) === TRUE) {
        echo "<p>ข้อมูลนักศึกษาได้รับการบันทึกสำเร็จ</p>";
    } else {
        echo "<p>เกิดข้อผิดพลาดในการบันทึกข้อมูลนักศึกษา: " . $conn->error . "</p>";
    }

    // ตรวจสอบและเพิ่มข้อมูลจากฟอร์มสำหรับการจองสินค้า
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'product_size_') === 0) {
            $product_id = str_replace('product_size_', '', $key);
            $size = $value;
            $quantity_key = 'product_number_' . $product_id;

            if (isset($_POST[$quantity_key])) {
                $quantity = (int)$_POST[$quantity_key];

                // ดึงข้อมูลชื่อสินค้าและราคา
                $query_product = "
                SELECT p.product_name, ps.price 
                FROM product_sizes ps
                JOIN products p ON ps.product_id = p.product_id
                WHERE ps.product_id = $product_id AND ps.size = '$size'
                ";
                $result_product = $conn->query($query_product);
                
                if ($result_product->num_rows > 0) {
                    $product = $result_product->fetch_assoc();
                    $product_name = $product['product_name'];
                    $price = $product['price'];
                    $total_price = $price * $quantity;

                    // เพิ่มข้อมูลการจองลงในฐานข้อมูล
                    $query_insert = "INSERT INTO bookings 
                                     (citizen_id, product_name, size, quantity, price, total_price, booking_date) 
                                     VALUES ('$citizen_id', '$product_name', '$size', $quantity, $price, $total_price, '$booking_date')";
                    
                    if ($conn->query($query_insert) === TRUE) {
                        echo "<p>บันทึกการจองสินค้าสำเร็จ: $product_name - $size จำนวน $quantity</p>";
                    } else {
                        echo "<p>เกิดข้อผิดพลาดในการบันทึก: " . $conn->error . "</p>";
                    }
                }
            }
        }
    }

    // // หลังจากบันทึกเสร็จแล้วทำลาย session
    // session_unset();  // ลบค่าตัวแปรทั้งหมดใน session
    // session_destroy();  // ทำลาย session ทั้งหมด

    // เปลี่ยนเส้นทางไปยังหน้าที่ต้องการ เช่น หน้า confirmation
    header('Location: confirmation.php');
    exit();
} else {
    echo "<p>ไม่มีข้อมูลการจอง</p>";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
