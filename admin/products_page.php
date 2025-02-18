<?php
include 'condb.php';

// คำสั่ง SQL สำหรับดึงข้อมูลสินค้าและเพศของผู้จอง
$sql = "
SELECT 
    c.category_name,
    b.product_name,
    b.size,
    SUM(b.quantity) AS total_reserved_quantity,
    CASE 
        WHEN p.separate_by_gender = FALSE THEN 'All'
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
JOIN 
    products p ON b.product_name = p.product_name
GROUP BY 
    c.category_name, b.product_name, b.size, 
    CASE 
        WHEN p.separate_by_gender = FALSE THEN 'All'
        WHEN si.prefix = 'นาย' THEN 'Male'
        WHEN si.prefix = 'น.ส.' THEN 'Female'
        ELSE 'Other'
    END
ORDER BY 
    c.category_name, b.product_name, b.size, gender;
";

// ดึงข้อมูลจากฐานข้อมูล
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานข้อมูลสินค้าที่ถูกจอง</title>
    <style>
        /* สไตล์สำหรับหน้าจอ */
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }
        th, td {
            padding: 10px;
            text-align: center;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f4f4f4;
        }
        /* สไตล์สำหรับปริ้น */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            table {
                width: 100%;
                border: 1px solid black;
                border-collapse: collapse;
            }
            th, td {
                padding: 8px;
                border: 1px solid black;
            }
            h1 {
                text-align: center;
                margin-top: 0;
            }
            .no-print {
                display: none;
            }
        }
        /* สไตล์สำหรับปุ่ม */
        .btn {
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            text-align: center;
        }
        .btn-print {
            background-color: #4CAF50;
            color: white;
        }
        .btn-back {
            background-color: #f44336;
            color: white;
        }
        .btn:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <h1>รายงานข้อมูลสินค้าที่ถูกจองแยกตามเพศ</h1>
    <table>
        <thead>
            <tr>
                <th>ชื่อสินค้า</th>
                <th>ไซต์</th>
                <th>จำนวนสินค้าที่ถูกจอง</th>
                <th>เพศ</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // ตรวจสอบว่ามีข้อมูลหรือไม่
            if ($result->num_rows > 0) {
                // แสดงข้อมูลแต่ละแถว
                while ($row = $result->fetch_assoc()) {
                    // เปลี่ยนค่า gender จาก 'Male', 'Female', 'All' เป็น 'ชาย', 'หญิง', 'ไม่ระบุเพศ'
                    $gender = $row["gender"];
                    if ($gender == 'Male') {
                        $gender = 'ชาย';
                    } elseif ($gender == 'Female') {
                        $gender = 'หญิง';
                    } elseif ($gender == 'All') {
                        $gender = 'ไม่ระบุเพศ';
                    }

                    echo "<tr>
                            <td>" . $row["product_name"] . "</td>
                            <td>" . $row["size"] . "</td>
                            <td>" . $row["total_reserved_quantity"] . "</td>
                            <td>" . $gender . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- ปุ่มพิมพ์และปุ่มกลับ -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button class="btn btn-print" onclick="window.print()">พิมพ์รายงาน</button>
        <a href="index.php">
            <button class="btn btn-back">กลับสู่หน้าหลัก</button>
        </a>
    </div>
</body>
</html>
