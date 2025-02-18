<?php
// เชื่อมต่อฐานข้อมูล
include 'head.php'; 
include 'header.php'; 
include 'condb.php';

// เริ่มต้น session
session_start();

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $citizen_id = $_POST['citizen_id'];

    // ตรวจสอบให้แน่ใจว่า $citizen_id ไม่เป็นค่าว่างหรือไม่เหมาะสม
    if (empty($citizen_id)) {
        echo "กรุณากรอกรหัสประชาชน";
        exit;
    }

    // เก็บรหัสประชาชนใน session
    $_SESSION['citizen_id'] = $citizen_id;

    // ดึงข้อมูลจาก student_info (ใช้ Prepared Statement เพื่อป้องกัน SQL Injection)
    $sql_student = "SELECT citizen_id, prefix, first_name, last_name, phone_number, major FROM student_info WHERE citizen_id = ?";
    $stmt_student = $conn->prepare($sql_student);
    if (!$stmt_student) {
        echo "Error in preparing student query: " . $conn->error;
        exit;
    }

    $stmt_student->bind_param("s", $citizen_id);
    $stmt_student->execute();
    $result_student = $stmt_student->get_result();

    // ตรวจสอบว่ามีข้อมูลผู้ใช้หรือไม่
    if ($result_student->num_rows > 0) {
        $student = $result_student->fetch_assoc();
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>';
        echo '<script type="text/javascript">
                Swal.fire({
                    icon: "error",
                    title: "ไม่พบข้อมูลผู้ใช้นี้ในระบบ",
                    text: "กรุณาตรวจสอบข้อมูลและลองใหม่",
                    showConfirmButton: true,
                    confirmButtonText: "ตกลง"
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = "search_booking.php";  // ไปที่หน้า search_booking.php
                    }
                });
            </script>';
        exit;
    }

    // ดึงข้อมูลจาก bookings (ใช้ Prepared Statement)
    $sql_bookings = "SELECT product_name, size, quantity, price, total_price FROM bookings WHERE citizen_id = ?";
    $stmt_bookings = $conn->prepare($sql_bookings);
    if (!$stmt_bookings) {
        echo "Error in preparing bookings query: " . $conn->error;
        exit;
    }

    $stmt_bookings->bind_param("s", $citizen_id);
    $stmt_bookings->execute();
    $result_bookings = $stmt_bookings->get_result();

    // คำนวณราคารวม
    $total_price_sum = 0;
    while ($row = $result_bookings->fetch_assoc()) {
        $total_price_sum += $row['total_price'];
    }
}

    // ดึงข้อมูลจาก payment_status โดยใช้ $citizen_id
    $sql_payment_status = "SELECT payment_method, status FROM payment_status WHERE citizen_id = ?";
    $stmt_payment_status = $conn->prepare($sql_payment_status);
    if (!$stmt_payment_status) {
        echo "Error in preparing payment_status query: " . $conn->error;
        exit;
    }

    $stmt_payment_status->bind_param("s", $citizen_id);
    $stmt_payment_status->execute();
    $result_payment_status = $stmt_payment_status->get_result();

    // ตรวจสอบว่าเจอข้อมูลหรือไม่
    if ($result_payment_status->num_rows > 0) {
        $payment_data = $result_payment_status->fetch_assoc();
        
        // ตรวจสอบ payment_method และตั้งค่า $status_payment
        if ($payment_data['payment_method'] == 'transfer') {
            $status_payment = "โอนเงิน";
        } elseif ($payment_data['payment_method'] == 'cash') {
            $status_payment = "เงินสด";
        } else {
            $status_payment = "ไม่พบข้อมูลการชำระเงิน";
        }
        
        // ตรวจสอบ status และตั้งค่า $status
        if ($payment_data['status'] === 'pending') {
            $status = "รอดำเนินการ";
        } elseif ($payment_data['status'] === 'paid') {
            $status = "ชำระเงินแล้ว";
        } elseif ($payment_data['status'] === 'failed') {
            $status = "ชำระเงินล้มเหลว";
        } elseif ($payment_data['status'] === 'completed') {
            $status = "ชำระเงินเสร็จสมบูรณ์";
        } else {
            $status = "สถานะไม่ทราบ";
        }
    } else {
        $status_payment = "ไม่พบข้อมูลการชำระเงิน";
        $status = "ไม่พบสถานะ";
    }

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ข้อมูลการจอง</title>
    <!-- เพิ่มลิงค์ Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">
    <style>
    /* เพิ่มการจัดรูปแบบพื้นฐาน */
    body {
        font-family: 'Poppins', sans-serif;
        background-color: #f4f4f4;
        padding: 20px;
    }

    .container1 {
        max-width: 1000px;
        margin: 0 auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        margin-top: 100px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    h3 {
        color: #333;
        text-align: center; /* จัดให้หัวข้ออยู่กลาง */
    }

    /* สไตล์ของกรอบข้อมูลผู้ใช้ */
    .student-info {
        background-color:rgb(255, 255, 255); /* สีพื้นหลังกรอบข้อมูลผู้ใช้ */
        border: 2px solid rgb(0, 0, 0); /* กรอบสีฟ้า */
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .student-info h3 {
        margin-bottom: 10px; /* เพิ่มระยะห่างด้านล่างของหัวข้อ */
        text-align: center; /* จัดหัวข้อข้อมูลผู้ใช้ให้ตรงกลาง */
    }

    .student-info p {
        font-size: 1em;
        color: #333;
    }

    .form-container, .booking-container {
        margin-bottom: 20px;
    }

    .form-container label,
    .form-container input,
    .form-container button {
        display: block;
        margin: 10px 0;
    }

    .form-container input,
    .form-container button {
        padding: 10px;
        width: 100%;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    .form-container button {
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
    }

    .form-container button:hover {
        background-color: #45a049;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    table th, table td {
        padding: 10px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    table th {
        background-color: #f4f4f4;
    }

    .total-price {
        font-weight: bold;
        margin-top: 20px;
        font-size: 1.2em;
    }

    .no-booking {
        color: red;
    }

    .button-print {
        display: inline-block;
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #007bff;
        color: white;
        text-align: center;
        border-radius: 4px;
        text-decoration: none;
    }

    .button-print:hover {
        background-color: #0056b3;
    }
    .button-payment {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px; /* เพิ่มขนาดปุ่ม */
    background-color: #ff6f61; /* สีแดงส้ม */
    color: white; /* สีข้อความ */
    font-size: 16px; /* ขนาดตัวอักษร */
    font-weight: bold; /* ตัวอักษรหนา */
    text-align: center;
    border: none; /* ไม่มีเส้นขอบ */
    border-radius: 25px; /* มุมมน */
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* เพิ่มเงา */
    transition: all 0.3s ease; /* เพิ่มเอฟเฟกต์การเปลี่ยนแปลง */
    cursor: pointer; /* เปลี่ยนเคอร์เซอร์เมื่อชี้ */
    }

    .button-payment:hover {
        background-color: #e65b50; /* สีเข้มขึ้นเมื่อชี้ */
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.2); /* เพิ่มเงาเมื่อชี้ */
        transform: translateY(-2px); /* ขยับขึ้นเล็กน้อย */
    }

    .button-payment:active {
        background-color: #cc5046; /* สีเข้มสุดเมื่อกด */
        transform: translateY(0); /* กลับมาตำแหน่งเดิม */
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* เงาปกติ */
    }
    
    .status.pending {
    color: orange;
    }

    .status.paid {
        color: green;
    }

    .status.failed {
        color: red;
    }

    .status.completed {
        color: blue;
    }

    </style>
</head>
<body>
    <div class="container1">
        <div class="form-container">
            <form method="POST" action="">
                <label for="citizen_id">หมายเลขบัตรประชาชน:</label>
                <input type="text" id="citizen_id" name="citizen_id" maxlength="13" required
                pattern="\d{13}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                <button type="submit">ค้นหา</button>
            </form>
        </div>

        <?php if (isset($student)): ?>
            <div class="student-info">
                <h3>ข้อมูลผู้ใช้</h3>
                <p>รหัสประชาชน : <?php echo $student['citizen_id']; ?></p>
                <p>คำนำหน้าชื่อ : <?php echo $student['prefix']; ?></p>
                <p>ชื่อจริง : <?php echo $student['first_name']; ?></p>
                <p>นามสกุล : <?php echo $student['last_name']; ?></p>
                <p>เบอร์โทร : <?php echo $student['phone_number']; ?></p>
                <p>สาขา : <?php echo $student['major']; ?></p>
                <p>ชำระโดย : <?php echo $status_payment; ?></p> <!-- แสดงข้อมูลสถานะการชำระเงิน -->
                <p>สถานะการตรวจสอบ : <span class="status <?php echo strtolower($status); ?>"><?php echo $status; ?></span></p>
            </div>
            
            <!-- แสดงข้อมูลสินค้าทั้งหมด -->
            <div class="booking-container">
                <h3>ข้อมูลการจอง</h3>
                <?php if ($result_bookings->num_rows > 0): ?>
                    <table class="table table-bordered payment-table">
                        <thead>
                            <tr>
                                <th>สินค้า</th>
                                <th>ขนาด</th>
                                <th>จำนวน</th>
                                <th>ราคา</th>
                                <th>ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Reset pointer to the start of the result set
                            $result_bookings->data_seek(0);

                            // แสดงข้อมูลสินค้าทั้งหมด
                            while ($row = $result_bookings->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['product_name'] ?></td>
                                    <td><?= $row['size'] ?></td>
                                    <td><?= $row['quantity'] ?></td>
                                    <td><?= number_format($row['price'], 2) ?></td>
                                    <td><?= number_format($row['total_price'], 2) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="4" class="text-end">รวมทั้งหมด</td>
                                <td><?= number_format($total_price_sum, 2) ?> บาท</td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <?php if ($status_payment === "ไม่พบข้อมูลการชำระเงิน" || $status === "ชำระเงินล้มเหลว"): ?>
                        <form method="POST" action="confirmation.php">
                            <input type="hidden" name="citizen_id" value="<?= $citizen_id ?>">
                            <button type="submit" class="button-payment">ชำระเงิน</button>
                        </form>
                    <?php else: ?>
                        <a href="print_order.php" class="button-print">Print</a>
                    <?php endif; ?>
                <?php else: ?>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'error',
                            title: 'ไม่มีข้อมูลการจอง',
                            text: 'ไม่พบข้อมูลการจองสำหรับรหัสประชาชนนี้',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true
                        });
                    </script>                
                <?php endif; ?>

            </div>
        <?php endif; ?>
    </div>
</body>
</html>

<?php 
// ปิดการเชื่อมต่อ
$conn->close();
?>
