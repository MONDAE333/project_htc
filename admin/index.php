<?php
    session_start();
    include 'condb.php';

// ตรวจสอบว่ามีการล็อกอินหรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}
    // ดึงข้อมูลจากตาราง student_info
    $sql = "SELECT id, citizen_id, prefix, first_name, last_name, phone_number, major, education_level FROM student_info";
    $result = $conn->query($sql);

    // คำสั่ง SQL เพื่อหาจำนวน ID จากตาราง student_info
    $sqltotal_student = "SELECT COUNT(id) AS total_students FROM student_info";
    $result_total = $conn->query($sqltotal_student);

    // ตรวจสอบผลลัพธ์
    if ($result_total->num_rows > 0) {
        // ดึงข้อมูล
        $row = $result_total->fetch_assoc();
        $total_students = $row['total_students']; // จำนวนที่ได้จากการนับ ID
    } else {
        $total_students = 0; // หากไม่พบข้อมูล
    }

    // คำสั่ง SQL เพื่อหาจำนวนชายและหญิงจากตาราง student_info
    $sql_gender = "SELECT 
                        SUM(CASE WHEN prefix = 'นาย' THEN 1 ELSE 0 END) AS male_students,
                        SUM(CASE WHEN prefix = 'น.ส.' THEN 1 ELSE 0 END) AS female_students
                    FROM student_info";
    $result_gender = $conn->query($sql_gender);

    // ตรวจสอบผลลัพธ์
    if ($result_gender->num_rows > 0) {
        $row = $result_gender->fetch_assoc();
        $male_students = $row['male_students']; // จำนวนชาย
        $female_students = $row['female_students']; // จำนวนหญิง
    } else {
        $male_students = 0;
        $female_students = 0;
    }

    // คำสั่ง SQL เพื่อหาจำนวน ปวช. และ ปวส.
    $sql_education = "SELECT 
    SUM(CASE WHEN education_level = 'ปวช.' THEN 1 ELSE 0 END) AS vocational_students,
    SUM(CASE WHEN education_level = 'ปวส.' THEN 1 ELSE 0 END) AS technical_students
    FROM student_info";
    $result_education = $conn->query($sql_education);

    // ตรวจสอบผลลัพธ์
    if ($result_education->num_rows > 0) {
    $row = $result_education->fetch_assoc();
    $vocational_students = $row['vocational_students']; // จำนวน ปวช.
    $technical_students = $row['technical_students']; // จำนวน ปวส.
    } else {
    $vocational_students = 0;
    $technical_students = 0;
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
                /* ทำให้ปุ่มอยู่ทางขวา */
                .mb-3    {
                    text-align: right;
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
                function filterByEducationLevel(level) {
                    var rows = document.querySelectorAll("#datatablesSimple tbody tr");
                    rows.forEach(function(row) {
                        var educationLevel = row.cells[7].innerText.trim();
                        if (level === "all" || educationLevel === level) {
                            row.style.display = "";
                        } else {
                            row.style.display = "none";
                        }
                    });
                }
            </script>
        </head>
        <body class="sb-nav-fixed">
        <?php include 'navbar.php'; ?>
                <div id="layoutSidenav_content">
                    <main>
                        <div class="container-fluid px-4">
                            <h1 class="mt-4"><i class="fas fa-calendar-check" style="margin-right: 10px; color:rgb(0, 100, 207);"></i>จำนวนการจอง</h1>
                            <ol class="breadcrumb mb-4">
                                <li class="breadcrumb-item active">Dashboard</li>
                            </ol>
                            <div class="row">
                                <!-- Pie Chart จำนวนการจอง ชาย/หญิง -->
                                <div class="col-lg-6">
                                    <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        จำนวนการจองของเพศชาย/หญิง
                                    </div>
                                        <div class="card-body">
                                            <canvas id="myPieChart" width="100%" height="50"></canvas>
                                        </div>
                                        <script>
                                            // ส่งค่าจำนวนการจองจาก PHP ไปยัง JavaScript
                                            const maleStudents = <?php echo $male_students; ?>;
                                            const femaleStudents = <?php echo $female_students; ?>;
                                            // สร้าง Pie Chart
                                            var ctx = document.getElementById("myPieChart").getContext('2d');
                                            var myPieChart = new Chart(ctx, {
                                                type: 'pie',
                                                data: {
                                                    labels: ["ชาย", "หญิง"], // Labels แสดงกลุ่มชายและหญิง
                                                    datasets: [{
                                                        data: [maleStudents, femaleStudents], // ใช้ค่าจาก PHP
                                                        backgroundColor: ['#007bff', '#dc3545'], // สีสำหรับแต่ละกลุ่ม
                                                    }],
                                                },
                                            });
                                        </script>
                                            <div class="card-footer small text-muted">ข้อมูลล่าสุด ณ เวลานี้</div>
                                    </div>
                                </div>
                                    <div class="col-lg-6">
                                        <div class="card mb-4">
                                        <div class="card-header">
                                            <i class="fas fa-user me-1"></i>
                                            จำนวนการจองตามระดับการศึกษา
                                        </div>
                                            <div class="card-body">
                                                <canvas id="educationPieChart" width="100%" height="50"></canvas>
                                            </div>
                                            <script>
                                                // รับค่าจำนวน ปวช. และ ปวส. จาก PHP
                                                const vocationalStudents = <?php echo $vocational_students; ?>;
                                                const technicalStudents = <?php echo $technical_students; ?>;
                                                
                                                // สร้าง Pie Chart
                                                var ctx2 = document.getElementById("educationPieChart").getContext('2d');
                                                var educationPieChart = new Chart(ctx2, {
                                                    type: 'pie',
                                                    data: {
                                                        labels: ["ปวช.", "ปวส."], // Label ของ Pie Chart
                                                        datasets: [{
                                                            data: [vocationalStudents, technicalStudents], // ใช้ค่าจาก PHP
                                                            backgroundColor: ['#28a745', '#ffc107'], // สีแสดงผล
                                                        }],
                                                    },
                                                });
                                            </script>
                                                <div class="card-footer small text-muted">ข้อมูลล่าสุด ณ เวลานี้</div>
                                        </div>
                                    </div>
                            </div>
                                <!-- Display the number of students who have booked equipment -->
                            <!-- โชว์ข้อมูลนักศึกษา -->
                            <div class="card mb-4" id="showStudent">
                                <div class="card-header">
                                    <i class="fas fa-table me-1"></i>
                                    ข้อมูลนักศึกษา
                                </div>
                                <div class="card-body">
                                <!-- ปุ่มเลือกแสดงข้อมูล ปวช. หรือ ปวส. -->
                                    <div class="mb-3">
                                        <button class="btn" onclick="filterByEducationLevel('all')">แสดงทั้งหมด</button>
                                        <button class="btn" onclick="filterByEducationLevel('ปวช.')">แสดงเฉพาะ ปวช.</button>
                                        <button class="btn" onclick="filterByEducationLevel('ปวส.')">แสดงเฉพาะ ปวส.</button>
                                    </div>
                                <table id="datatablesSimple" class="table table-striped table-bordered">
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
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ID</th>
                                            <th>Citizen ID</th>
                                            <th>Prefix</th>
                                            <th>First Name</th>
                                            <th>Last Name</th>
                                            <th>Phone Number</th>
                                            <th>Major</th>
                                            <th>Education Level</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                        <?php
                                            // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
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
                                                    echo "</tr>";
                                                }
                                            } else {
                                                echo "<tr><td colspan='8'>No data available</td></tr>";
                                            }
                                            // ปิดการเชื่อมต่อ
                                            $conn->close();
                                        ?>
                                    </tbody>
                                </table>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
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