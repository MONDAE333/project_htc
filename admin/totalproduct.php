<?php
include 'condb.php';

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



// SQL Query ที่ใช้
$sql = "
SELECT 
    c.category_name,
    b.product_name,
    b.size,
    SUM(b.quantity) AS total_reserved_quantity,
    CASE 
        WHEN si.prefix = 'นาย' THEN 'Male'
        WHEN si.prefix = 'น.ส.' THEN 'Female'
        ELSE 'Other'
    END AS gender,
    COUNT(b.booking_id) AS total_bookings
FROM 
    bookings b
JOIN 
    categories c ON b.product_name = c.category_name
JOIN 
    student_info si ON b.citizen_id = si.citizen_id
GROUP BY 
    c.category_name, b.product_name, b.size, gender
ORDER BY 
    c.category_name, b.product_name, b.size, gender;

";

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Totalproduct - Admin</title>
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
                        <h1 class="mt-4">สินค้าที่ถูกจอง</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Totalproduct</li>
                        </ol>
                            <!-- จำนวนการจองแยกตามเพศ -->
                            <div class="row">
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-info text-white mb-4">
                                        <div class="card-body">
                                            มีนักศึกษาจองอุปกรณ์แล้ว <?php echo $total_students; ?> คน
                                        </div>
                                        <div class="card-footer d-flex align-items-center justify-content-between">
                                            <a class="small text-white stretched-link" href="#showStudent">View Details</a>
                                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-3 col-md-6">
                                    <div class="card bg-info text-white mb-4">
                                        <div class="card-body">
                                            มีนักศึกษาชายจองอุปกรณ์แล้ว <?php echo $male_students; ?> คน<br>
                                            มีนักศึกษาหญิงจองอุปกรณ์แล้ว <?php echo $female_students; ?> คน
                                        </div>
                                        <div class="card-footer d-flex align-items-center justify-content-between">
                                            <a class="small text-white stretched-link" href="#showStudent">View Details</a>
                                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <div class="card mb-4" id="showStudent">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Example
                            </div>
                            <div class="card-body">
                                <table id="datatablesSimple">
                                    <thead>
                                        <tr>
                                            <th>ชื่อสินค้า</th>
                                            <th>ไซต์</th>
                                            <th>จำนวนสินค้าที่ถูกจองโดยเพศ</th>
                                            <th>เพศ</th>
                                            <th>จำนวนครั้งที่ถูกจอง</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>ชื่อสินค้า</th>
                                            <th>ไซต์</th>
                                            <th>จำนวนสินค้าที่ถูกจองโดยเพศ</th>
                                            <th>เพศ</th>
                                            <th>จำนวนครั้งที่ถูกจอง</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
                                    <?php
                                        // รันคำสั่ง SQL
                                        $result = $conn->query($sql);

                                        // ตรวจสอบว่ามีข้อมูลหรือไม่
                                        if ($result->num_rows > 0) {
                                            // แสดงข้อมูลแต่ละแถว
                                            while($row = $result->fetch_assoc()) {
                                                echo "<tr>
                                                        <td>" . $row["product_name"] . "</td>
                                                        <td>" . $row["size"] . "</td>
                                                        <td>" . $row["total_reserved_quantity"] . "</td>
                                                        <td>" . $row["gender"] . "</td>
                                                        <td>" . $row["total_bookings"] . "</td>
                                                    </tr>";
                                            }
                                            
                                            // ปิดแท็กตาราง
                                            echo "</table>";
                                        } else {
                                            echo "<tr><td colspan='8'>No data available</td></tr>";
                                        }
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
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
    </body>
</html>
