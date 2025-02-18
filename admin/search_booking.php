<?php
session_start();
include 'condb.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า session checklogin มีค่าเป็น true หรือไม่
if (!isset($_SESSION['checklogin']) || $_SESSION['checklogin'] !== true) {
    $_SESSION['message'] = 'กรุณาเข้าสู่ระบบก่อนเข้าใช้งาน';
    header("Location: login.php");
    exit();
}

if (isset($_POST['search'])) {
    $citizen_id = $_POST['citizen_id'];
    
    $stmt = $conn->prepare("SELECT booking_id, citizen_id, bookings.product_name, bookings.size, bookings.quantity, bookings.price, bookings.total_price, products.product_id FROM bookings INNER JOIN products ON bookings.product_name = products.product_name WHERE citizen_id = ?");
    $stmt->bind_param("s", $citizen_id);
    $stmt->execute();
    $result = $stmt->get_result();
}

if (isset($_POST['update'])) {
    $old_data = [];
    $new_data = [];
    foreach ($_POST['booking_id'] as $index => $booking_id) {
        $product_name = $_POST['product_name'][$index];
        $size = $_POST['size'][$index];
        // ตรวจสอบว่า 'quantity' มีค่าหรือไม่ หากไม่มีกำหนดเป็นค่าเริ่มต้นเป็น 1
        $quantity = isset($_POST['quantity'][$index]) ? $_POST['quantity'][$index] : 1; 
        $quantity = min(5, $quantity); // จำกัดจำนวนสูงสุดที่ 5
        $price = $_POST['price'][$index];
        $total_price = $quantity * $price;

        // ดึงข้อมูลเก่ามา
        $stmt = $conn->prepare("SELECT product_name, size, price, total_price, quantity FROM bookings WHERE booking_id = ?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $old_result = $stmt->get_result()->fetch_assoc();

        // อัปเดตข้อมูล
        $stmt = $conn->prepare("UPDATE bookings SET product_name = ?, size = ?, quantity = ?, price = ?, total_price = ? WHERE booking_id = ?");
        $stmt->bind_param("ssiddi", $product_name, $size, $quantity, $price, $total_price, $booking_id);
        $stmt->execute();

        // เก็บข้อมูลเก่าและใหม่
        $old_data[] = $old_result;
        $new_data[] = ['product_name' => $product_name, 'size' => $size, 'quantity' => $quantity, 'price' => $price, 'total_price' => $total_price];
    }

    $old_total_price_all = 0; // กำหนดค่าราคารวมทั้งหมดเก่า
    $new_total_price_all = 0; // กำหนดค่าราคารวมทั้งหมดใหม่
    $price_difference = 0; // กำหนดค่าความแตกต่างของราคารวมทั้งหมด
    
    $comparison = ''; // เปลี่ยนแปลงของแต่ละรายการ
    
    for ($i = 0; $i < count($old_data); $i++) {
        $old = $old_data[$i];
        $new = $new_data[$i];
    
        // คำนวณราคารวมทั้งหมดก่อนและหลังการอัปเดต
        $old_total_price_all += $old['total_price'];
        $new_total_price_all += $new['total_price'];
    
        // ใช้ htmlspecialchars() เพื่อป้องกันปัญหาตัวอักษรพิเศษ
        $comparison .= "
        <p><strong>ชื่อสินค้า:</strong> " . htmlspecialchars($new['product_name']) . "</p>
        <p><strong>ขนาด:</strong> " . htmlspecialchars($old['size']) . " → " . htmlspecialchars($new['size']) . "</p>
        <p><strong>จำนวนสินค้า:</strong> " . htmlspecialchars($old['quantity']) . " → " . htmlspecialchars($new['quantity']) . "</p>
        <p><strong>ราคา:</strong> " . htmlspecialchars($old['price']) . " → " . htmlspecialchars($new['price']) . "</p>
        <p><strong>ราคารวม:</strong> " . htmlspecialchars($old['total_price']) . " → " . htmlspecialchars($new['total_price']) . "</p>
        <hr>";
    }
    // หลังจากการคำนวณราคารวมทั้งหมดเก่าและใหม่
    $price_difference = $old_total_price_all - $new_total_price_all; // ความแตกต่างระหว่างเก่าและใหม่

    // กำหนดข้อความแจ้งเตือนตามเงื่อนไข
    if ($price_difference < 0) {
        $payment_message = "คุณต้องจ่ายเพิ่มอีก " . number_format(abs($price_difference), 2) . " บาท";
    } elseif ($price_difference > 0) {
        $payment_message = "คุณต้องถอนเงินอีก " . number_format(abs($price_difference), 2) . " บาท";
    } else {
        $payment_message =  number_format(abs($price_difference), 2) . " บาท";
    }

    // แปลงข้อมูลที่อาจมีอักขระพิเศษ
    $comparison = str_replace(["\r", "\n", "'"], ["\\r", "\\n", "\\'"], $comparison);

    // แสดงการแจ้งเตือนด้วย SweetAlert2 รวมราคารวมทั้งหมดเก่าและใหม่
    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: 'อัปเดตข้อมูลสำเร็จ!',
                html: '$comparison' + 
                    '<p><strong>ราคารวมทั้งหมดก่อนการอัปเดต:</strong> " . htmlspecialchars($old_total_price_all) . "</p>' + 
                    '<p><strong>ราคารวมทั้งหมดหลังการอัปเดต:</strong> " . htmlspecialchars($new_total_price_all) . "</p>' +
                    '<p><strong>ความแตกต่างระหว่างราคารวมทั้งหมด:</strong> ' + '$payment_message' + '</p>',
                icon: 'success',
                confirmButtonText: 'ตกลง'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'search_booking.php';
                }
            });
        });
    </script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ค้นหาและแก้ไขข้อมูลการจอง</title>
    <link href="assets/img/favicon1.png" rel="icon">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
            <h1 class="mt-4">แก้ไขสินค้าที่ถูกจอง</h1>
            <ol class="breadcrumb mb-4">
                <li class="breadcrumb-item active">Edit booking</li>
            </ol>
            <form method="POST" class="mb-3">
                <div class="mb-3">
                    <label for="citizen_id" class="form-label">หมายเลขบัตรประชาชน:</label>
                    <input type="text" id="citizen_id" name="citizen_id" class="form-control" maxlength="13" required
                    pattern="\d{13}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
                <button type="submit" name="search" class="btn btn-primary">ค้นหา</button>
            </form>
            
            <?php if (isset($result) && $result->num_rows > 0): 
                $row = $result->fetch_assoc(); ?>
                <h4>หมายเลขบัตรประชาชน: <?php echo htmlspecialchars($row['citizen_id']); ?></h4>
                <form method="POST">
                    <input type="hidden" name="citizen_id" value="<?php echo htmlspecialchars($row['citizen_id']); ?>">
                    <table class="table table-bordered mt-3">
                        <thead>
                            <tr>
                                <th>ชื่อสินค้า</th>
                                <th>ขนาด</th>
                                <th>จำนวน</th>
                                <th>ราคา</th>
                                <th>ราคารวม</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php do { 
                                $product_id = $row['product_id'];
                                $size_options = "";
                                $stmt = $conn->prepare("SELECT size, price FROM product_sizes WHERE product_id = ?");
                                $stmt->bind_param("i", $product_id);
                                $stmt->execute();
                                $sizes_result = $stmt->get_result();
                                while ($size_row = $sizes_result->fetch_assoc()) {
                                    $selected = ($size_row['size'] == $row['size']) ? "selected" : "";
                                    $size_options .= "<option value='{$size_row['size']}' data-price='{$size_row['price']}' $selected>{$size_row['size']}</option>";
                                }
                            ?>
                            <tr>
                                <input type="hidden" name="booking_id[]" value="<?php echo $row['booking_id']; ?>">
                                <td>
                                    <?php echo $row['product_name']; ?>
                                    <input type="hidden" name="product_name[]" value="<?php echo $row['product_name']; ?>">
                                </td>
                                <td>
                                    <select name="size[]" class="form-control size-select">
                                        <?php echo $size_options; ?>
                                    </select>
                                </td>
                                <td><input type="number" name="quantity[]" value="<?php echo $row['quantity']; ?>" class="form-control quantity-input" min="1" max="5"></td>
                                <td><input type="number" name="price[]" value="<?php echo $row['price']; ?>" class="form-control price-input" readonly></td>
                                <td class="total-price"><?php echo $row['total_price']; ?></td>
                            </tr>
                            <?php } while ($row = $result->fetch_assoc()); ?>
                        </tbody>
                    </table>
                    <button type="submit" name="update" class="btn btn-success">อัปเดตข้อมูลทั้งหมด</button>
                </form>
            <?php elseif (isset($_POST['search'])): ?>
                <p class="text-danger">ไม่พบข้อมูลการจอง</p>
            <?php endif; ?>
            </div>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.size-select').forEach(select => {
            select.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const priceInput = this.closest('tr').querySelector('.price-input');
                const quantityInput = this.closest('tr').querySelector('.quantity-input');
                const totalPriceCell = this.closest('tr').querySelector('.total-price');
                priceInput.value = selectedOption.getAttribute('data-price');
                totalPriceCell.textContent = (quantityInput.value * priceInput.value).toFixed(2);
            });
        });

        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('input', function () {
                if (this.value > 5) this.value = 5; // จำกัดจำนวนสูงสุดที่ 5
                if (this.value < 1) this.value = 1; // จำกัดจำนวนขั้นต่ำที่ 1
                const row = this.closest('tr');
                const price = row.querySelector('.price-input').value;
                const totalPriceCell = row.querySelector('.total-price');
                totalPriceCell.textContent = (this.value * price).toFixed(2);
            });
        });
    </script>
</body>
</html>
