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

        // เช็คว่าผู้ใช้ถูกปิดใช้งานหรือไม่
        if ($user['status'] === 'inactive') {
            $_SESSION['message'] = 'บัญชีของคุณถูกปิดใช้งาน ไม่สามารถเข้าสู่ระบบได้';
            header("Location: login.php");
            exit();
        }

        // เช็ค password ว่าตรงกับใน database หรือไม่
        if (password_verify($password, $user['password'])) {
            // เก็บข้อมูลเข้าสู่ session
            $_SESSION['checklogin'] = true;
            $_SESSION['id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_level'] = $user['user_level']; // เก็บระดับผู้ใช้ไว้ใน Session
            
            header("Location: index.php");
            exit();
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