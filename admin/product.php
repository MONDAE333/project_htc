<?php
session_start();
include 'condb.php';

// แก้ไข SQL query ให้รวม separate_by_gender และ target_group ด้วย
$sql = "
    SELECT 
        p.product_id,
        p.product_name,
        p.category_id,
        p.price,
        p.product_image,
        p.separate_by_gender,
        p.target_group,
        p.gender,
        GROUP_CONCAT(CONCAT_WS(':', ps.size, ps.price, ps.size_id)) AS sizes
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
    <title>Manage Products</title>
    <link href="assets/img/favicon1.png" rel="icon">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <style>
      h1 {
          font-size: 2.5rem;
          font-weight: 600;
          color: #333;
          border-bottom: 3px solid #007bff;
          padding-bottom: 10px;
          margin-bottom: 20px;
      }
      .table th, .table td {
          padding: 12px;
          text-align: center;
          border-bottom: 1px solid #ddd;
      }
      .table thead {
          background-color: #f8f9fa;
          font-weight: bold;
      }
      .card-body { padding: 1.5rem; }
      @media (max-width: 768px) {
          .card-header { font-size: 1.2rem; }
          .card-body { padding: 1rem; }
          #myPieChart, #educationPieChart { height: 200px; }
      }
    </style>
  </head>
  <body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
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
              <div class="container">
                <div class="text-end mb-3">
                  <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#productModal" onclick="openAddForm()">เพิ่มสินค้า</button>
                </div>
                <table class="table table-striped" id="datatablesSimple">
                  <thead>
                    <tr>
                      <th>ลำดับ</th>
                      <th>ชื่อสินค้า</th>
                      <th>หมวดหมู่</th>
                      <th>ราคา</th>
                      <th>รูปภาพ</th>
                      <th>เพศ</th>
                      <th>กลุ่มผู้ใช้</th>
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
                          <td><img src="uploads/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>" width="200"></td>
                          <td>
                            <?php
                              if ($row['gender'] == 'male') {
                                echo 'ชาย';
                              } elseif ($row['gender'] == 'female') {
                                echo 'หญิง';
                              } else {
                                echo 'ไม่ระบุเพศ';
                              }
                            ?>
                          </td>
                          <td>
                            <?php
                              if ($row['target_group'] == 'pvc') {
                                echo 'ปวช.';
                              } elseif ($row['target_group'] == 'pvs') {
                                echo 'ปวส.';
                              } else {
                                echo 'ทั้งหมด';
                              }
                            ?>
                          </td>
                          <td>
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#productModal" onclick='openEditForm(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>แก้ไข</button>
                            <button class="btn btn-info btn-sm" onclick="showDetails(<?= htmlspecialchars(json_encode($row)) ?>)">รายละเอียด</button>
                            <button class="btn btn-danger btn-sm" onclick="deleteProduct(<?= $row['product_id'] ?>)">ลบ</button>
                          </td>
                        </tr>
                      <?php endwhile; ?>
                    <?php else: ?>
                      <tr>
                        <td colspan="8" class="text-center">ไม่มีสินค้าในระบบ</td>
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
                <input type="file" class="form-control" id="product_image" name="product_image">
              </div>
              <!-- ช่องเลือกเพศสินค้า -->
              <div class="mb-3">
                <label for="gender" class="form-label">เลือกเพศสินค้า</label>
                <select class="form-control" id="gender" name="gender">
                  <option value="unisex">ไม่ระบุเพศ</option>
                  <option value="male">ชาย</option>
                  <option value="female">หญิง</option>
                </select>
              </div>
              <!-- ช่องเลือกระดับ -->
              <div class="mb-3">
                <label for="target_group" class="form-label">กลุ่มผู้ใช้สินค้า</label>
                <select class="form-control" id="target_group" name="target_group">
                  <option value="all">ทั้งหมด</option>
                  <option value="pvc">ปวช.</option>
                  <option value="pvs">ปวส.</option>
                </select>
              </div>
              <!-- ขนาดสินค้า -->
              <div class="mb-3" id="sizeFields">
                <label class="form-label">ขนาดสินค้า</label>
                <!-- ช่องกรอกสำหรับไซต์จะถูกเพิ่มโดย JavaScript -->
              </div>
              <button type="button" class="btn btn-secondary" onclick="addNewSizeField()">เพิ่มจำนวนช่อง</button>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
              <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
      // ============================
      // ฟังก์ชันสำหรับเพิ่ม field ขนาดสินค้า
      // สำหรับข้อมูลที่มีอยู่แล้ว (ในโหมดแก้ไข)
      function addExistingSizeField(size = '', price = '', sizeId = '') {
        const sizeFields = document.getElementById('sizeFields');
        const newField = document.createElement('div');
        newField.classList.add('input-group', 'mb-3');
        const hiddenInput = sizeId ? `<input type="hidden" name="size_id[]" value="${sizeId}">` : '';
        newField.innerHTML = `
          ${hiddenInput}
          <input type="text" class="form-control" name="size[]" placeholder="ขนาดสินค้า" value="${size}" required>
          <input type="number" class="form-control" name="size_price[]" placeholder="ราคา" value="${price}" required>
          <button type="button" class="btn btn-danger" onclick="removeSizeField(this)">ลบ</button>
        `;
        sizeFields.appendChild(newField);
      }
      
      // สำหรับไซต์สินค้าใหม่ (ในโหมดเพิ่ม)
      function addNewSizeField(size = '', price = '') {
        const sizeFields = document.getElementById('sizeFields');
        const newField = document.createElement('div');
        newField.classList.add('input-group', 'mb-3');
        newField.innerHTML = `
          <input type="text" class="form-control" name="new_size[]" placeholder="ขนาดสินค้า" value="${size}" required>
          <input type="number" class="form-control" name="new_size_price[]" placeholder="ราคา" value="${price}" required>
          <button type="button" class="btn btn-danger" onclick="removeSizeField(this)">ลบ</button>
        `;
        sizeFields.appendChild(newField);
      }
      
      // ============================
      // เปิดฟอร์มแก้ไข (Edit)
      function openEditForm(product) {
        try {
          document.getElementById('product_id').value = product.product_id || '';
          document.getElementById('product_name').value = product.product_name || '';
          document.getElementById('price').value = product.price || '';
          // เซ็ตค่า gender จากฐานข้อมูล (ถ้าไม่มีจะตั้งเป็น unisex)
          document.getElementById('gender').value = product.gender !== undefined ? product.gender : 'unisex';
          document.getElementById('target_group').value = product.target_group !== undefined ? product.target_group : 'all';
      
          // แปลงข้อมูล sizes (คาดว่าใน DB query เราใช้ CONCAT_WS(':', ps.size, ps.price, ps.size_id))
          const sizes = product.sizes ? product.sizes.split(',').map(sizeData => {
              const parts = sizeData.split(':');
              return { 
                size: parts[0] || '', 
                price: parts[1] || '', 
                sizeId: parts[2] || '' 
              };
          }) : [];
      
          // ลบช่องขนาดเดิมทั้งหมด
          const sizeFields = document.getElementById('sizeFields');
          while (sizeFields.firstChild) {
              sizeFields.removeChild(sizeFields.firstChild);
          }
      
          // สำหรับแต่ละไซต์ในฐานข้อมูล ใช้ addExistingSizeField
          if (sizes.length > 0) {
              sizes.forEach(item => {
                  addExistingSizeField(item.size, item.price, item.sizeId);
              });
          } else {
              // ถ้าไม่มีไซต์ ให้เพิ่มช่องว่างหนึ่งช่อง
              addNewSizeField();
          }
        } catch (error) {
          console.error('Error in openEditForm:', error);
        }
      }
      
      // เปิดฟอร์มเพิ่มสินค้าใหม่ (Add)
      function openAddForm(){
        document.getElementById('productForm').reset();
        document.getElementById('product_id').value = '';
        document.getElementById('productModalLabel').innerText = 'เพิ่มสินค้า';
        // ตั้งค่า gender เริ่มต้นเป็น unisex (ไม่ระบุเพศ)
        document.getElementById('gender').value = 'unisex';
        document.getElementById('target_group').value = 'all';
      
        // ลบช่องไซต์เดิมทั้งหมด
        const sizeFields = document.getElementById('sizeFields');
        while(sizeFields.firstChild){
          sizeFields.removeChild(sizeFields.firstChild);
        }
        // เพิ่มช่องไซต์ใหม่หนึ่งช่อง (ใช้ฟังก์ชันสำหรับไซต์ใหม่)
        addNewSizeField();
      }
      
      // ============================
      // ฟังก์ชันลบช่องไซต์สินค้า
      function removeSizeField(button) {
        const sizeField = button.closest('.input-group');
        // ตรวจสอบว่าช่องนี้เป็นไซต์ที่มีอยู่แล้ว (มี hidden input "size_id[]")
        const hiddenSizeId = sizeField.querySelector('input[name="size_id[]"]');
        if (hiddenSizeId && hiddenSizeId.value) {
          // หากเป็นไซต์ที่มีอยู่แล้ว ให้เก็บ size_id ที่ต้องการลบลงใน container hidden
          let deletedContainer = document.getElementById('deletedSizesContainer');
          if (!deletedContainer) {
            deletedContainer = document.createElement('div');
            deletedContainer.id = 'deletedSizesContainer';
            deletedContainer.style.display = 'none';
            document.getElementById('productForm').appendChild(deletedContainer);
          }
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'deleted_size_id[]';
          input.value = hiddenSizeId.value;
          deletedContainer.appendChild(input);
        }
        // ลบ element ของช่องไซต์ออกจาก DOM
        sizeField.remove();
      }
      
      // ============================
      // ฟังก์ชัน showDetails และ deleteProduct
      function showDetails(product){
        const sizes = product.sizes ? product.sizes.split(',').map(item => {
          const parts = item.split(':');
          return { size: parts[0], price: parts[1] };
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
            <p><strong>เพศ:</strong> ${product.gender == 'male' ? 'ชาย' : product.gender == 'female' ? 'หญิง' : 'ไม่ระบุเพศ'}</p>
            <p><strong>กลุ่มผู้ใช้:</strong> ${product.target_group == 'pvc' ? 'ปวช.' : product.target_group == 'pvs' ? 'ปวส.' : 'ทั้งหมด'}</p>
            <p><strong>ขนาดและราคา:</strong></p>
            ${sizeDetails}
            <p><strong>รูปภาพ:</strong></p>
            <img src="uploads/${product.product_image}" alt="${product.product_name}" width="350">
          </div>
        `;
        const modalElement = document.createElement('div');
        modalElement.classList.add('modal','fade');
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
      
      function deleteProduct(productId){
        if(confirm('คุณต้องการลบสินค้านี้หรือไม่?')){
          fetch('delete_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ product_id: productId })
          })
          .then(response => response.json())
          .then(data => {
            if(data.success){
              alert('ลบสินค้าเรียบร้อยแล้ว');
              location.reload();
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
      
      // ============================
      // Event listener สำหรับ submit ฟอร์ม
      document.getElementById('productForm').addEventListener('submit', function(e){
          e.preventDefault();
          const formData = new FormData(this);
          // ส่งข้อมูลทั้งหมดในฟอร์มไปยัง save_product.php
          fetch('save_product.php', {
              method: 'POST',
              body: formData
          })
          .then(response => response.json())
          .then(data => {
              if(data.success){
                  alert('บันทึกข้อมูลสินค้าสำเร็จ');
                  location.reload();
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