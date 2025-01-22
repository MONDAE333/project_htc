<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link rel="stylesheet" href="css/style_login.css" />
    <!-- นำเข้า Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ปรับแต่งปุ่มกลับไปหน้า index */
        .back-button {
            position: fixed;            /* ตรึงตำแหน่งปุ่ม */
            bottom: 20px;               /* ระยะห่างจากขอบล่าง */
            right: 20px;                /* ระยะห่างจากขอบขวา */
            padding: 10px 20px;
            background-color: #4CAF50;  /* สีพื้นหลังของปุ่ม */
            color: white;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            z-index: 1000;              /* ทำให้ปุ่มอยู่เหนือองค์ประกอบอื่น */
            display: flex;              /* ใช้ Flexbox เพื่อจัดตำแหน่ง */
            align-items: center;        /* จัดแนวไอคอนและข้อความให้ตรงกลางแนวตั้ง */
        }

        .back-button i {
            font-size: 24px; /* ขนาดของไอคอน */
            margin-right: 8px; /* ระยะห่างระหว่างไอคอนและข้อความ */
        }

        .back-button:hover {
            background-color: #45a049;   /* เปลี่ยนสีเมื่อ hover */
        }
    </style>
</head>
<body>
    <section class="wrapper">
        <!-- ปุ่มกลับไปหน้า index พร้อมไอคอน -->
        <a href="/iLanding/index.php" class="back-button">
            <i class="fas fa-home"></i> กลับไปหน้าแรก</a>

        <!-- ส่วนที่เหลือของฟอร์ม login/signup -->
        <div class="form signup">
            <header>Login</header>
            <form action="login-form.php" method="post">
                <input type="text" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <input type="submit" value="Login" />
            </form>
        </div>
        <div class="form login"></div>
    </section>

    <script>
        const wrapper = document.querySelector(".wrapper"),
            signupHeader = document.querySelector(".signup header"),
            loginHeader = document.querySelector(".login header");

        loginHeader.addEventListener("click", () => {
            wrapper.classList.add("active");
        });
        signupHeader.addEventListener("click", () => {
            wrapper.classList.remove("active");
        });
    </script>
</body>
</html>
