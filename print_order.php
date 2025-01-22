    <?php
    ob_start();
    session_start();
    include 'condb.php';
    $citizen_id = $_SESSION['citizen_id'];

    // ตรวจสอบให้แน่ใจว่า $citizen_id ไม่เป็นค่าว่างหรือไม่เหมาะสม
    if (empty($citizen_id)) {
        echo "กรุณากรอกรหัสประชาชน";
        exit;
    }

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
        echo "ไม่พบข้อมูลผู้ใช้นี้ในระบบ";
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

    // ดึงข้อมูลจาก payment_status โดยใช้ $citizen_id
    $sql_payment_status = "SELECT payment_method FROM payment_status WHERE citizen_id = ?";
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
        // ตรวจสอบ payment_method และตั้งค่า status
        if ($payment_data['payment_method'] == 'transfer') {
            $status = "โอนเงิน";
        } else if ($payment_data['payment_method'] == 'cash') {
            $status = "เงินสด";
        } else {
            $status = "ไม่ทราบ";
        }
    } else {
        $status = "ไม่พบข้อมูลการชำระเงิน";
    }
    
    ?>
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายการสั่งซื้อสินค้า</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            margin-top: 20px;
            background: #eee;
            font-family: 'Prompt', sans-serif;
        }
        .invoice {
            background: #fff;
            padding: 20px;
        }
        .invoice-company {
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }
        .invoice-header {
            margin: 0 -20px;
            background: #f0f3f4;
            padding: 20px;
        }
        .invoice-date,
        .invoice-from,
        .invoice-to {
            display: table-cell;
            width: 1%;
        }
        .invoice-from,
        .invoice-to {
            padding-right: 20px;
        }
        .invoice-date .date,
        .invoice-from strong,
        .invoice-to strong {
            font-size: 16px;
            font-weight: 600;
        }
        .invoice-date {
            text-align: right;
            padding-left: 20px;
        }
        .invoice-price {
            background: #f0f3f4;
            display: table;
            width: 100%;
        }
        .invoice-price .invoice-price-left,
        .invoice-price .invoice-price-right {
            display: table-cell;
            padding: 20px;
            font-size: 20px;
            font-weight: 600;
            width: 75%;
            position: relative;
            vertical-align: middle;
        }
        .invoice-price .invoice-price-left .sub-price {
            display: table-cell;
            vertical-align: middle;
            padding: 0 20px;
        }
        .invoice-price small {
            font-size: 12px;
            font-weight: 400;
            display: block;
        }
        .invoice-price .invoice-price-row {
            display: table;
            float: left;
        }
        .invoice-price .invoice-price-right {
            width: 25%;
            background: #2d353c;
            color: #fff;
            font-size: 28px;
            text-align: right;
            vertical-align: bottom;
            font-weight: 300;
        }
        .invoice-price .invoice-price-right small {
            display: block;
            opacity: .6;
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 12px;
        }
        .invoice-footer {
            border-top: 1px solid #ddd;
            padding-top: 10px;
            font-size: 10px;
        }
        .invoice-note {
            color: #999;
            margin-top: 80px;
            font-size: 85%;
        }
        .invoice > div:not(.invoice-footer) {
            margin-bottom: 20px;
        }
        .btn.btn-white {
            color: #2d353c;
            background: #fff;
            border-color: #d9dfe3;
        }
        .btn.btn-white:hover {
            background-color: #f0f0f0;
            border-color: #ccc;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            font-weight: 600;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="invoice">
                <!-- begin invoice-company -->
                <div class="invoice-company text-inverse f-w-600">
                    <span class="pull-right hidden-print">
                        <!-- <a href="javascript:;" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-file t-plus-1 text-danger fa-fw fa-lg"></i> Export as PDF</a> -->
                        <a href="javascript:;" onclick="window.print()" class="btn btn-sm btn-white m-b-10 p-l-5"><i class="fa fa-print t-plus-1 fa-fw fa-lg"></i> Print</a>
                    </span>
                    วิทยาลัยเทคนิคหาดใหญ่
                </div>
                <!-- ข้อมูลลูกค้า -->
                 
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="text-center"><strong>ข้อมูลผู้สั่ง</strong></h5>
                            <p><strong>รหัสประชาชน: <?php echo $student['citizen_id']; ?></strong></p>
                            <p><strong>คำนำหน้าชื่อ: <?php echo $student['prefix']; ?></strong></p>
                            <p><strong>ชื่อจริง: <?php echo $student['first_name']; ?></strong></p>
                            <p><strong>นามสกุล: <?php echo $student['last_name']; ?></strong></p>
                            <p><strong>เบอร์โทร: <?php echo $student['phone_number']; ?></strong></p>
                            <p><strong>สาขา: <?php echo $student['major']; ?></strong></p>
                            <p><strong>ชำระโดย : <?php echo $status; ?></strong></p> <!-- แสดงข้อมูลสถานะการชำระเงิน -->
                        </div>
                    </div>
                        <!-- สิ้นสุดข้อมูลลูกค้า -->
                <!-- end invoice-header -->
                <!-- begin invoice-content -->
                <div class="invoice-content">
                    <!-- begin table-responsive -->
                    <div class="table-responsive">
                        <table class="table table-striped">
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
                        </table>
                        <div class="d-flex justify-content-end">
                            <h6 class="me-3">รวมเป็นเงิน <span class="text-danger"><?=number_format($total_price_sum, 2)?></span> บาท</h6>
                        </div>
                    </div>
                    <!-- end table-responsive -->
                </div>
                <!-- end invoice-content -->
                <!-- begin invoice-note -->

                <!-- <div class="invoice-note text-center">
                    *** กรุณาโอนเงินภายใน 7 วัน หลังจากทำการสั่งซื้อ โอนเงินผ่านธนาคาร กรุงไทย ชื่อบัญชี กฤตพล วิริยะภูรี เลขบัญชี 123456789 ***
                </div> -->

                <!-- end invoice-note -->

                <!-- begin invoice-footer -->
                <div class="invoice-footer">
                    <p class="text-center m-b-5 f-w-600">
                        THANK YOU
                    </p>
                    <p class="text-center">
                        <span class="m-r-10"><i class="fa fa-fw fa-lg fa-globe"></i> htc.ac.th</span>
                        <span class="m-r-10"><i class="fa fa-fw fa-lg fa-phone-volume"></i> Tel : 074-212300</span>
                        <span class="m-r-10"><i class="fa fa-fw fa-lg fa-envelope"></i>archives-htc@htc.ac.th</span>
                    </p>

                </div>
                <!-- end invoice-footer -->
                    <div class="text-center">
                        <a href="index.php" class="btn btn-primary mt-3">
                            <i class="fa fa-home"></i> กลับหน้าหลัก
                        </a>
                    </div>
            </div>
        </div>
    </div>

</div>
</body>
</html>
