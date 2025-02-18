<?php
session_start();
include('condb.php');


// var_dump($_POST); 
$email = mysqli_real_escape_string($conn, $_POST['email']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

$email_check = "SELECT * FROM user WHERE email = '$email' LIMIT 1";
$result = mysqli_query($conn, $email_check);
$user = mysqli_fetch_array($result);
    if ($user['email'] === $email){
        echo "<script>alert('Email already exists');</script>";
    }
    if(!empty($username) && !empty($email) && !empty($password)){
        // ทำให้ Password อ่านไม่ออก
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $query = mysqli_query($conn,"INSERT INTO user (email, username, password, user_level, status) 
        VALUES ('{$email}', '{$username}', '{$hash}', 'admin', 'active')") or die ('query failed!');

        if($query){
            $_SESSION['message'] = 'Register Completel';
            header("location: login.php");
        }else{
            $_SESSION['message'] = 'Register could not be saved';
            header("location: login.php");
        }


    }else{
        $_SESSION['message'] = 'Input is required';
        header("location: login.php");
    }







