<?php 
include 'condb.php';

// ดึงข้อมูลสาขาวิชาจากตาราง major
$sql = "SELECT * FROM major WHERE level = 'ปวช.'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ฟอร์มรับข้อมูล</title>
    <style>
        /* เพิ่ม prefix เพื่อลดความซ้ำซ้อน */
        body.form-input-voc {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center; /* จัดกลางในแนวนอน */
            align-items: center;     /* จัดกลางในแนวตั้ง */
            height: 100vh;           /* ใช้ความสูงเต็มหน้าจอ */
            background-color: #f0faff;
        }

        .form-input-voc-container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }

        .form-input-voc-container label {
            display: block;
            margin-bottom: 8px;
        }

        .form-input-voc-container input, .form-input-voc-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .form-input-voc-container button {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-input-voc-container button:hover {
            background-color: #45a049;
        }

        .form-input-voc-container .error {
            color: red;
        }
    </style>
</head>
<body class="form-input-voc">

    <div class="form-input-voc-container">
    <form action="insert_student.php" method="POST" onsubmit="return validateForm()">
        <label for="citizen_id">รหัสบัตรประชาชน:</label>
        <input type="text" id="citizen_id" name="citizen_id" maxlength="13" required
            pattern="\d{13}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        <small class="error" id="citizen_id_error"></small>

        <label for="prefix">คำนำหน้าชื่อ:</label>
        <select id="prefix" name="prefix" required>
            <option value="นาย">นาย</option>
            <option value="น.ส.">น.ส.</option>
        </select>

        <label for="first_name">ชื่อ:</label>
        <input type="text" id="first_name" name="first_name" required 
        pattern="[ก-๏a-zA-Z\s]+" title="กรุณากรอกเฉพาะตัวอักษรเท่านั้น">

        <label for="last_name">นามสกุล:</label>
        <input type="text" id="last_name" name="last_name" required
        pattern="[ก-๏a-zA-Z\s]+" title="กรุณากรอกเฉพาะตัวอักษรเท่านั้น">

        <label for="phone_number">เบอร์โทรศัพท์:</label>
        <input type="text" id="phone_number" name="phone_number" maxlength="10" required
            pattern="\d{10}" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
        <small class="error" id="phone_number_error"></small>

        <label for="major">สาขาวิชา:</label>
        <select id="major" name="major" required>
            <?php
            // แสดงตัวเลือกสาขาวิชา
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['major_name'] . "'>" . $row['major_name'] . "</option>";
                }
            } else {
                echo "<option value=''>ไม่มีข้อมูล</option>";
            }
            ?>
        </select>

        <input type="hidden" id="education_level" name="education_level" value="ปวช.">

        <button type="submit">ยืนยันข้อมูล</button>
    </form>

    </div>

    <script>
        function validateForm() {
            const citizenId = document.getElementById('citizen_id').value;
            const phoneNumber = document.getElementById('phone_number').value;
            const citizenIdError = document.getElementById('citizen_id_error');
            const phoneNumberError = document.getElementById('phone_number_error');

            // ลบข้อความข้อผิดพลาดก่อนหน้า
            citizenIdError.textContent = '';
            phoneNumberError.textContent = '';

            // ตรวจสอบความยาวของรหัสบัตรประชาชน
            if (citizenId.length !== 13) {
                citizenIdError.textContent = 'รหัสบัตรประชาชนต้องมี 13 หลัก';
                return false;
            }

            // ตรวจสอบว่ารหัสบัตรประชาชนมีแต่ตัวเลข
            if (!/^\d+$/.test(citizenId)) {
                citizenIdError.textContent = 'รหัสบัตรประชาชนต้องเป็นตัวเลขเท่านั้น';
                return false;
            }

            // ตรวจสอบความยาวของเบอร์โทรศัพท์
            if (phoneNumber.length !== 10) {
                phoneNumberError.textContent = 'เบอร์โทรศัพท์ต้องมี 10 หลัก';
                return false;
            }

            // ตรวจสอบว่าเบอร์โทรศัพท์มีแต่ตัวเลข
            if (!/^\d+$/.test(phoneNumber)) {
                phoneNumberError.textContent = 'เบอร์โทรศัพท์ต้องเป็นตัวเลขเท่านั้น';
                return false;
            }

            return true;
        }
    </script>

</body>
</html>
<?php $conn->close(); ?>

