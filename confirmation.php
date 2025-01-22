<?php
session_start();
include 'head.php'; 
include 'header.php'; 
include 'condb.php'; 

$citizen_id = $_SESSION['citizen_id']; // รับค่า citizen_id จาก session

// ดึงข้อมูลการจองจากตาราง bookings ตาม citizen_id
$sql = "SELECT * FROM bookings WHERE citizen_id = '$citizen_id'";
$result = $conn->query($sql);

// ตรวจสอบว่า query ทำงานหรือไม่
if ($result === false) {
    die("Error executing query: " . $conn->error);
}

$total_amount = 0;
while ($row = $result->fetch_assoc()) {
    $total_amount += $row['total_price'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body.payment-page {
            background-color: #f8f9fa;
        }

        .container.payment-container {
            max-width: 800px;
        }

        .payment-table th, .payment-table td {
            text-align: center;
            vertical-align: middle;
        }

        .payment-table .total-row td {
            font-weight: bold;
        }

        .card.payment-card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-modal .modal-header {
            background-color: #28a745;
            color: white;
        }

        .payment-modal .modal-footer .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-payment {
            background-color: #007bff;
            color: white;
        }

        .btn-payment:hover {
            background-color: #0056b3;
        }

        .qrcode-img {
            max-width: 180px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body class="payment-page">
<div class="container payment-container mt-4">
    <div class="card payment-card p-4">
        <br><br>
        <h2 class="text-center mb-4">ชำระเงิน</h2>

        <!-- ตารางการชำระเงิน -->
        <table class="table table-bordered payment-table">
            <thead class="table-light">
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
            $result->data_seek(0);

            // แสดงข้อมูลสินค้าทั้งหมด
            while ($row = $result->fetch_assoc()): ?>
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
                    <td><?= number_format($total_amount, 2) ?></td>
                </tr>
            </tfoot>
        </table>

        <form action="process_payment.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label>วิธีการชำระเงิน:</label>
                <div>
                    <input type="radio" name="payment_method" value="cash" id="cash" onclick="toggleSlip(false)" required>
                    <label for="cash">เงินสด</label>
                    <input type="radio" name="payment_method" value="transfer" id="transfer" onclick="toggleSlip(true)">
                    <label for="transfer">โอนเงิน</label>
                </div>
            </div>

            <div id="qrcode-section" style="display: none;">
                <p>สแกน QR Code เพื่อชำระเงิน:</p>
                <img src="assets/img/qrcode.jpg" alt="QR Code" class="img-fluid qrcode-img">
                <div class="mt-3">
                    <label>อัปโหลดสลิปโอนเงิน:</label>
                    <input type="file" name="slip" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-payment mt-3">ยืนยันการชำระเงิน</button>
        </form>
    </div>
</div>

<!-- Modal สำหรับการแจ้งเตือน -->
<?php if (isset($_SESSION['error_message']) || isset($_SESSION['success_message'])): ?>
    <div class="modal fade payment-modal" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="notificationModalLabel">แจ้งเตือน</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            <?php if (isset($_SESSION['success_message'])) { ?>
                <div class="modal-body">
                    <?php
                        echo $_SESSION['success_message'];
                        unset($_SESSION['success_message']);
                    ?>
                </div>
                <div class="modal-footer">
                    <a href="index.php" class="btn btn-primary">กลับไปที่หน้าหลัก</a>
                </div>
            <?php } ?>
                <?php if (isset($_SESSION['error_message'])) { ?>
                <div class="modal-body">
                   <?php 
                        echo $_SESSION['error_message'];
                        unset($_SESSION['error_message']);
                    ?>
                </div>
                <!-- <div class="modal-footer">
                    <a href="index.php" class="btn btn-primary">กลับไปที่หน้าหลัก</a>
                </div> -->
            <?php } ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
function toggleSlip(show) {
    document.getElementById('qrcode-section').style.display = show ? 'block' : 'none';
}

document.addEventListener("DOMContentLoaded", function () {
    var notificationModal = document.getElementById('notificationModal');
    if (notificationModal) {
        var modal = new bootstrap.Modal(notificationModal);
        modal.show();
    }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
