<?php
include 'condb.php';

$data = json_decode(file_get_contents('php://input'), true);

$product_id = $data['product_id'];
$size = $data['size'];

// ลบขนาดสินค้าตาม product_id และ size ที่เลือก
$sql = "DELETE FROM product_sizes WHERE product_id = ? AND size = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('is', $product_id, $size);  // 'i' สำหรับ int, 's' สำหรับ string

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'ไม่สามารถลบขนาดสินค้าได้']);
}

$stmt->close();
$conn->close();
?>
