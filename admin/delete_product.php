<?php
include 'condb.php';

// รับข้อมูล JSON จาก client
$data = json_decode(file_get_contents('php://input'), true);
$product_id = $data['product_id'] ?? null;

if ($product_id) {
    // เริ่มต้นโดยลบข้อมูลจากตาราง product_sizes
    $sql = "DELETE FROM product_sizes WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบขนาดสินค้าได้']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // ลบข้อมูลในตาราง products
    $sql = "DELETE FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);

    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบข้อมูลสินค้าได้']);
        $stmt->close();
        $conn->close();
        exit();
    }

    // ค้นหาชื่อ product_name จากตาราง products
    $sql = "SELECT product_name FROM products WHERE product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $stmt->bind_result($product_name);
    $stmt->fetch();

    // ลบข้อมูลในตาราง categories โดยใช้ product_name ที่ค้นหาได้
    if ($product_name) {
        $sql = "DELETE FROM categories WHERE category_name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $product_name);
        if (!$stmt->execute()) {
            echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบหมวดหมู่ได้']);
            $stmt->close();
            $conn->close();
            exit();
        }
    }

    // ปิดการเชื่อมต่อ
    $stmt->close();
    $conn->close();

    echo json_encode(['success' => true]);

} else {
    echo json_encode(['success' => false, 'message' => 'ข้อมูลสินค้าไม่ถูกต้อง']);
}
?>
