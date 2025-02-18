<?php
session_start();
include "condb.php"; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า session checklogin มีค่าเป็น true หรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}
// ตรวจสอบว่าเป็น super_admin หรือไม่
if ($_SESSION['user_level'] !== 'super_admin') {
    echo "คุณไม่มีสิทธิ์ดำเนินการนี้";
    exit();
}

// ดึงรายชื่อผู้ใช้ทั้งหมด (ยกเว้นตัวเอง)
$sql = "SELECT id, username, user_level, status FROM user WHERE user_level != 'super_admin'";
$result = $conn->query($sql);

// ตรวจสอบว่า query สำเร็จหรือไม่
if (!$result) {
    die("เกิดข้อผิดพลาดในการดึงข้อมูล: " . $conn->error);
}

// ตรวจสอบว่า $_SESSION['message'] มีค่า
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // ลบข้อความหลังจากแสดงผลแล้ว
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>จัดการผู้ใช้</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <link href="assets/img/favicon1.png" rel="icon">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
        <div class="container mt-5">
        <h2>จัดการผู้ใช้</h2>
            <!-- เพิ่มปุ่มเพื่อดูรายชื่อ Super Admin -->
            <button class="btn btn-info mb-3" data-bs-toggle="modal" data-bs-target="#superAdminModal">ดู Super Admin</button>

            <!-- Modal สำหรับแสดงรายชื่อ Super Admin -->
            <div class="modal fade" id="superAdminModal" tabindex="-1" aria-labelledby="superAdminModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="superAdminModalLabel">รายชื่อ Super Admin</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <table class="table" id="superAdminTable">
                                <thead>
                                    <tr>
                                        <th>ชื่อผู้ใช้</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- ข้อมูล Super Admin จะถูกแสดงที่นี่ -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ตารางผู้ใช้ -->
            <table class="table">
                <thead>
                    <tr>
                        <th>ชื่อผู้ใช้</th>
                        <th>ระดับ</th>
                        <th>สถานะ</th>
                        <th>การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td>
                            <form method="post" action="update_user.php" style="display: flex; align-items: center;">
                                <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                                <select name="user_level" class="form-select" style="margin-right: 10px;">
                                    <option value="admin" <?= $row['user_level'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                    <option value="super_admin" <?= $row['user_level'] == 'super_admin' ? 'selected' : '' ?>>Super admin</option>
                                </select>
                        </td>
                        <td>
                            <select name="status" class="form-select" style="margin-right: 10px;">
                                <option value="active" <?= $row['status'] == 'active' ? 'selected' : '' ?>>เปิด</option>
                                <option value="inactive" <?= $row['status'] == 'inactive' ? 'selected' : '' ?>>ปิด</option>
                            </select>
                        </td>
                        <td>
                            <button type="submit" class="btn btn-primary btn-sm mt-1">อัปเดต</button>
                        </form>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        </main>
    </div>

    <!-- Toast สำหรับแจ้งเตือน -->
    <?php if (isset($message)): ?>
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <div id="messageToast" class="toast align-items-center text-bg-success" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?= $message ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/scripts.js"></script>
    <script>
        // แสดง Toast เมื่อโหลดหน้า
        var toastEl = document.getElementById('messageToast');
        var toast = new bootstrap.Toast(toastEl, {
            autohide: true,
            delay: 3000 // แสดง Toast เป็นเวลา 3 วินาที
        });
        toast.show();
    </script>


    <?php endif; ?>
    <script>
        $(document).ready(function() {
            $('#superAdminModal').on('show.bs.modal', function () {
                $.ajax({
                    url: 'get_super_admins.php',
                    method: 'GET',
                    success: function(response) {
                        console.log(response);  // ดูข้อมูลที่ได้รับจาก PHP
                        $('#superAdminTable tbody').html(response);
                    },
                    error: function() {
                        alert('เกิดข้อผิดพลาดในการดึงข้อมูล');
                    }
                });
            });
        });
    </script>
</body>
</html>
