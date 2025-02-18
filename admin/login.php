<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link href="assets/img/favicon1.png" rel="icon">
    <link rel="stylesheet" href="css/style_login.css" />
    <!-- นำเข้า Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ปรับแต่งปุ่มกลับไปหน้า index */
        .back-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
        }

        .back-button i {
            font-size: 24px;
            margin-right: 8px;
        }

        .back-button:hover {
            background-color: #45a049;
        }

        /* สไตล์สำหรับ popup */
        .popup {
            display: none; /* เริ่มต้นซ่อน popup */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #f44336;
            color: white;
            border-radius: 5px;
            z-index: 1001;
        }

        .popup.active {
            display: block; /* แสดง popup เมื่อมีการเพิ่มคลาส active */
        }

        .popup button {
            background-color: #ffffff;
            color: #f44336;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 3px;
        }
    </style>
</head>
<body>
    <section class="wrapper">
        <!-- ปุ่มกลับไปหน้า index พร้อมไอคอน -->
        <a href="/iLanding/index.php" class="back-button">
            <i class="fas fa-home"></i> กลับไปหน้าแรก</a>

        <!-- ฟอร์ม login -->
        <div class="form signup">
            <header>Login</header>
            <form action="login-form.php" method="post">
                <input type="text" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="submit" value="Login" />
            </form>
        </div>
    </section>

    <!-- Popup แจ้งเตือน -->
    <div id="popup" class="popup">
        <p id="popupMessage"></p>
    </div>

    <script>
        // ฟังก์ชันเปิด Popup
        function openPopup(message) {
            const popup = document.getElementById('popup');
            const popupMessage = document.getElementById('popupMessage');
            popupMessage.textContent = message;  // ตั้งข้อความใน Popup
            popup.classList.add('active'); // เพิ่มคลาส active เพื่อแสดง popup

            // ให้ Popup หายไปหลังจาก 3 วินาที
            setTimeout(() => {
                popup.classList.remove('active'); // ลบคลาส active เพื่อซ่อน popup
            }, 3000); // หายไปหลังจาก 3000 มิลลิวินาที (3 วินาที)
        }

        // ถ้ามีข้อความจากเซสชัน
        <?php if (isset($_SESSION['message'])): ?>
            openPopup("<?php echo $_SESSION['message']; ?>");
            <?php unset($_SESSION['message']); ?> // รีเซ็ตข้อความหลังจากแสดงแล้ว
        <?php endif; ?>
    </script>
</body>
</html>
