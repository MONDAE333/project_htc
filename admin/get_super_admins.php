<?php
include "condb.php"; // เชื่อมต่อฐานข้อมูล

// ดึงรายชื่อ Super Admin
$sql = "SELECT id, username FROM user WHERE user_level = 'super_admin'";
$result = $conn->query($sql);

// ตรวจสอบว่า query สำเร็จหรือไม่
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . htmlspecialchars($row['username']) . "</td></tr>";
    }
} else {
    echo "<tr><td colspan='1'>ไม่มี Super Admin ในระบบ</td></tr>";
}
?>
