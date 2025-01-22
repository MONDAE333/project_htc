<?php
include 'condb.php';

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
        <link href="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/style.min.css" rel="stylesheet" />
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">Home</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                            <!-- Display the number of students who have booked equipment -->
                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-info text-white mb-4">
                                        <div class="card-body">
                                            มีนักศึกษาจองอุปกรณ์แล้ว <?php echo $total_students; ?> คน
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-info text-white mb-4">
                                        <div class="card-body">
                                            มีนักศึกษาชายจองอุปกรณ์แล้ว <?php echo $male_students; ?> คน<br>
                                            มีนักศึกษาหญิงจองอุปกรณ์แล้ว <?php echo $female_students; ?> คน
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- โชว์ข้อมูลนักศึกษา -->
                        <div class="card mb-4" id="showStudent">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
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
    </body>
</html>
