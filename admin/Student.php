<?php
session_start();

// ตรวจสอบว่า session checklogin มีค่าเป็น true หรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}

if (isset($_GET['status'])) {
    $status = $_GET['status'];
    if ($status == 'success') {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'สำเร็จ',
                    text: 'ข้อมูลได้รับการอัปเดตสำเร็จ'
                });
              </script>";
    } elseif ($status == 'error') {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'เกิดข้อผิดพลาดในการอัปเดตข้อมูล'
                });
              </script>";
    }
}

include 'condb.php';

// ดึงข้อมูลจากตาราง student_info
$sql = "SELECT id, citizen_id, prefix, first_name, last_name, phone_number, major, education_level FROM student_info";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Sidenav Light - SB Admin</title>
        <link href="assets/img/favicon1.png" rel="icon">
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
    </head>
    <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">แก้ไขข้อมูลนักศึกษา</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Student</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Student
                            </div>
                            <div class="card-body">
                                <div class="container">
                                    <!-- <h1 class="text-center">Student</h1> -->
                                    <table class="table table-striped" id="datatablesSimple">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Citizen ID</th>
                                                <th>Prefix</th>
                                                <th>First Name</th>
                                                <th>Last Name</th>
                                                <th>Phone Number</th>
                                                <th>Major</th>
                                                <th>Education Level</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                                if ($result->num_rows > 0) {
                                                    // แสดงข้อมูลในตาราง
                                                    while($row = $result->fetch_assoc()) {
                                                        echo "<tr>";
                                                        echo "<td>" . $row["id"] . "</td>";
                                                        echo "<td>" . $row["citizen_id"] . "</td>";
                                                        echo "<td>" . $row["prefix"] . "</td>";
                                                        echo "<td>" . $row["first_name"] . "</td>";
                                                        echo "<td>" . $row["last_name"] . "</td>";
                                                        echo "<td>" . $row["phone_number"] . "</td>";
                                                        echo "<td>" . $row["major"] . "</td>";
                                                        echo "<td>" . $row["education_level"] . "</td>";
                                                        echo "<td>
                                                                <button type='button' class='btn btn-warning' onclick='openEditForm(".json_encode($row).")'>แก้ไข</button>
                                                            </td>";
                                                        echo "</tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='9'>No data available</td></tr>";
                                                }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>  
                            </div>
                        </div>
                    </div>
                </main>
            </div>
        </div>

<!-- Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <form id="studentForm" action="save_student.php" method="POST">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">แก้ไขข้อมูลนักศึกษา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="product_id" name="product_id">
                
                <!-- ID (ไม่สามารถแก้ไขได้) -->
                <div class="mb-3">
                    <label for="id" class="form-label">ID</label>
                    <input type="text" class="form-control" id="id" name="id" readonly>
                </div>

                <!-- Citizen ID -->
                <div class="mb-3">
                    <label for="citizen_id" class="form-label">Citizen ID</label>
                    <input type="text" class="form-control" id="citizen_id" name="citizen_id" required>
                </div>

                <!-- Prefix -->
                <div class="mb-3">
                    <label for="prefix" class="form-label">Prefix</label>
                    <select class="form-select" id="prefix" name="prefix" required>
                        <option value="นาย">นาย</option>
                        <option value="น.ส.">น.ส.</option>
                    </select>
                </div>

                <!-- First Name -->
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                </div>

                <!-- Last Name -->
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                </div>

                <!-- Phone Number -->
                <div class="mb-3">
                    <label for="phone_number" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                </div>

                <!-- Education Level -->
                    <div class="mb-3">
                        <label for="education_level" class="form-label">Education Level</label>
                        <select class="form-select" id="education_level" name="education_level" required onchange="fetchMajors()">
                            <option value="">เลือกระดับการศึกษา</option>
                            <option value="ปวช.">ปวช.</option>
                            <option value="ปวส.">ปวส.</option>
                        </select>
                    </div>

                    <!-- Major -->
                    <div class="mb-3">
                        <label for="major" class="form-label">Major</label>
                        <select class="form-select" id="major" name="major" required>
                            <option value="">เลือกสาขาวิชา</option>
                        </select>
                    </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
        </div>
    </div>
</div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script src="js/scripts.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
        <script src="assets/demo/chart-area-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <!-- SweetAlert2 CDN -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            function openEditForm(student) {
                try {
                    // ตั้งค่าฟิลด์ต่างๆ ในฟอร์ม
                    document.getElementById('id').value = student.id || ''; // ตั้งค่า ID
                    document.getElementById('citizen_id').value = student.citizen_id || '';

                    // ตั้งค่า prefix จากฐานข้อมูล (จะใช้ .value เพื่อเลือกค่า)
                    document.getElementById('prefix').value = student.prefix || '';

                    document.getElementById('first_name').value = student.first_name || '';
                    document.getElementById('last_name').value = student.last_name || '';
                    document.getElementById('phone_number').value = student.phone_number || '';
                    document.getElementById('major').value = student.major || '';
                    document.getElementById('education_level').value = student.education_level || '';

                    // เรียกฟังก์ชัน fetchMajors() หลังจากตั้งค่า education_level
                    fetchMajors();

                    // ตั้งชื่อ modal ให้เหมาะสม
                    document.getElementById('productModalLabel').innerText = 'แก้ไขข้อมูลนักศึกษา';

                    // เปิด modal โดยใช้ Bootstrap JavaScript
                    var myModal = new bootstrap.Modal(document.getElementById('productModal'));
                    myModal.show(); // เปิด modal
                } catch (error) {
                    console.error('Error in openEditForm:', error);
                }
            }



            function fetchMajors() {
                var education_level = document.getElementById('education_level').value;

                if (education_level) {
                    fetch('get_majors.php?education_level=' + education_level)
                        .then(response => response.json())
                        .then(data => {
                            var majorSelect = document.getElementById('major');
                            majorSelect.innerHTML = '<option value="">เลือกสาขาวิชา</option>'; // เคลียร์ตัวเลือกเก่า
                            data.majors.forEach(function(major) {
                                var option = document.createElement('option');
                                option.value = major.major_name;
                                option.textContent = major.major_name;
                                majorSelect.appendChild(option);
                            });
                        })
                        .catch(error => {
                            console.error('Error fetching majors:', error);
                        });
                } else {
                    // หากไม่ได้เลือก education_level ให้เคลียร์ major
                    document.getElementById('major').innerHTML = '<option value="">เลือกสาขาวิชา</option>';
                }
            }

            // ใช้ฟอร์มส่งข้อมูลผ่าน AJAX
            document.getElementById('studentForm').addEventListener('submit', function(e) {
                e.preventDefault(); // หยุดการรีเฟรชหน้า

                fetch('save_student.php', {
                    method: 'POST',
                    body: new FormData(document.getElementById('studentForm'))
                })
                .then(response => response.json())  // รับข้อมูลเป็น JSON
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: data.message
                        }).then(() => {
                            location.reload(); // รีเฟรชหน้าเมื่อการแจ้งเตือนแสดงเสร็จ
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message
                        }).then(() => {
                            location.reload(); // รีเฟรชหน้าเมื่อการแจ้งเตือนแสดงเสร็จ
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถติดต่อกับเซิร์ฟเวอร์ได้'
                    }).then(() => {
                        location.reload(); // รีเฟรชหน้าเมื่อการแจ้งเตือนแสดงเสร็จ
                    });
                });
            });

        </script>
    </body>
</html>

<?php
$conn->close();
?>
