<?php 
$page_title = "voc_cert"; // กำหนดชื่อหน้า
include 'head.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการสินค้า</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 450vh;
            background-color: #ecfdff;
        }

        .container1 {
            width: 80%;
            max-width: 1000px;
            background-color: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        h1 {
            margin: 10;
            text-align: center;
        }

        .content-container {
            margin-top: 10px;
        }

        .product-section {
            background-color: #f9f9f9;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .product-title {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
        }

        .product-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .product-details img {
            max-width: 150px;
            border-radius: 8px;
        }

        .product-options {
            display: flex;
            flex-direction: column;
            width: 60%;
        }

        label {
            font-size: 16px;
            margin-bottom: 5px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 10px; /* เพิ่มช่องว่างใต้ select */
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
        .booking-confirmation {
            text-align: center;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            background-color: #4CAF50; /* สีเขียว */
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #45a049; /* สีเขียวเข้มเมื่อ hover */
        }
    </style>
</head>
<body>
    <div class="container1">
        
        <div class="content-container">
            <h1>รายการสินค้า</h1>
            <!-- เสื้อฝึกงาน ปวช. (เสื้อช็อป) -->
            <div class="product-section">
                <div class="product-title">เสื้อฝึกงาน ปวช. (เสื้อช็อป)</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="เสื้อฝึกงาน ปวช. (เสื้อช็อป)">
                    <div class="product-options">
                        <label for="sport-shirt-sizes">เลือกไซต์และราคา :</label>
                        <select id="sport-shirt-sizes">
                            <option value="XS">XS (อก 34) - 350 บาท</option>
                            <option value="S">S - 350 บาท</option>
                            <option value="M">M - 350 บาท</option>
                            <option value="L">L - 350 บาท</option>
                            <option value="XL">XL - 350 บาท</option>
                            <option value="2XL">2XL - 350 บาท</option>
                            <option value="3XL">3XL (อก 46) - 400 บาท</option>
                        </select>
                        <label for="shop-shirt-number">เลือกจำนวนที่ต้องการ :</label>
                        <select id="shop-shirt-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- เสื้อนักศึกษาสีขาว แขนสั้น -->
            <div class="product-section">
                <div class="product-title">เสื้อนักศึกษาสีขาว แขนสั้น</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="เสื้อนักศึกษาสีขาว แขนสั้น">
                    <div class="product-options">
                        <label for="sport-shirt-sizes">เลือกไซต์และราคา :</label>
                        <select id="sport-shirt-sizes">
                            <option value="female_XS">หญิง XS (อก 34) - 260 บาท</option>
                            <option value="male_SS">ชาย SS - 260 บาท</option>
                            <option value="male_S">ชาย S - 260 บาท</option>
                            <option value="male_M">ชาย M - 260 บาท</option>
                            <option value="male_L">ชาย L - 260 บาท</option>
                            <option value="male_XL">ชาย XL - 260 บาท</option>
                            <option value="male_2XL">ชาย 2XL - 260 บาท</option>
                            <option value="male_3XL">ชาย 3XL - 260 บาท</option>
                            <option value="male_4XL">ชาย 4XL - 260 บาท</option>
                            <option value="male_5XL">ชาย 5XL - 260 บาท</option>
                            <option value="male_6XL">ชาย 6XL - 260 บาท</option>
                            <option value="female_S">หญิง S - 260 บาท</option>
                            <option value="female_M">หญิง M - 260 บาท</option>
                            <option value="female_L">หญิง L - 260 บาท</option>
                            <option value="female_XL">หญิง XL - 260 บาท</option>
                            <option value="female_2XL">หญิง 2XL - 260 บาท</option>
                            <option value="female_3XL">หญิง 3XL - 260 บาท</option>
                            <option value="female_4XL">หญิง 4XL - 260 บาท</option>
                        </select>
                        <label for="shop-shirt-number">เลือกจำนวนที่ต้องการ:</label>
                        <select id="shop-shirt-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- กางเกงขายาว สีกรม -->
            <div class="product-section">
                <div class="product-title">กางเกงขายาว สีกรม</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="กางเกงขายาว สีกรม">
                    <div class="product-options">
                        <label for="sport-shirt-sizes">เลือกไซต์และราคา :</label>
                        <select id="sport-shirt-sizes">
                            <option value="XS">XS (รอบเอว 34) - 250 บาท</option>
                            <option value="S">S - 250 บาท</option>
                            <option value="M">M - 250 บาท</option>
                            <option value="L">L - 250 บาท</option>
                            <option value="XL">XL - 250 บาท</option>
                            <option value="2XL">2XL - 250 บาท</option>
                            <option value="3XL">3XL - 250 บาท</option>
                            <option value="4XL">4XL - 250 บาท</option>
                        </select>
                        <label for="shop-shirt-number">เลือกจำนวนที่ต้องการ :</label>
                        <select id="shop-shirt-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- เสื้อพละ -->
            <div class="product-section">
                <div class="product-title">เสื้อพละ</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="เสื้อพละ">
                    <div class="product-options">
                        <label for="sport-shirt-sizes">เลือกไซต์และราคา :</label>
                        <select id="sport-shirt-sizes">
                            <option value="XS">XS (อก 34) - 250 บาท</option>
                            <option value="S">S - 250 บาท</option>
                            <option value="M">M - 250 บาท</option>
                            <option value="L">L - 250 บาท</option>
                            <option value="XL">XL - 250 บาท</option>
                            <option value="2XL">2XL - 250 บาท</option>
                            <option value="3XL">3XL - 250 บาท</option>
                            <option value="4XL">4XL - 250 บาท</option>
                        </select>
                        <label for="shop-shirt-number">เลือกจำนวนที่ต้องการ :</label>
                        <select id="shop-shirt-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- กางเกงพละ -->
            <div class="product-section">
                <div class="product-title">กางเกงพละ</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="กางเกงพละ">
                    <div class="product-options">
                        <label for="sport-shirt-sizes">เลือกไซต์และราคา :</label>
                        <select id="sport-shirt-sizes">
                            <option value="XS">XS (รอบเอว 34) - 260 บาท</option>
                            <option value="S">S - 260 บาท</option>
                            <option value="M">M - 260 บาท</option>
                            <option value="L">L - 260 บาท</option>
                            <option value="XL">XL - 260 บาท</option>
                            <option value="2XL">2XL - 260 บาท</option>
                            <option value="3XL">3XL - 260 บาท</option>
                        </select>
                        <label for="shop-shirt-number">เลือกจำนวนที่ต้องการ :</label>
                        <select id="shop-shirt-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- เข็มขัดและหัวเข็มขัด -->
            <div class="product-section">
                <div class="product-title">เข็มขัดและหัวเข็มขัด</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="เข็มขัดและหัวเข็มขัด">
                    <div class="product-options">
                        <label for="belt-option">เลือก size ตอนมารับของ :</label>
                        <select id="belt-option">
                            <option value="belt">180 บาท</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- เน็คไทและเข็ม -->
            <div class="product-section">
                <div class="product-title">เน็คไทและเข็ม สีกรม</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="เน็คไทและเข็ม">
                    <div class="product-options">
                        <label for="tie-option">ราคา :</label>
                        <select id="tie-option">
                            <option value="tie">150 บาท</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- กระเป๋าเป้ตราวิทยาลัย -->
            <div class="product-section">
                <div class="product-title">กระเป๋าเป้ตราวิทยาลัย</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="กระเป๋าเป้ตราวิทยาลัย">
                    <div class="product-options">
                        <label for="backpack-option">ราคา:</label>
                        <select id="backpack-option">
                            <option value="backpack">350 บาท</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- สมุดตรา วท.หาดใหญ่ -->
            <div class="product-section">
                <div class="product-title">สมุดตรา วท.หาดใหญ่</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="สมุดตรา วท.หาดใหญ่">
                    <div class="product-options">
                        <label for="notebook-option">1 โหล:</label>
                        <select id="notebook-option">
                            <option value="notebook">115 บาท</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- รองเท้าผ้าใบสีดำ -->
            <div class="product-section">
                <div class="product-title">รองเท้าผ้าใบสีดำ</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="รองเท้าผ้าใบสีดำ">
                    <div class="product-options">
                        <label for="shoe-sizes">เลือกไซต์และราคา:</label>
                        <select id="shoe-sizes">
                            <option value="36">36 - 355 บาท</option>
                            <option value="37">37 - 355 บาท</option>
                            <option value="38">38 - 355 บาท</option>
                            <option value="39">39 - 355 บาท</option>
                            <option value="40">40 - 355 บาท</option>
                            <option value="41">41 - 355 บาท</option>
                            <option value="42">42 - 355 บาท</option>
                            <option value="43">43 - 355 บาท</option>
                            <option value="44">44 - 355 บาท</option>
                            <option value="45">45 - 355 บาท</option>
                        </select>
                        <label for="shop-shoes-number">เลือกจำนวนที่ต้องการ:</label>
                        <select id="shop-shoes-number">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- ถุงเท้า -->
            <div class="product-section">
                <div class="product-title">ถุงเท้า</div>
                <div class="product-details">
                    <img src="https://via.placeholder.com/150" alt="ถุงเท้า">
                    <div class="product-options">
                        <label for="socks-option">เลือกจำนวนที่ต้องการ :</label>
                        <select id="socks-option">
                            <option value="socks">5 คู่ - 100 บาท</option>
                            <option value="socks">10 คู่ - 200 บาท</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- ปุ่มยืนยันการจอง -->
            <div class="booking-confirmation">
                <button type="button" class="btn btn-success" id="confirm-booking">ยืนยันการจอง</button>
            </div>
        </div>
    </div>
    <script>
    document.getElementById('confirm-booking').addEventListener('click', function() {
        alert('การจองของคุณถูกยืนยันแล้ว!');
    });
    </script>
</body>
</html>
