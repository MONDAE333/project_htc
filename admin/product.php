<?php
include 'condb.php';

// // ดึงข้อมูลจากตาราง products
// $sql = "SELECT * FROM products";
// $result = $conn->query($sql);

$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.category_id,
        p.price,
        p.product_image,
        GROUP_CONCAT(CONCAT(ps.size, ':', ps.price)) AS sizes
    FROM products p
    LEFT JOIN product_sizes ps ON p.product_id = ps.product_id
    GROUP BY p.product_id
";
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
        <link href="css/styles.css" rel="stylesheet" />
        <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    </head>
    <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
            </div>
            <div id="layoutSidenav_content">
                <main>
                    <div class="container-fluid px-4">
                        <h1 class="mt-4">แก้ไข/เพิ่มสินค้า</h1>
                        <ol class="breadcrumb mb-4">
                            <li class="breadcrumb-item active">Manage Products</li>
                        </ol>
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-table me-1"></i>
                                DataTable Products
                            </div>
                            <div class="card-body">
                                <div class="container mt-5">
                                    <h1 class="text-center">Manage Products</h1>
                                    <div class="text-end mb-3">
                                        <!-- ปุ่มเพิ่มสินค้า -->
                                        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openAddForm()">เพิ่มสินค้า</button>
                                    </div>
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>ลำดับ</th>
                                                <th>ชื่อสินค้า</th>
                                                <th>หมวดหมู่</th>
                                                <th>ราคา</th>
                                                <th>รูปภาพ</th>
                                                <th>จัดการ</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($result->num_rows > 0): ?>
                                                <?php while ($row = $result->fetch_assoc()): ?>
                                                    <tr>
                                                        <td><?= $row['product_id'] ?></td>
                                                        <td><?= $row['product_name'] ?></td>
                                                        <td><?= $row['category_id'] ?></td>
                                                        <td><?= number_format($row['price'], 2) ?> บาท</td>
                                                        <td><img src="/iLanding/assets/img/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>" width="200"></td>
                                                        <td>
                                                            <!-- ปุ่มแก้ไข -->
                                                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" onclick='openEditForm(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>แก้ไข</button>

                                                            <!-- ปุ่มแสดงรายละเอียด -->
                                                            <button class="btn btn-info btn-sm" onclick="showDetails(<?= htmlspecialchars(json_encode($row)) ?>)">รายละเอียด</button>

                                                            <!-- ปุ่มลบ -->
                                                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $row['product_id'] ?>)">ลบ</button>
                                                        </td>
                                                    </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="6" class="text-center">ไม่มีสินค้าในระบบ</td>
                                                </tr>
                                            <?php endif; ?>
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
            <form id="productForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalLabel">เพิ่ม/แก้ไขสินค้า</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="product_id" name="product_id">
                    
                    <!-- ชื่อสินค้า -->
                    <div class="mb-3">
                        <label for="product_name" class="form-label">ชื่อสินค้า</label>
                        <input type="text" class="form-control" id="product_name" name="product_name" required>
                    </div>
                    
                    
                    <!-- ราคา -->
                    <div class="mb-3">
                        <label for="price" class="form-label">ราคา</label>
                        <input type="number" class="form-control" id="price" name="price" required>
                    </div>
                    
                    <!-- รูปภาพ -->
                    <div class="mb-3">
                        <label for="product_image" class="form-label">รูปภาพ</label>
                        <input type="file" class="form-control" id="product_image" name="product_image" required>
                    </div>

                    <!-- ขนาดสินค้า -->
                    <div class="mb-3" id="sizeFields">
                        <label for="size" class="form-label">ขนาดสินค้า</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="size[]" placeholder="ขนาดสินค้า" required>
                            <input type="number" class="form-control" name="size_price[]" placeholder="ราคา" required>
                        </div>
                    </div>

                    <button type="button" class="btn btn-secondary" onclick="addSizeField()">เพิ่มจำนวนช่อง</button>
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
        <script>

            function openEditForm(product) {
                try {
                    // ตั้งค่าฟิลด์ต่างๆ ในฟอร์ม
                    document.getElementById('product_id').value = product.product_id || '';
                    document.getElementById('product_name').value = product.product_name || '';
                    document.getElementById('price').value = product.price || '';
                    
                    // ตรวจสอบว่า product.sizes มีข้อมูลหรือไม่
                    const sizes = product.sizes ? product.sizes.split(',').map(sizeData => {
                        const [size, price] = sizeData.split(':');
                        return { size, price };
                    }) : [];

                    // ลบช่องขนาดเดิม
                    const sizeFields = document.getElementById('sizeFields');
                    while (sizeFields.firstChild) {
                        sizeFields.removeChild(sizeFields.firstChild);
                    }

                    // เพิ่มช่องกรอกขนาดตามข้อมูลที่ได้จากฐานข้อมูล
                    sizes.forEach((sizeData, index) => {
                        addSizeField(sizeData.size, sizeData.price, index === sizes.length - 1); // เพิ่มช่องกรอกขนาด
                    });

                    console.log(product); // ตรวจสอบค่าที่ส่งมา
                } catch (error) {
                    console.error('Error in openEditForm:', error);
                }
            }


            function openAddForm() {
                // รีเซ็ตฟอร์มทั้งหมด
                document.getElementById('productForm').reset();
                document.getElementById('product_id').value = ''; // เคลียร์ค่า product_id
                document.getElementById('productModalLabel').innerText = 'เพิ่มสินค้า'; // แก้ไขชื่อหัวข้อ modal

                // ลบช่องกรอกขนาดสินค้า (ถ้ามี)
                const sizeFields = document.getElementById('sizeFields');
                while (sizeFields.firstChild) {
                    sizeFields.removeChild(sizeFields.firstChild);
                }
                
                // เพิ่มช่องกรอกขนาดสินค้าเริ่มต้น
                addSizeField();
            }

            // ฟังก์ชันสำหรับเพิ่มช่องกรอกขนาดสินค้า
            function addSizeField(size = '', price = '') {
                const sizeFields = document.getElementById('sizeFields');
                const newField = document.createElement('div');
                newField.classList.add('input-group', 'mb-3');
                newField.innerHTML = `
                    <input type="text" class="form-control" name="size[]" placeholder="ขนาดสินค้า" value="${size}" required>
                    <input type="number" class="form-control" name="size_price[]" placeholder="ราคา" value="${price}" required>
                    <button type="button" class="btn btn-danger" onclick="removeSizeField(this)">ลบ</button>
                `;
                sizeFields.appendChild(newField);
            }


            
            // ฟังก์ชันลบช่องขนาด
            function removeSizeField(button) {
                // ลบช่องที่ผู้ใช้เลือก
                const sizeField = button.closest('.input-group');
                sizeField.remove();
            }

            function showDetails(product) {
                const sizes = product.sizes ? product.sizes.split(',').map(sizeData => {
                    const [size, price] = sizeData.split(':');
                    return { size, price };
                }) : [];

                const sizeDetails = sizes.length
                    ? sizes.map(s => `<p>${s.size}: ${s.price} บาท</p>`).join('')
                    : '<p>ไม่มีข้อมูลขนาดสินค้า</p>';

                const detailContent = `
                    <div>
                        <h5>รายละเอียดสินค้า</h5>
                        <p><strong>ชื่อสินค้า:</strong> ${product.product_name}</p>
                        <p><strong>หมวดหมู่:</strong> ${product.category_id}</p>
                        <p><strong>ราคา:</strong> ${product.price} บาท</p>
                        <p><strong>ขนาดและราคา:</strong></p>
                        ${sizeDetails}
                        <p><strong>รูปภาพ:</strong></p>
                        <img src="/iLanding/assets/img/${product.product_image}" alt="${product.product_name}" width="350">
                    </div>
                `;

                const modalElement = document.createElement('div');
                modalElement.classList.add('modal', 'fade');
                modalElement.innerHTML = `
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">รายละเอียดสินค้า</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">${detailContent}</div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            </div>
                        </div>
                    </div>
                `;
                document.body.appendChild(modalElement);

                const modal = new bootstrap.Modal(modalElement);
                modal.show();

                modalElement.addEventListener('hidden.bs.modal', () => {
                    modalElement.remove();
                });
            }

            function deleteProduct(productId) {
                if (confirm('คุณต้องการลบสินค้านี้หรือไม่?')) {
                    fetch('delete_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ product_id: productId }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('ลบสินค้าเรียบร้อยแล้ว');
                            location.reload(); // รีเฟรชหน้าเว็บ
                        } else {
                            alert('เกิดข้อผิดพลาด: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('เกิดข้อผิดพลาดขณะลบข้อมูล');
                    });
                }
            }

            document.getElementById('productForm').addEventListener('submit', function (e) {
                e.preventDefault(); // ป้องกันการส่งฟอร์มแบบปกติ
                const formData = new FormData(this);

                fetch('save_product.php', {
                    method: 'POST',
                    body: formData,
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('บันทึกสำเร็จ!');
                        location.reload(); // รีเฟรชหน้าเพื่อแสดงข้อมูลล่าสุด
                    } else {
                        alert('เกิดข้อผิดพลาด: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดขณะบันทึกข้อมูล');
                });
            });

        </script>
    </body>
</html>

<?php
$conn->close();
?>
