<?php
include 'condb.php'; // เชื่อมต่อฐานข้อมูล
session_start();

// ตรวจสอบว่า session checklogin มีค่าเป็น true หรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}

if (isset($_POST['update_status'])) {
    $citizen_id = $_POST['citizen_id'];
    $status = $_POST['status'];

    $sql_update = "UPDATE payment_status SET status = ? WHERE citizen_id = ?";
    $stmt = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt, 'ss', $status, $citizen_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // รีเฟรชหน้า
    header("Location: check_payment.php");
    exit();
}

$sql = "SELECT citizen_id, payment_method, slip_file, status FROM payment_status";
$result = mysqli_query($conn, $sql);


// ดึงจำนวนการชำระเงินแยกตามวิธีการชำระเงิน
$sql_payment_method = "SELECT payment_method, COUNT(*) as count FROM payment_status GROUP BY payment_method";
$result_payment = mysqli_query($conn, $sql_payment_method);

$cash_count = 0;
$transfer_count = 0;

while ($row = mysqli_fetch_assoc($result_payment)) {
    if ($row['payment_method'] == 'cash') {
        $cash_count = $row['count'];
    } elseif ($row['payment_method'] == 'transfer') {
        $transfer_count = $row['count'];
    }
}


// คำนวณจำนวนแต่ละสถานะจากฐานข้อมูล
$status_pending = 0;
$status_completed = 0;
$status_failed = 0;

$sql_status_count = "SELECT status, COUNT(*) as count FROM payment_status GROUP BY status";
$result_status = mysqli_query($conn, $sql_status_count);

while ($row = mysqli_fetch_assoc($result_status)) {
    if ($row['status'] == 'pending') {
        $status_pending = $row['count'];
    } elseif ($row['status'] == 'completed') {
        $status_completed = $row['count'];
    } elseif ($row['status'] == 'failed') {
        $status_failed = $row['count'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Home - Admin</title>
        <link href="assets/img/favicon1.png" rel="icon">
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
        <style>
                /* ปรับให้หัวข้อ h1 ดูโดดเด่น */
                h1 {
                    font-size: 2.5rem;
                    font-weight: 600;
                    color: #333; /* สีเทาเข้มเพื่อให้ดูชัดเจน */
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 10px;
                    margin-bottom: 20px;
                }

                /* ปรับปรุงสีของปุ่มในกราฟ */
                button, .btn {
                    background-color: #007bff; /* ปุ่มสีน้ำเงิน */
                    color: white;
                    border-radius: 10px;
                    padding: 5px 25px;
                }

                button:hover, .btn:hover {
                    background-color: #0056b3; /* สีเข้มขึ้นเมื่อ hover */
                }

                /* ปรับสีของตาราง */
                .table th, .table td {
                    padding: 12px;
                    text-align: center;
                    border-bottom: 1px solid #ddd;
                }

                .table thead {
                    background-color: #f8f9fa;
                    font-weight: bold;
                }

                /* ปรับลักษณะของแผนภูมิ (Chart) */
                .card-body {
                    padding: 1.5rem;
                }

                /* ทำให้กราฟแสดงผลได้ดีในหน้าจอมือถือ */
                @media (max-width: 768px) {
                    .card-header {
                        font-size: 1.2rem;
                    }

                    .card-body {
                        padding: 1rem;
                    }
                    
                    #myPieChart, #educationPieChart {
                        height: 200px;
                    }
                }
            </style>
        <script>
            // ส่งค่าจำนวนการจองชายและหญิงไปยัง JavaScript
            const maleStudents = <?php echo $male_students; ?>;
            const femaleStudents = <?php echo $female_students; ?>;
        </script>
    </head>
    <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-money-check-alt" style="margin-right: 10px; color:rgb(0, 100, 207);"></i>ตรวจสอบการจ่ายเงิน</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Payment</li>
                        </ol>
                        <div class="row">
                            <!-- Pie Chart วิธีการชำระเงิน -->
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-credit-card me-1"></i>
                                        วิธีการชำระเงิน
                                    </div>
                                    <div class="card-body">
                                        <canvas id="paymentPieChart" width="100%" height="50"
                                                data-cash="<?php echo $cash_count; ?>"
                                                data-transfer="<?php echo $transfer_count; ?>">
                                        </canvas>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <i class="fas fa-sync-alt"></i> ข้อมูลล่าสุด ณ เวลานี้
                                    </div>
                                </div>
                            </div>

                            <!-- สถานะการชำระเงิน -->
                            <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-check-circle me-1"></i>
                                        สถานะการชำระเงิน
                                    </div>
                                    <div class="card-body">
                                        <canvas id="statusPieChart" width="100%" height="50"
                                                data-pending="<?php echo $status_pending; ?>"
                                                data-completed="<?php echo $status_completed; ?>"
                                                data-failed="<?php echo $status_failed; ?>">
                                        </canvas>
                                    </div>
                                    <div class="card-footer small text-muted">
                                        <i class="fas fa-sync-alt"></i> ข้อมูลล่าสุด ณ เวลานี้
                                    </div>
                                </div>
                            </div>

                        <!-- โชว์ข้อมูลนักศึกษา -->
                        <div class="card mb-4" id="showStudent">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                ข้อมูลของนักศึกษา
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>รหัสประชาชน</th>
                                        <th>วิธีการชำระเงิน</th>
                                        <th>สลิปการชำระเงิน</th>
                                        <th>สถานะ</th>
                                        <th>อัปเดตสถานะ</th>
                                        <th>รายละเอียดการจอง</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                        if ($result->num_rows > 0) {
                                            while ($row = $result->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . htmlspecialchars($row["citizen_id"]) . "</td>";
                                                echo "<td>" . ($row["payment_method"] == "cash" ? "เงินสด" : "เงินโอน") . "</td>";

                                                // แสดงสลิปการชำระเงิน
                                                echo "<td>";
                                                if (!empty($row['slip_file'])) {
                                                    echo '<a href="/iLanding/' . htmlspecialchars($row['slip_file']) . '" target="_blank" class="text-primary">';
                                                    echo '<i class="fas fa-file-image"></i> ดูสลิป';
                                                    echo '</a>';
                                                } else {
                                                    echo 'ไม่มีสลิป';
                                                }
                                                echo "</td>";

                                                // แสดงสถานะ
                                                echo "<td>";
                                                if ($row['status'] == 'pending') {
                                                    echo '<span class="badge bg-warning">รอตรวจสอบ</span>';
                                                } elseif ($row['status'] == 'completed') {
                                                    echo '<span class="badge bg-primary">ชำระเงินเสร็จสมบูรณ์</span>';
                                                } else {
                                                    echo '<span class="badge bg-danger">ไม่ผ่าน</span>';
                                                }
                                                echo "</td>";

                                                // ปุ่มอัปเดตสถานะพร้อมไอคอน
                                                echo "<td>";
                                                echo "<button type='button' class='btn btn-warning btn-sm update-status' 
                                                        data-citizen-id='" . htmlspecialchars($row['citizen_id']) . "' 
                                                        data-status='" . htmlspecialchars($row['status']) . "' 
                                                        data-bs-toggle='modal' data-bs-target='#updateStatusModal'>
                                                        <i class='fas fa-edit'></i> อัปเดตสถานะ
                                                    </button>";
                                                echo "</td>";

                                                // ปุ่มดูรายละเอียดสินค้า
                                                echo "<td>";
                                                echo "<button type='button' class='btn btn-info btn-sm show-details' 
                                                        data-citizen-id='" . htmlspecialchars($row['citizen_id']) . "' 
                                                        data-bs-toggle='modal' data-bs-target='#productDetailsModal'>
                                                        <i class='fas fa-info-circle'></i> ดูรายละเอียดการจอง
                                                    </button>";
                                                echo "</td>";

                                                echo "</tr>";
                                                
                                            }
                                        } else {
                                            echo "<tr><td colspan='10'>No data available</td></tr>";
                                        }
                                    ?>
                                </tbody>
                                </table>
                            </div>
                        </div>

<!-- Modal อัปเดตสถานะ -->
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-labelledby="updateStatusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateStatusLabel">อัปเดตสถานะการชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="">
                    <input type="hidden" id="modal_citizen_id" name="citizen_id">

                    <div class="mb-3">
                        <label class="form-label">รหัสประชาชน</label>
                        <input type="text" id="modal_citizen_id_display" class="form-control" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select class="form-control" name="status" id="modal_status">
                            <option value="pending">รอตรวจสอบ</option>
                            <option value="completed">ชำระเงินเสร็จสมบูรณ์</option>
                            <option value="failed">ชำระเงินล้มเหลว</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                        <button type="submit" name="update_status" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Modal ดูรายละเอียดสินค้า -->
<div class="modal fade" id="productDetailsModal" tabindex="-1" aria-labelledby="productDetailsLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productDetailsLabel">รายละเอียดการจอง</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="productDetailsBody">
                <!-- ข้อมูลรายละเอียดสินค้าแสดงที่นี่ -->
            </div>
            <div id="slipImage">
                <!-- ข้อมูลสลิปแสดงที่นี่ -->
            </div>
            <!-- เพิ่มราคารวมทั้งหมดที่นี่ -->
            <div class="modal-footer">
                <p id="totalPrice"></p> <!-- แสดงราคารวมทั้งหมด -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>




                    </div>
                </main>
            </div>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                document.querySelectorAll(".show-details").forEach(button => {
                    button.addEventListener("click", function () {
                        let citizenId = this.getAttribute("data-citizen-id");

                        // ใช้ Ajax เพื่อดึงข้อมูลสินค้าจาก server
                        fetch(`get_product_details.php?citizen_id=${citizenId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.error) {
                                    document.getElementById("productDetailsBody").innerHTML = `<p>${data.error}</p>`;
                                    document.getElementById("totalPrice").innerHTML = ''; // ลบราคารวมหากเกิดข้อผิดพลาด
                                    document.getElementById("slipImage").innerHTML = ''; // ลบสลิป
                                } else {
                                    let productDetails = '';
                                    let totalPrice = 0; // ตัวแปรสำหรับเก็บราคารวมทั้งหมด

                                    // สร้างข้อมูลสินค้าแต่ละตัว
                                    data.product_details.forEach(item => {
                                        let rowTotalPrice = item.price * item.quantity;
                                        productDetails += `
                                            <p><strong>ชื่อสินค้า:</strong> ${item.product_name}</p>
                                            <p><strong>ไซต์:</strong> ${item.size}</p>
                                            <p><strong>ราคา:</strong> ${item.price}</p>
                                            <p><strong>จำนวน:</strong> ${item.quantity}</p>
                                            <p><strong>ราคารวม:</strong> ${rowTotalPrice}</p><hr>
                                        `;
                                        totalPrice += rowTotalPrice; // คำนวณราคารวมทั้งหมด
                                    });

                                    // แสดงรายละเอียดสินค้าใน modal
                                    document.getElementById("productDetailsBody").innerHTML = productDetails;

                                    // แสดงราคารวมทั้งหมด
                                    document.getElementById("totalPrice").innerHTML = `<strong>ราคารวมทั้งหมด:</strong> ${totalPrice}`;

                                    // แสดงสลิปถ้ามี
                                    if (data.slip_file) {
                                        // เพิ่ม path สำหรับสลิป
                                        let slipPath = `/iLanding/${data.slip_file}`;
                                        document.getElementById("slipImage").innerHTML = `
                                            <p><strong>สลิปการชำระเงิน:</strong></p>
                                            <img src="${slipPath}" alt="Slip" class="img-fluid" />
                                        `;
                                    } else {
                                        document.getElementById("slipImage").innerHTML = '<p>ไม่มีสลิปการชำระเงิน</p>';
                                    }
                                }
                            })
                            .catch(error => {
                                console.error("Error fetching product details:", error);
                                document.getElementById("productDetailsBody").innerHTML = `<p>ไม่สามารถโหลดข้อมูลได้</p>`;
                            });
                    });
                });
            });

            document.addEventListener("DOMContentLoaded", function () {
                // ค่าจาก PHP
                const cashCount = <?php echo $cash_count; ?>;
                const transferCount = <?php echo $transfer_count; ?>;
                const statusPending = <?php echo $status_pending; ?>;
                const statusCompleted = <?php echo $status_completed; ?>;
                const statusFailed = <?php echo $status_failed; ?>;

                // Chart วิธีการชำระเงิน
                var ctx1 = document.getElementById("paymentPieChart").getContext('2d');
                new Chart(ctx1, {
                    type: 'pie',
                    data: {
                        labels: ["เงินสด", "เงินโอน"],
                        datasets: [{
                            data: [cashCount, transferCount],
                            backgroundColor: ['#28a745', '#007bff'],
                        }],
                    },
                });

                // Chart สถานะการชำระเงิน
                var ctx2 = document.getElementById("statusPieChart").getContext('2d');
                new Chart(ctx2, {
                    type: 'pie',
                    data: {
                        labels: ["รอตรวจสอบ", "ชำระเงินเสร็จสมบูรณ์", "ไม่ผ่าน"],
                        datasets: [{
                            data: [statusPending, statusCompleted, statusFailed],
                            backgroundColor: ['#ffc107', '#007bff', '#dc3545'],
                        }],
                    },
                });
            });
            
            document.addEventListener("DOMContentLoaded", function () {
                // เมื่อคลิกปุ่ม "อัปเดตสถานะ"
                document.querySelectorAll(".update-status").forEach(button => {
                    button.addEventListener("click", function () {
                        let citizenId = this.getAttribute("data-citizen-id");
                        let status = this.getAttribute("data-status");

                        // ตั้งค่าข้อมูลใน Modal
                        document.getElementById("modal_citizen_id").value = citizenId;
                        document.getElementById("modal_citizen_id_display").value = citizenId;
                        document.getElementById("modal_status").value = status;
                    });
                });
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script src="assets/demo/chart-pie-demo.js"></script>

    </body>
</html>
                    