<?php
include 'condb.php';

// SQL Query ที่ใช้
$sql = "
SELECT 
    c.category_name,
    b.product_name,
    b.size,
    SUM(b.quantity) AS total_reserved_quantity,
    CASE 
        WHEN si.prefix = 'นาย' THEN 'Male'
        WHEN si.prefix = 'น.ส.' THEN 'Female'
        ELSE 'Other'
    END AS gender,
    COUNT(b.booking_id) AS total_bookings
FROM 
    bookings b
JOIN 
    categories c ON b.product_name = c.category_name
JOIN 
    student_info si ON b.citizen_id = si.citizen_id
GROUP BY 
    c.category_name, b.product_name, b.size, gender
ORDER BY 
    c.category_name, b.product_name, b.size, gender;

";

// รันคำสั่ง SQL
$result = $conn->query($sql);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result->num_rows > 0) {
    // เริ่มต้นการแสดงผลในตาราง HTML
    echo "<table border='1'>
            <tr>
                <th>ชื่อสินค้า</th>
                <th>ไซต์</th>
                <th>จำนวนสินค้าที่ถูกจองโดยเพศ</th>
                <th>เพศ</th>
                <th>จำนวนครั้งที่ถูกจอง</th>
            </tr>";
    
    // แสดงข้อมูลแต่ละแถว
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>" . $row["product_name"] . "</td>
                <td>" . $row["size"] . "</td>
                <td>" . $row["total_reserved_quantity"] . "</td>
                <td>" . $row["gender"] . "</td>
                <td>" . $row["total_bookings"] . "</td>
              </tr>";
    }
    
    // ปิดแท็กตาราง
    echo "</table>";
} else {
    echo "0 results";
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
