<?php 
session_start();  // เริ่มต้น session
$major = $_SESSION['major'];

// ตรวจสอบว่า session citizen_id มีอยู่หรือไม่
if (!isset($_SESSION['citizen_id']) || empty($_SESSION['citizen_id'])) {
    // ถ้าไม่มี ให้เปลี่ยนเส้นทางไปที่หน้า form_input_voc.php
    header('Location: form_input_voc.php');
    exit();  // หยุดการทำงานของสคริปต์
}

$page_title = "รายการสินค้า"; // กำหนดชื่อหน้า
include 'head.php'; 
include 'header.php'; 
include 'condb.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง major ที่มีค่า has_file เป็น 1
$major_query = "SELECT major_name FROM major WHERE has_file = 1";
$major_result = $conn->query($major_query);

$major_conditions = [];
if ($major_result->num_rows > 0) {
    while ($row = $major_result->fetch_assoc()) {
        $major_conditions[] = $row['major_name'];
    }
}

// ถ้า major อยู่ในเงื่อนไขที่กำหนด ให้แสดงสินค้า product_id ตั้งแต่ 1 ถึง 14 ยกเว้น 12
if (in_array($major, $major_conditions)) {
    $query = "SELECT * FROM products WHERE product_id BETWEEN 1 AND 14 AND product_id != 12"; 
} else {
    // ถ้าไม่ตรงกับเงื่อนไขใดๆ ให้แสดงสินค้า product_id ตั้งแต่ 1 ถึง 11
    $query = "SELECT * FROM products WHERE product_id BETWEEN 1 AND 11"; 
}

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการสินค้า</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .card {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card img {
            max-width: 150px;
            max-height: 150px;
            object-fit: cover;
            margin: 15px 0; /* เพิ่มระยะห่างระหว่างรูปและเนื้อหา */
        }

        .card-body {
            padding: 15px;
        }

        .card-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #007bff;
            text-align: center;
        }

        .card-options {
            margin: 10px 0;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-top: 10px; /* เพิ่มช่องว่างด้านบน 10px */
        }   

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            text-align: center;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .booking-confirmation {
            text-align: center;
            margin-top: 30px;
        }
        img.product-image {
            width: 150px !important;
            height: 150px !important;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <form action="save_booking.php" method="POST">
        <div class="container">
        <br>
            <h1>รายการสินค้า</h1>
            <div class="grid">
                <?php
                // ตรวจสอบว่า มีสินค้าจากฐานข้อมูลหรือไม่
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="card">';
                        echo '<img src="assets/img/' . $row['product_image'] . '" alt="' . $row['product_name'] . '" class="product-image">';
                        echo '<div class="card-body">';
                        echo '<div class="card-title">' . $row['product_name'] . '</div>';
                        echo '<div class="card-options">';

                        // ดึงข้อมูลขนาดและราคา
                        $query_sizes = "SELECT size, price FROM product_sizes WHERE product_id = " . $row['product_id'];
                        $result_sizes = $conn->query($query_sizes);

                        echo '<label for="product-size-' . $row['product_id'] . '">เลือกไซต์และราคา:</label>';
                        echo '<select name="product_size_' . $row['product_id'] . '" id="product-size-' . $row['product_id'] . '">';

                        if ($result_sizes->num_rows > 0) {
                            while ($size = $result_sizes->fetch_assoc()) {
                                echo '<option value="' . $size['size'] . '">' . $size['size'] . ' - ' . $size['price'] . ' บาท</option>';
                            }
                        } else {
                            echo '<option value="">ไม่มีข้อมูลไซส์และราคา</option>';
                        }

                        echo '</select>';
                        echo '<label for="product-number-' . $row['product_id'] . '">เลือกจำนวน:</label>';
                        echo '<select name="product_number_' . $row['product_id'] . '" id="product-number-' . $row['product_id'] . '">';
                        for ($i = 1; $i <= 5; $i++) {
                            echo '<option value="' . $i . '">' . $i . '</option>';
                        }
                        echo '</select>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo 'ไม่มีข้อมูลสินค้า';
                }

                $conn->close();
                ?>
            </div>

            <!-- ปุ่มยืนยันการจอง -->
            <div class="booking-confirmation">
                <button type="submit" class="btn" id="confirm-booking">ยืนยันการจอง</button>
            </div>
        </div>
    </form>
</body>
</html>
