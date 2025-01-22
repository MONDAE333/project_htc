<?php
include 'condb.php';

// รับข้อมูลจากฟอร์ม
$product_name = $_POST['product_name'] ?? '';
$category_name = $_POST['product_name'] ?? ''; // แก้ไขจาก 'product_name' เป็น 'category_name'
$price = $_POST['price'] ?? 0;
$product_image = $_FILES['product_image']['name'] ?? '';
$product_image_tmp = $_FILES['product_image']['tmp_name'] ?? '';
$upload_dir = 'uploads/';
$product_id = $_POST['product_id'] ?? null; // สำหรับการแก้ไขสินค้า
$sizes = $_POST['size'] ?? [];
$size_prices = $_POST['size_price'] ?? [];

$image_ext = strtolower(pathinfo($product_image, PATHINFO_EXTENSION));
$max_file_size = 5 * 1024 * 1024; // จำกัดขนาดไฟล์ไม่เกิน 5MB
if ($_FILES['product_image']['size'] > $max_file_size) {
    echo json_encode(['success' => false, 'message' => 'File size exceeds the limit of 5MB']);
    exit;
}

if (in_array($image_ext, ['jpg', 'jpeg', 'png'])) {
    $product_image_new = $product_image; // ใช้ชื่อไฟล์เดิม
    move_uploaded_file($product_image_tmp, $upload_dir . $product_image_new);
} else {
    $product_image_new = ""; // หากไม่มีการอัปโหลดรูปใหม่
}

// เช็คดูว่า category_name นี้มีอยู่ในตาราง categories หรือไม่
$sql_check_category = "SELECT * FROM categories WHERE category_name=?";
$stmt_check_category = $conn->prepare($sql_check_category);
$stmt_check_category->bind_param('s', $category_name);
$stmt_check_category->execute();
$result_check_category = $stmt_check_category->get_result();

if ($result_check_category->num_rows == 0) {
    // ถ้าไม่มี, เพิ่ม category ใหม่
    $sql_insert_category = "INSERT INTO categories (category_name) VALUES (?)";
    $stmt_insert_category = $conn->prepare($sql_insert_category);
    $stmt_insert_category->bind_param('s', $category_name);
    if (!$stmt_insert_category->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error inserting category']);
        exit;
    }
    $category_id = $conn->insert_id; // ใช้ category_id ที่ได้จากการเพิ่ม category ใหม่
} else {
    // ถ้ามีอยู่แล้ว, ใช้ category_id ที่มี
    $category_data = $result_check_category->fetch_assoc();
    $category_id = $category_data['category_id'];
}

// เพิ่มหรืออัปเดตสินค้า
if ($product_id) {
    // อัปเดตสินค้า
    $sql_update_product = "UPDATE products SET 
        product_name = ?, 
        category_id = ?, 
        price = ?, 
        product_image = IF(? != '', ?, product_image)
        WHERE product_id = ?";
    $stmt_update_product = $conn->prepare($sql_update_product);
    $stmt_update_product->bind_param('sidssi', $product_name, $category_id, $price, $product_image_new, $product_image_new, $product_id);
    if ($stmt_update_product->execute()) {
        // อัปเดตข้อมูลใน product_sizes
        if (isset($_POST['size_id'], $_POST['size'], $_POST['size_price'])) {
            $size_ids = $_POST['size_id'];
            $sizes = $_POST['size'];
            $size_prices = $_POST['size_price'];

            $sql_update_size = "UPDATE product_sizes SET size = ?, price = ? WHERE size_id = ?";
            $stmt_update_size = $conn->prepare($sql_update_size);

            foreach ($size_ids as $key => $size_id) {
                $size = $sizes[$key];
                $size_price = $size_prices[$key];
                $stmt_update_size->bind_param('sdi', $size, $size_price, $size_id);
                $stmt_update_size->execute();
            }
        }

        // เพิ่มขนาดสินค้าใหม่ (หากมี)
        if (isset($_POST['new_size'], $_POST['new_size_price'])) {
            $new_sizes = $_POST['new_size'];
            $new_size_prices = $_POST['new_size_price'];
            $sql_insert_size = "INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)";
            $stmt_insert_size = $conn->prepare($sql_insert_size);

            foreach ($new_sizes as $key => $new_size) {
                $new_size_price = $new_size_prices[$key];
                $stmt_insert_size->bind_param('isd', $product_id, $new_size, $new_size_price);
                $stmt_insert_size->execute();
            }
        }

        // ลบขนาดสินค้าที่ถูกลบออกจากฟอร์ม
        if (isset($_POST['deleted_size_id'])) {
            $deleted_size_ids = $_POST['deleted_size_id'];
            $sql_delete_size = "DELETE FROM product_sizes WHERE size_id = ?";
            $stmt_delete_size = $conn->prepare($sql_delete_size);

            foreach ($deleted_size_ids as $deleted_size_id) {
                $stmt_delete_size->bind_param('i', $deleted_size_id);
                $stmt_delete_size->execute();
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating product']);
    }
} else {
    // ถ้าไม่มี product_id, ให้เพิ่มสินค้าใหม่
    $sql_insert_product = "INSERT INTO products (product_name, category_id, price, product_image) VALUES (?, ?, ?, ?)";
    $stmt_insert_product = $conn->prepare($sql_insert_product);
    $stmt_insert_product->bind_param('sids', $product_name, $category_id, $price, $product_image_new);

    if ($stmt_insert_product->execute()) {
        $product_id = $conn->insert_id; // ได้ product_id หลังจากที่เพิ่มสินค้าใหม่

        // เพิ่มขนาดสินค้าใหม่ (ถ้ามี)
        if (isset($_POST['size'], $_POST['size_price'])) {
            $new_sizes = $_POST['size'];
            $new_size_prices = $_POST['size_price'];
            $sql_insert_size = "INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)";
            $stmt_insert_size = $conn->prepare($sql_insert_size);

            foreach ($new_sizes as $key => $new_size) {
                $new_size_price = $new_size_prices[$key];
                $stmt_insert_size->bind_param('isd', $product_id, $new_size, $new_size_price);
                $stmt_insert_size->execute();
            }
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting product']);
    }
}

$conn->close();
?>
