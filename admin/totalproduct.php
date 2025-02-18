<?php
include 'condb.php';
session_start();

// ตรวจสอบว่า session checklogin มีค่าเป็น true หรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}

$sql_with_gender = "
SELECT 
    c.category_name,
    b.product_name,
    b.size,
    SUM(b.quantity) AS total_reserved_quantity,
    CASE 
        WHEN p.separate_by_gender = FALSE THEN 'All'
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
JOIN 
    products p ON b.product_name = p.product_name
GROUP BY 
    c.category_name, b.product_name, b.size, 
    CASE 
        WHEN p.separate_by_gender = FALSE THEN 'All'
        WHEN si.prefix = 'นาย' THEN 'Male'
        WHEN si.prefix = 'น.ส.' THEN 'Female'
        ELSE 'Other'
    END
ORDER BY 
    c.category_name, b.product_name, b.size, gender;
";

$sql_no_gender = "
SELECT 
    c.category_name,
    b.product_name,
    b.size,
    SUM(b.quantity) AS total_reserved_quantity,
    COUNT(b.booking_id) AS total_bookings
FROM 
    bookings b
JOIN 
    categories c ON b.product_name = c.category_name
GROUP BY 
    c.category_name, b.product_name, b.size
ORDER BY 
    c.category_name, b.product_name, b.size;
";

// สำหรับแยกเพศ
$result_with_gender = $conn->query($sql_with_gender);

// สำหรับไม่แยกเพศ
$result_no_gender = $conn->query($sql_no_gender);

// ตรวจสอบผลลัพธ์
if ($result_with_gender->num_rows > 0) {
    // การแสดงผล
} else {
    echo "No data available";
}

if ($result_no_gender->num_rows > 0) {
    // การแสดงผล
} else {
    echo "No data available";
}


// สร้างอาร์เรย์เก็บข้อมูลสำหรับเพศชายและหญิง
$product_names_male = [];
$total_quantities_male = [];

$product_names_female = [];
$total_quantities_female = [];

$product_names_other = [];
$total_quantities_other = [];

// ดึงข้อมูลแยกตามเพศ
$result_with_gender = $conn->query($sql_with_gender);

if ($result_with_gender->num_rows > 0) {
    while ($row = $result_with_gender->fetch_assoc()) {
        if ($row["gender"] == "Male") {
            $product_names_male[] = $row["product_name"];
            $total_quantities_male[] = $row["total_reserved_quantity"];
        } elseif ($row["gender"] == "Female") {
            $product_names_female[] = $row["product_name"];
            $total_quantities_female[] = $row["total_reserved_quantity"];
        } else {
            $product_names_other[] = $row["product_name"];
            $total_quantities_other[] = $row["total_reserved_quantity"];
        }
    }
} else {
    echo "No data available";
}


// ดึงข้อมูลจากฐานข้อมูล (SQL ไม่แยกเพศ)
$product_names_no_gender = [];
$total_quantities_no_gender = [];

$result_no_gender = $conn->query($sql_no_gender);  // ใช้ $sql_no_gender แทน $sql_without_gender

if ($result_no_gender->num_rows > 0) {
    while ($row = $result_no_gender->fetch_assoc()) {
        $product_names_no_gender[] = $row["product_name"];
        $total_quantities_no_gender[] = $row["total_reserved_quantity"];
    }
} else {
    echo "No data available";
}

?>

<script>
// ส่งข้อมูลจาก PHP ไปยัง JavaScript สำหรับกราฟแยกตามเพศ
var productNamesMale = <?php echo json_encode($product_names_male); ?>;
var totalQuantitiesMale = <?php echo json_encode($total_quantities_male); ?>;

var productNamesFemale = <?php echo json_encode($product_names_female); ?>;
var totalQuantitiesFemale = <?php echo json_encode($total_quantities_female); ?>;

var productNamesOther = <?php echo json_encode($product_names_other); ?>;
var totalQuantitiesOther = <?php echo json_encode($total_quantities_other); ?>;


// ส่งข้อมูลจาก PHP ไปยัง JavaScript สำหรับกราฟไม่แยกเพศ
var productNamesNoGender = <?php echo json_encode($product_names_no_gender); ?>;
var totalQuantitiesNoGender = <?php echo json_encode($total_quantities_no_gender); ?>;

// สร้างกราฟแยกเพศ
var ctx = document.getElementById('myBarChart').getContext('2d');
var myBarChart;

// สร้างกราฟแยกตามเพศ
function showGenderChart() {
    if (myBarChart) myBarChart.destroy(); // ลบกราฟเดิม

    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productNamesMale.concat(productNamesFemale), // รวมชื่อสินค้าจากชายและหญิง
            datasets: [{
                label: 'จำนวนที่ถูกจอง (ชาย)',
                data: totalQuantitiesMale, // จำนวนที่ถูกจองจากเพศชาย
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // สีฟ้าสำหรับชาย
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'จำนวนที่ถูกจอง (หญิง)',
                data: totalQuantitiesFemale, // จำนวนที่ถูกจองจากเพศหญิง
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // สีชมพูสำหรับหญิง
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'จำนวนที่ถูกจอง (อื่นๆ)',
                data: totalQuantitiesOther, // จำนวนที่ถูกจองจากเพศอื่นๆ
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // สีเขียวสำหรับอื่นๆ
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0, // บังคับให้เริ่มที่ศูนย์
                    ticks: {
                        stepSize: 1 // บังคับให้เพิ่มทีละ 1
                    }
                }
            }
        }
    });
}


// สร้างกราฟไม่แยกเพศ
function showNoGenderChart() {
    if (myBarChart) myBarChart.destroy(); // ลบกราฟเดิม

    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productNamesNoGender, // ใช้ชื่อสินค้าที่ส่งมาจาก PHP
            datasets: [{
                label: 'จำนวนที่ถูกจอง',
                data: totalQuantitiesNoGender, // ใช้จำนวนสินค้าที่ถูกจองที่ส่งมาจาก PHP
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// เริ่มต้นแสดงกราฟที่ไม่แยกเพศ
showNoGenderChart();
</script>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Totalproduct - Admin</title>
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
                #myBarChart {
                    width: 100% !important;  /* ทำให้กราฟกว้างเต็มหน้าจอ */
                    height: 600px;  /* ปรับความสูงตามที่ต้องการ */
                }
        </style>
        <script>
            // ดึงข้อมูลจาก PHP
            var productNames = <?php echo json_encode($product_names); ?>;
            var totalQuantities = <?php echo json_encode($total_quantities); ?>;

            // สร้างกราฟ
            var ctx = document.getElementById('myBarChart');
            var myBarChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: productNames, // ชื่อสินค้า
                    datasets: [{
                        label: 'จำนวนที่ถูกจอง',
                        data: totalQuantities, // จำนวนที่ถูกจอง
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false, // ทำให้กราฟปรับตามขนาด
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        </script>

    </head>
    <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4"><i class="fas fa-shopping-cart" style="margin-right: 10px; color:rgb(0, 100, 207);"></i>สินค้าที่ถูกจอง</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Totalproduct</li>
                        </ol>
                        <div class="col-lg-6">
                                <div class="card mb-4">
                                    <div class="card-header">
                                        <i class="fas fa-chart-bar me-1"></i>
                                        Bar Chart Example
                                    </div>
                                    
                                    <!-- ปุ่มสำหรับเลือกกราฟ -->
                                    <button onclick="showGenderChart()">แสดงกราฟแยกตามเพศ</button>
                                    <button onclick="showNoGenderChart()">แสดงกราฟรวม</button>

                                    <!-- Canvas สำหรับแสดงกราฟ -->
                                    <canvas id="myBarChart" width="100%" height="50"></canvas>
                                    <div class="card-footer small text-muted">Updated yesterday at 11:59 PM</div>
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
                                        $result = $conn->query($sql_with_gender);

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
                                        <button onclick="window.location.href='products_page.php';" class="btn btn-primary">แสดงสินค้าทั้งหมด</button>

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
        <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
        <script src="js/datatables-simple-demo.js"></script>
        <script src="assets/demo/chart-bar-demo.js"></script>
        
    </body>
</html>
