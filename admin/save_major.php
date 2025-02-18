<?php
include 'condb.php';

$major_id = $_POST['major_id'];
$major_name = $_POST['major_name'];
$level = $_POST['level'];
$has_file = $_POST['has_file'];

if ($major_id) {
    // Update existing record
    $sql = "UPDATE major SET major_name = ?, level = ?, has_file = ? WHERE major_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $major_name, $level, $has_file, $major_id);
} else {
    // Insert new record
    $sql = "INSERT INTO major (major_name, level, has_file) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $major_name, $level, $has_file);
}

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $stmt->error]);
}

$stmt->close();
$conn->close();
?>