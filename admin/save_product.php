<?php 
include 'condb.php';

// กำหนด header ให้ส่งกลับเป็น JSON เสมอ
header('Content-Type: application/json');

// รับข้อมูลจากฟอร์ม
$product_name        = $_POST['product_name'] ?? '';
$category_name       = $_POST['product_name'] ?? '';
$price               = $_POST['price'] ?? 0;
$product_id          = $_POST['product_id'] ?? null; // สำหรับการแก้ไขสินค้า
$separate_by_gender  = isset($_POST['separate_by_gender']) ? (int)$_POST['separate_by_gender'] : 0; // 1 = แยกเพศ, 0 = ไม่แยกเพศ
$gender              = $_POST['gender'] ?? ''; // สำหรับแยกเพศ
$target_group        = $_POST['target_group'] ?? 'all'; // 'pvc' สำหรับ ปวช. , 'pvs' สำหรับ ปวส. , 'all' สำหรับทั้งหมด

// จัดการกับรูปภาพ
$product_image       = $_FILES['product_image']['name'] ?? '';
$product_image_tmp   = $_FILES['product_image']['tmp_name'] ?? '';
$upload_dir          = 'uploads/';
$product_image_new   = "";

if (!empty($product_image)) {
    
    $image = $product_image;
    $image_tmp = $product_image_tmp;
    $image_ext = strtolower(pathinfo($image, PATHINFO_EXTENSION));
    $max_file_size = 5 * 1024 * 1024; // 5MB
    $product_image_new   = "";
    if ($_FILES['product_image']['size'] > $max_file_size) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds the limit of 5MB']);
        exit;
    }
    
    if (in_array($image_ext, ['jpg', 'jpeg', 'png'])) {
        $product_image_new = $image; // ใช้ชื่อไฟล์เดิม (หรือปรับเปลี่ยนตามต้องการ)
        move_uploaded_file($image_tmp, $upload_dir . $product_image_new);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid image type']);
        exit;
    }
} else {
    // ถ้าไม่มีการอัพโหลด ให้ใช้รูปเดิมจากฐานข้อมูล (เฉพาะในกรณีแก้ไข)
    if ($product_id) {
        $sql = "SELECT product_image FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $product_image_new = $row['product_image'];
        }
        $stmt->close();
    }
}

// เช็คและอัปเดต category
$sql_check_category = "SELECT * FROM categories WHERE category_name = ?";
$stmt_check_category = $conn->prepare($sql_check_category);
$stmt_check_category->bind_param('s', $category_name);
$stmt_check_category->execute();
$result_check_category = $stmt_check_category->get_result();

if ($result_check_category->num_rows == 0) {
    // เพิ่ม category ใหม่
    $sql_insert_category = "INSERT INTO categories (category_name) VALUES (?)";
    $stmt_insert_category = $conn->prepare($sql_insert_category);
    $stmt_insert_category->bind_param('s', $category_name);
    if (!$stmt_insert_category->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error inserting category']);
        exit;
    }
    $category_id = $conn->insert_id;
    $stmt_insert_category->close();
} else {
    $category_data = $result_check_category->fetch_assoc();
    $category_id = $category_data['category_id'];
}
$stmt_check_category->close();

// แปลงข้อมูลสำหรับขนาดสินค้า
// สำหรับสินค้าเดิมอาจมีการส่ง size_id, size และ size_price สำหรับข้อมูลที่มีอยู่แล้วcategory_name
$existing_size_ids = isset($_POST['size_id']) ? $_POST['size_id'] : [];
$existing_sizes    = isset($_POST['size']) ? $_POST['size'] : [];
$existing_prices   = isset($_POST['size_price']) ? $_POST['size_price'] : [];

// สำหรับขนาดสินค้าใหม่ (ถ้ามี) ให้ใช้ field new_size, new_size_price
$new_sizes       = isset($_POST['new_size']) ? $_POST['new_size'] : [];
$new_size_prices = isset($_POST['new_size_price']) ? $_POST['new_size_price'] : [];

// เริ่มการเพิ่ม/อัปเดตสินค้า
if ($product_id) {
    // อัปเดตสินค้า (รวม field separate_by_gender และ target_group ด้วย)
    if (!empty($product_image_new)) {
        $sql_update_product = "UPDATE products SET 
            product_name = ?, 
            category_id = ?, 
            price = ?, 
            product_image = ?, 
            separate_by_gender = ?, 
            target_group = ? ,
            gender = ?
            WHERE product_id = ?";
        $stmt_update_product = $conn->prepare($sql_update_product);
        $stmt_update_product->bind_param('sidsissi', 
            $product_name, $category_id, $price, $product_image_new, 
            $separate_by_gender, $target_group, $gender, $product_id
        );
    } else {
        $sql_update_product = "UPDATE products SET 
            product_name = ?, 
            category_id = ?, 
            price = ?, 
            separate_by_gender = ?, 
            target_group = ? ,
            gender = ?
            WHERE product_id = ?";
        $stmt_update_product = $conn->prepare($sql_update_product);
        $stmt_update_product->bind_param('sidissi', 
            $product_name, $category_id, $price, 
            $separate_by_gender, $target_group, $gender,$product_id
        );
    }    


    if ($stmt_update_product->execute()) {
        $stmt_update_product->close();
        // อัปเดตขนาดสินค้าที่มีอยู่แล้ว (ถ้ามี)
        if (!empty($existing_size_ids)) {
            $sql_update_size = "UPDATE product_sizes SET size = ?, price = ? WHERE size_id = ?";
            $stmt_update_size = $conn->prepare($sql_update_size);
            foreach ($existing_size_ids as $key => $size_id) {
                $size       = $existing_sizes[$key];
                $size_price = $existing_prices[$key];
                $stmt_update_size->bind_param('sdi', $size, $size_price, $size_id);
                $stmt_update_size->execute();
            }
            $stmt_update_size->close();
        }
        
        // เพิ่มขนาดสินค้าใหม่ (ถ้ามี)
        if (!empty($new_sizes) && !empty($new_size_prices)) {
            $sql_insert_size = "INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)";
            $stmt_insert_size = $conn->prepare($sql_insert_size);
            foreach ($new_sizes as $key => $new_size) {
                if (!empty($new_size) && isset($new_size_prices[$key]) && $new_size_prices[$key] !== '') {
                    $new_size_price = $new_size_prices[$key];
                    $stmt_insert_size->bind_param('isd', $product_id, $new_size, $new_size_price);
                    $stmt_insert_size->execute();
                }
            }
            $stmt_insert_size->close();
        }
        
        // ลบขนาดสินค้าที่ถูกลบออกจากฟอร์ม (ส่งมาจาก hidden field deleted_size_id)
        if (isset($_POST['deleted_size_id']) && !empty($_POST['deleted_size_id'])) {
            $deleted_size_ids = $_POST['deleted_size_id'];
            $sql_delete_size = "DELETE FROM product_sizes WHERE size_id = ?";
            $stmt_delete_size = $conn->prepare($sql_delete_size);

            foreach ($deleted_size_ids as $deleted_size_id) {
                $stmt_delete_size->bind_param('i', $deleted_size_id);
                $stmt_delete_size->execute();
            }
            $stmt_delete_size->close();
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error updating product']);
    }
} else {
    // เพิ่มสินค้าใหม่ (รวม field separate_by_gender และ target_group ด้วย)
    $sql_insert_product = "INSERT INTO products (product_name, category_id, price, product_image, separate_by_gender, target_group, gender) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert_product = $conn->prepare($sql_insert_product);
    // Binding: product_name (s), category_id (i), price (d), product_image (s), separate_by_gender (i), target_group (s)
    $stmt_insert_product->bind_param('sidsiss', $product_name, $category_id, $price, $product_image_new, $separate_by_gender, $target_group, $gender);
    
    if ($stmt_insert_product->execute()) {
        $product_id = $conn->insert_id;
        $stmt_insert_product->close();
        
        // เพิ่มขนาดสินค้าใหม่ (ถ้ามี)
        if (!empty($existing_sizes) && !empty($existing_prices)) {
            $sql_insert_size = "INSERT INTO product_sizes (product_id, size, price) VALUES (?, ?, ?)";
            $stmt_insert_size = $conn->prepare($sql_insert_size);
            foreach ($existing_sizes as $key => $new_size) {
                if (!empty($new_size) && isset($existing_prices[$key]) && $existing_prices[$key] !== '') {
                    $new_size_price = $existing_prices[$key];
                    $stmt_insert_size->bind_param('isd', $product_id, $new_size, $new_size_price);
                    $stmt_insert_size->execute();
                }
            }
            $stmt_insert_size->close();
        }
        
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error inserting product']);
    }
}

$conn->close();
?>
