<?php
include 'condb.php';

if (isset($_GET['education_level'])) {
    $education_level = $_GET['education_level'];

    // สร้างคำสั่ง SQL เพื่อดึงข้อมูล major ที่ตรงกับ education_level
    $sql = "SELECT major_name FROM major WHERE level = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $education_level);
    $stmt->execute();
    $result = $stmt->get_result();

    $majors = [];
    while ($row = $result->fetch_assoc()) {
        $majors[] = $row;
    }

    // ส่งข้อมูลในรูปแบบ JSON
    echo json_encode(['majors' => $majors]);

    $stmt->close();
}

$conn->close();
?>
