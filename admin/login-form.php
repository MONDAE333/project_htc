<?php
session_start();
include 'condb.php';

$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

if (!empty($email) && !empty($password)) {
    $query = mysqli_query($conn, "SELECT * FROM user WHERE email ='$email'");

    if (!$query) {
        die('Query Failed: ' . mysqli_error($conn)); 
    }

    $row = mysqli_num_rows($query); 

    if ($row != 0) {
        $user = mysqli_fetch_assoc($query);

        // เช็ค password ว่าตรงกับใน database หรือไม่
        if (password_verify($password, $user['password'])) {
            // เก็บข้อมูลเข้าสู่ session
            $_SESSION['checklogin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            // // ไปที่หน้า index.php หลังจากล็อกอินสำเร็จ
            // header("Location: index.php");
            // exit();
        } else {
            $_SESSION['message'] = 'อีเมลหรือรหัสผ่านของคุณไม่ถูกต้อง'; // แจ้งเตือนรหัสผ่านไม่ถูกต้อง
            header("Location: login.php");
            exit();
        }
    } else {
        $_SESSION['message'] = 'อีเมลหรือรหัสผ่านของคุณไม่ถูกต้อง'; // แจ้งเตือนไม่พบอีเมล
        header("Location: login.php");
        exit();
    }
} else {
    $_SESSION['message'] = 'กรุณากรอกอีเมลหรือรหัสผ่าน'; // แจ้งเตือนข้อมูลไม่ครบ
    header("Location: login.php");
    exit();
}
