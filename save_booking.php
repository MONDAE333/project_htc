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
        VALUES ('$citizen_id', '$prefix', '$first_name', '$last_name', '$phone_number', '$major', '$education_level')
        ON DUPLICATE KEY UPDATE 
        prefix = VALUES(prefix),
        first_name = VALUES(first_name),
        last_name = VALUES(last_name),
        phone_number = VALUES(phone_number),
        major = VALUES(major),
        education_level = VALUES(education_level)";

    if ($conn->query($query_student) === TRUE) {
    echo "<p>บันทึกหรืออัปเดตข้อมูลนักศึกษาสำเร็จ</p>";
    } else {
    echo "<p>เกิดข้อผิดพลาดในการบันทึกข้อมูลนักศึกษา: " . $conn->error . "</p>";
    }


    // ตรวจสอบและเพิ่ม/อัปเดตข้อมูลจากฟอร์มสำหรับการจองสินค้า
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'product_size_') === 0) {
            $product_id = str_replace('product_size_', '', $key);
            $size = $conn->real_escape_string($value); // ป้องกัน SQL Injection
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

                    // ตรวจสอบว่ามีรายการสินค้านี้อยู่แล้ว (แต่ไม่เช็ค size)
                    $query_check = "SELECT * FROM bookings 
                                    WHERE citizen_id = '$citizen_id' 
                                    AND product_name = '$product_name'";
                    $result_check = $conn->query($query_check);

                    if ($result_check->num_rows > 0) {
                        // มีรายการอยู่แล้ว → ตรวจสอบว่าไซต์ (`size`) เปลี่ยนไปหรือไม่
                        $existing_booking = $result_check->fetch_assoc();
                        $existing_size = $existing_booking['size'];

                        if ($existing_size != $size) {
                            // ถ้า size เปลี่ยน → อัปเดต size ด้วย
                            $query_update = "UPDATE bookings SET 
                                            size = '$size',
                                            quantity = $quantity, 
                                            total_price = $total_price,
                                            booking_date = '$booking_date' 
                                            WHERE citizen_id = '$citizen_id' 
                                            AND product_name = '$product_name'";
                        } else {
                            // ถ้า size ไม่เปลี่ยน → อัปเดตเฉพาะจำนวนและราคา
                            $query_update = "UPDATE bookings SET 
                                            quantity = $quantity, 
                                            total_price = $total_price,
                                            booking_date = '$booking_date' 
                                            WHERE citizen_id = '$citizen_id' 
                                            AND product_name = '$product_name'";
                        }

                        if ($conn->query($query_update) === TRUE) {
                            echo "<p>อัปเดตข้อมูลการจองสำเร็จ: $product_name - ไซต์ $size จำนวนใหม่ $quantity</p>";
                        } else {
                            echo "<p>เกิดข้อผิดพลาดในการอัปเดต: " . $conn->error . "</p>";
                        }
                    } else {
                        // ถ้าไม่มี ให้เพิ่มรายการใหม่
                        $query_insert = "INSERT INTO bookings 
                                        (citizen_id, product_name, size, quantity, price, total_price, booking_date) 
                                        VALUES ('$citizen_id', '$product_name', '$size', $quantity, $price, $total_price, '$booking_date')";
                        
                        if ($conn->query($query_insert) === TRUE) {
                            echo "<p>บันทึกการจองสินค้าสำเร็จ: $product_name - ไซต์ $size จำนวน $quantity</p>";
                        } else {
                            echo "<p>เกิดข้อผิดพลาดในการบันทึก: " . $conn->error . "</p>";
                        }
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
