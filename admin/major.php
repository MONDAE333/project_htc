<?php
session_start();
include 'condb.php';

// ดึงข้อมูลจากตาราง major
$sql = "
    SELECT 
        major_id,
        major_name,
        level,
        has_file
    FROM major
";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Majors</title>
    <link href="assets/img/favicon1.png" rel="icon">
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="sb-nav-fixed">
    <?php include 'navbar.php'; ?>
    <div id="layoutSidenav_content">
        <main>
            <div class="container-fluid px-4">
                <h1 class="mt-4">จัดการข้อมูลสาขา</h1>
                <ol class="breadcrumb mb-4">
                    <li class="breadcrumb-item active">Manage Majors</li>
                </ol>
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-table me-1"></i>
                        DataTable Majors
                    </div>
                    <div class="card-body">
                        <div class="container">
                            <div class="text-end mb-3">
                                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#majorModal" onclick="openAddForm()">เพิ่มสาขา</button>
                            </div>
                            <table class="table table-striped" id="datatablesSimple">
                                <thead>
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ชื่อสาขา</th>
                                        <th>ระดับ</th>
                                        <th>มีตะไบ</th>
                                        <th>จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($result->num_rows > 0): ?>
                                        <?php while ($row = $result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= $row['major_id'] ?></td>
                                                <td><?= $row['major_name'] ?></td>
                                                <td><?= $row['level'] ?></td>
                                                <td><?= $row['has_file'] ? 'มี' : 'ไม่มี' ?></td>
                                                <td>
                                                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#majorModal" onclick='openEditForm(<?= json_encode($row, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>)'>แก้ไข</button>
                                                    <button class="btn btn-danger btn-sm" onclick="deleteMajor(<?= $row['major_id'] ?>)">ลบ</button>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">ไม่มีข้อมูลในระบบ</td>
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
    <div class="modal fade" id="majorModal" tabindex="-1" aria-labelledby="majorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="majorForm">
                    <div class="modal-header">
                        <h5 class="modal-title" id="majorModalLabel">เพิ่ม/แก้ไขสาขา</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="major_id" name="major_id">
                        <!-- ชื่อสาขา -->
                        <div class="mb-3">
                            <label for="major_name" class="form-label">ชื่อสาขา</label>
                            <input type="text" class="form-control" id="major_name" name="major_name" required>
                        </div>
                        <!-- ระดับ -->
                        <div class="mb-3">
                            <label for="level" class="form-label">ระดับ</label>
                            <select class="form-control" id="level" name="level" required>
                                <option value="">เลือกระดับ</option>
                                <option value="ปวช.">ปวช.</option>
                                <option value="ปวส.">ปวส.</option>
                            </select>
                        </div>
                        <!-- มีตะไบ -->
                        <div class="mb-3">
                            <label for="has_file" class="form-label">มีตะไบ</label>
                            <select class="form-control" id="has_file" name="has_file" required>
                                <option value="0">ไม่มี</option>
                                <option value="1">มี</option>
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
    <!-- JS Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="js/scripts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="assets/demo/chart-area-demo.js"></script>
    <script src="assets/demo/chart-bar-demo.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="js/datatables-simple-demo.js"></script>
    <script>
        function deleteMajor(majorId){
            if(confirm('คุณต้องการลบข้อมูลนี้หรือไม่?')){
                fetch('delete_major.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ major_id: majorId })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success){
                        alert('ลบข้อมูลเรียบร้อยแล้ว');
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

        function openEditForm(major) {
            try {
                document.getElementById('major_id').value = major.major_id || '';
                document.getElementById('major_name').value = major.major_name || '';
                document.getElementById('level').value = major.level || '';
                document.getElementById('has_file').value = major.has_file ? '1' : '0';
            } catch (error) {
                console.error('Error in openEditForm:', error);
            }
        }

        function openAddForm(){
            document.getElementById('majorForm').reset();
            document.getElementById('major_id').value = '';
            document.getElementById('majorModalLabel').innerText = 'เพิ่มสาขา';
        }

        document.getElementById('majorForm').addEventListener('submit', function(e){
            e.preventDefault();
            const formData = new FormData(this);
            fetch('save_major.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success){
                    alert('บันทึกข้อมูลสำเร็จ');
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