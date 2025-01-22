<?php
include 'condb.php';

if ($conn->connect_error) {
    die("ไม่สามารถติดต่อกับเซิร์ฟเวอร์ได้: " . $conn->connect_error);
}

$response = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $citizen_id = $_POST['citizen_id'];
    $prefix = $_POST['prefix'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone_number = $_POST['phone_number'];
    $major = $_POST['major'];
    $education_level = $_POST['education_level'];

    $sql = "UPDATE student_info SET 
                citizen_id='$citizen_id', 
                prefix='$prefix', 
                first_name='$first_name', 
                last_name='$last_name', 
                phone_number='$phone_number', 
                major='$major', 
                education_level='$education_level' 
            WHERE id='$id'";

    if ($conn->query($sql) === TRUE) {
        // ส่งค่าผลลัพธ์เป็น JSON ถ้าการอัปเดตสำเร็จ
        $response['success'] = true;
        $response['message'] = "ข้อมูลได้รับการอัปเดตสำเร็จ";
    } else {
        // ส่งค่าผลลัพธ์เป็น JSON ถ้ามีข้อผิดพลาด
        $response['success'] = false;
        $response['message'] = "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }

    // ส่งข้อมูลกลับไปเป็น JSON
    echo json_encode($response);

    $conn->close();
}
?>
