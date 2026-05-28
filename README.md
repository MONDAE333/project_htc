# 📚 ระบบจองอุปกรณ์การเรียน - Hatyai Technical College (HTCS)

ระบบจองอุปกรณ์การเรียนแบบออนไลน์สำหรับเทคนิคหาดใหญ่ ที่ช่วยให้นักศึกษาสามารถจองอุปกรณ์การเรียนได้อย่างสะดวกและรวดเร็ว พร้อมระบบการชำระเงิน และแดชบอร์ดจัดการสำหรับผู้ดูแล

---

## 🎯 ลักษณะเด่นของระบบ

✅ **ระบบจองอัตโนมัติ** - ใช้งานง่าย จองได้ทุกเวลา  
✅ **เปิดให้บริการ 24 ชั่วโมง** - ตอบโจทย์ทุกความต้องการ  
✅ **จองได้ในไม่กี่ขั้นตอน** - สะดวก ปลอดภัย ทุกแพลตฟอร์ม  
✅ **ระบบแยกประเภทนักศึกษา** - สำหรับ ปวช. และ ปวส.  
✅ **ระบบชำระเงิน** - โอนเงิน และ เงินสด  
✅ **แดชบอร์ดแอดมิน** - จัดการและติดตามสถิติการจอง  

---

## 📋 สารบัญ

- [ความต้องการของระบบ](#ความต้องการของระบบ)
- [การติดตั้ง](#การติดตั้ง)
- [โครงสร้างฐานข้อมูล](#โครงสร้างฐานข้อมูล)
- [ไฟล์และโฟลเดอร์](#ไฟล์และโฟลเดอร์)
- [วิธีใช้งาน](#วิธีใช้งาน)
- [API และ Endpoints](#api-และ-endpoints)
- [ขั้นตอนการจอง](#ขั้นตอนการจอง)
- [ระบบการชำระเงิน](#ระบบการชำระเงิน)
- [ข้อมูลผู้พัฒนา](#ข้อมูลผู้พัฒนา)

---

## ความต้องการของระบบ

### 💾 ซอฟต์แวร์

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Apache Server** หรือเซิร์ฟเวอร์อื่นที่รองรับ PHP
- **Web Browser** ที่สมัยใหม่ (Chrome, Firefox, Safari, Edge)

### 📦 Libraries และ Frameworks

- **Bootstrap 5.3.0** - สำหรับ UI และ Responsive Design
- **Chart.js 2.8.0** - สำหรับสร้างกราฟสถิติ
- **SweetAlert2** - สำหรับแจ้งเตือนและ Dialog
- **Google Fonts (Poppins)** - สำหรับฟอนต์
- **FontAwesome 6.3.0** - สำหรับไอคอน

---

## การติดตั้ง

### ขั้นตอนที่ 1: Clone Repository

```bash
git clone https://github.com/MONDAE333/project_htc.git
cd project_htc
```

### ขั้นตอนที่ 2: ตั้งค่า Web Server

1. วางโฟลเดอร์ `project_htc` ใน `htdocs` (สำหรับ XAMPP) หรือ `www` (สำหรับ Laragon)
2. หรือตั้งค่า Virtual Host ชี้ไปยังโฟลเดอร์นี้

### ขั้นตอนที่ 3: สร้างฐานข้อมูล

1. เปิด **phpMyAdmin** (http://localhost/phpmyadmin)
2. สร้างฐานข้อมูลใหม่ชื่อ `student_data`
3. Import ไฟล์ SQL (หากมี) หรือสร้างตารางตามรายละเอียดด้านล่าง

### ขั้นตอนที่ 4: ตั้งค่า Database Connection

แก้ไขไฟล์ `condb.php`:

```php
<?php
$servername = "localhost";  // ชื่อโฮสต์ฐานข้อมูล
$username = "root";         // ชื่อผู้ใช้ฐานข้อมูล
$password = "";             // รหัสผ่านฐานข้อมูล
$dbname = "student_data";   // ชื่อฐานข้อมูล

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}
?>
```

### ขั้นตอนที่ 5: สำหรับแอดมิน

1. ตั้งค่า Session Login ใน `admin/login.php`
2. สร้างบัญชีผู้ดูแลระบบ (ตารางผู้ใช้งาน)

---

## 📊 โครงสร้างฐานข้อมูล

ฐานข้อมูล: `student_data`

### ตาราง: `student_info` - ข้อมูลนักศึกษา

```sql
CREATE TABLE student_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id VARCHAR(13) UNIQUE NOT NULL,
    prefix VARCHAR(10) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone_number VARCHAR(10) NOT NULL,
    major VARCHAR(100) NOT NULL,
    education_level VARCHAR(10) NOT NULL
);
```

| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัสประจำตัวนักศึกษา |
| citizen_id | VARCHAR(13) | รหัสบัตรประชาชน (13 หลัก) |
| prefix | VARCHAR(10) | คำนำหน้าชื่อ (นาย / น.ส.) |
| first_name | VARCHAR(100) | ชื่อจริง |
| last_name | VARCHAR(100) | นามสกุล |
| phone_number | VARCHAR(10) | เบอร์โทรศัพท์ |
| major | VARCHAR(100) | สาขาวิชา |
| education_level | VARCHAR(10) | ระดับการศึกษา (ปวช. / ปวส.) |

---

### ตาราง: `bookings` - รายการจอง

```sql
CREATE TABLE bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id VARCHAR(13) NOT NULL,
    product_name VARCHAR(100) NOT NULL,
    size VARCHAR(50),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES student_info(citizen_id)
);
```

| Column | Type | Description |
|--------|------|-------------|
| booking_id | INT | รหัสการจอง |
| citizen_id | VARCHAR(13) | รหัสบัตรประชาชน |
| product_name | VARCHAR(100) | ชื่อสินค้า |
| size | VARCHAR(50) | ขนาดของสินค้า |
| quantity | INT | จำนวนที่จอง |
| price | DECIMAL(10, 2) | ราคาต่อหน่วย |
| total_price | DECIMAL(10, 2) | ราคารวม |
| booking_date | TIMESTAMP | วันที่จอง |

---

### ตาราง: `products` - สินค้า

```sql
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100) NOT NULL,
    description TEXT
);
```

| Column | Type | Description |
|--------|------|-------------|
| product_id | INT | รหัสสินค้า |
| product_name | VARCHAR(100) | ชื่อสินค้า |
| description | TEXT | คำอธิบายสินค้า |

---

### ตาราง: `product_sizes` - ขนาดและราคาของสินค้า

```sql
CREATE TABLE product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(50) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);
```

| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัสขนาด |
| product_id | INT | รหัสสินค้า |
| size | VARCHAR(50) | ขนาด |
| price | DECIMAL(10, 2) | ราคา |

---

### ตาราง: `payment_status` - สถานะการชำระเงิน

```sql
CREATE TABLE payment_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id VARCHAR(13) NOT NULL,
    payment_method VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    FOREIGN KEY (citizen_id) REFERENCES student_info(citizen_id)
);
```

| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัส |
| citizen_id | VARCHAR(13) | รหัสบัตรประชาชน |
| payment_method | VARCHAR(20) | วิธีการชำระ (cash / transfer) |
| status | VARCHAR(20) | สถานะ (pending / paid / completed) |

---

### ตาราง: `major` - สาขาวิชา

```sql
CREATE TABLE major (
    id INT AUTO_INCREMENT PRIMARY KEY,
    major_name VARCHAR(100) NOT NULL,
    level VARCHAR(10) NOT NULL
);
```

| Column | Type | Description |
|--------|------|-------------|
| id | INT | รหัส |
| major_name | VARCHAR(100) | ชื่อสาขาวิชา |
| level | VARCHAR(10) | ระดับการศึกษา (ปวช. / ปวส.) |

---

## 📁 ไฟล์และโฟลเดอร์

```
project_htc/
│
├── 📄 index.php                    # หน้าแรก
├── 📄 condb.php                    # การเชื่อมต่อฐานข้อมูล
├── 📄 config.php                   # ไฟล์ตั้งค่า
│
├── 🔑 ระบบจองของผู้ใช้
│   ├── 📄 form_input_voc.php       # ฟอร์มสำหรับ ปวช.
│   ├── 📄 form_input_high_voc.php  # ฟอร์มสำหรับ ปวส.
│   ├── 📄 insert_student.php       # บันทึกข้อมูลผู้ใช้
│   ├── 📄 voc_cert_male.php        # เลือกสินค้า (ปวช. ชาย)
│   ├── 📄 voc_cert_female.php      # เลือกสินค้า (ปวช. หญิง)
│   ├── 📄 high_voc_cert_male.php   # เลือกสินค้า (ปวส. ชาย)
│   ├── 📄 high_voc_cert_female.php # เลือกสินค้า (ปวส. หญิง)
│   ├── 📄 save_booking.php         # บันทึกการจอง
│   ├── 📄 confirmation.php         # ยืนยันการจองและชำระเงิน
│   ├── 📄 process_payment.php      # ประมวลผลการชำระเงิน
│   ├── 📄 search_booking.php       # ค้นหาการจอง
│   └── 📄 print_order.php          # พิมพ์ใบเสร็จ
│
├── 📁 admin/                       # ส่วนแอดมิน
│   ├── 📄 index.php                # แดชบอร์ด
│   ├── 📄 login.php                # เข้าสู่ระบบ
│   ├── 📄 condb.php                # เชื่อมต่อ DB (Admin)
│   ├── 📄 navbar.php               # เมนูด้านข้าง
│   ├── 📄 search_booking.php       # ค้นหาและแก้ไขการจอง
│   └── 📁 css/
│       └── 📄 styles.css           # สไตล์ Admin
│
├── 📁 assets/                      # ไฟล์สถิติและรูปภาพ
│   ├── 📁 img/
│   │   ├── Advice.png
│   │   ├── qrcode.jpg              # QR Code สำหรับชำระเงิน
│   │   └── favicon1.png
│   └── 📁 demo/
│       ├── chart-area-demo.js
│       └── chart-bar-demo.js
│
├── 📁 uploads/                     # โฟลเดอร์สำหรับอัปโหลดสลิป
│
├── 📄 head.php                     # Header HTML
├── 📄 header.php                   # Navigation Bar
├── 📄 footer.php                   # Footer
│
└── 📄 ocr_qr_reader.py             # Python Script สำหรับ OCR/QR

```

---

## 🎯 วิธีใช้งาน

### 1. เข้าใช้งานระบบผู้ใช้งาน

**URL:** `http://localhost/project_htc/`

#### ขั้นตอนการจอง:

1. **กดปุ่ม "จองอุปกรณ์การเรียน"** หรือเลือกระดับชั้น (ปวช. / ปวส.)
2. **กรอกข้อมูลส่วนตัว:**
   - รหัสบัตรประชาชน (13 หลัก)
   - คำนำหน้าชื่อ
   - ชื่อ-นามสกุล
   - เบอร์โทรศัพท์
   - สาขาวิชา

3. **เลือกสินค้า:**
   - เลือกขนาดที่ต้องการ
   - ระบุจำนวน (1-5 ชิ้น)
   - ราคาจะคำนวณอัตโนมัติ

4. **ยืนยันการจอง:**
   - ตรวจสอบข้อมูลและสินค้า
   - เลือกวิธีการชำระเงิน

5. **ชำระเงิน:**
   - เงินสด: ส่งเรียบร้อย
   - โอนเงิน: สแกน QR Code และอัปโหลดสลิป

### 2. ค้นหาการจอง

**URL:** `http://localhost/project_htc/search_booking.php`

- ใส่หมายเลขบัตรประชาชน
- ตรวจสอบข้อมูลและสถานะการชำระเงิน
- ดาวน์โหลด/พิมพ์ใบเสร็จ

### 3. เข้าใช้แดชบอร์ดแอดมิน

**URL:** `http://localhost/project_htc/admin/`

#### ส่วนแอดมิน:
- **Dashboard:** ดูสถิติการจอง (ชาย/หญิง, ปวช./ปวส.)
- **ค้นหาและแก้ไข:** ค้นหารายการจองและแก้ไขข้อมูล
- **ข้อมูลนักศึกษา:** ดูรายชื่อนักศึกษาทั้งหมด
- **กรอง:** กรองข้อมูลตามระดับการศึกษา

---

## 📡 API และ Endpoints

| Method | Endpoint | ชื่อไฟล์ | คำอธิบาย |
|--------|----------|---------|---------|
| GET | `/` | index.php | หน้าแรก |
| POST | `/insert_student.php` | insert_student.php | บันทึกข้อมูลผู้ใช้ |
| POST | `/save_booking.php` | save_booking.php | บันทึกการจอง |
| GET/POST | `/search_booking.php` | search_booking.php | ค้นหาการจอง |
| POST | `/confirmation.php` | confirmation.php | ยืนยันการจองและชำระเงิน |
| POST | `/process_payment.php` | process_payment.php | ประมวลผลการชำระเงิน |
| GET | `/print_order.php` | print_order.php | พิมพ์ใบเสร็จ |
| GET | `/admin/` | admin/index.php | แดชบอร์ดแอดมิน |
| POST | `/admin/search_booking.php` | admin/search_booking.php | แก้ไขการจอง (Admin) |

---

## 🔄 ขั้นตอนการจอง (User Flow)

```
┌─────────────────────────────────────────────────────────┐
│                      หน้าแรก (index.php)                │
│            เลือกระดับชั้น (ปวช. / ปวส.)                 │
└────────────────────┬────────────────────────────────────┘
                     │
        ┌────────────┴────────────┐
        │                         │
   ┌────▼─────────┐      ┌───────▼────────┐
   │ form_input   │      │ form_input_    │
   │ _voc.php     │      │ high_voc.php   │
   │  (ปวช.)      │      │  (ปวส.)        │
   └────┬─────────┘      └────────┬───────┘
        │                         │
        └────────────┬────────────┘
                     │
        ┌────────────▼────────────┐
        │   insert_student.php    │
        │   บันทึกข้อมูลผู้ใช้     │
        │   ตรวจสอบเพศ/ระดับ      │
        └────────────┬────────────┘
                     │
        ┌────────────┴─────────────────────────────────┐
        │                                              │
   ┌────▼──────────────┐  ┌──────────────────────┐   │
   │ voc_cert_male.php │  │ voc_cert_female.php  │   │
   │ (ปวช. ชาย)       │  │ (ปวช. หญิง)        │   │
   └────┬──────────────┘  └────────────┬─────────┘   │
        │                              │              │
        └──────────────┬───────────────┘              │
                       │                              │
                  ┌────▼────────────────┐             │
                  │high_voc_cert_male   │             │
                  │(ปวส. ชาย)          │             │
                  └────┬─────────────────┘             │
                       │                              │
                  ┌────▼────────────────┐             │
                  │high_voc_cert_female │             │
                  │(ปวส. หญิง)         │             │
                  └────┬─────────────────┘             │
                       │                              │
                       │ ← เลือกสินค้า/ขนาด/จำนวน    │
                       │                              │
        ┌──────────────▼──────────────────────────────┘
        │
        │   กรอกข้อมูลการจอง
        │   เลือก size และจำนวน
        │
   ┌────▼──────────────┐
   │ save_booking.php  │
   │ บันทึกการจอง      │
   │ ลงใน DB          │
   └────┬──────────────┘
        │
   ┌────▼──────────────┐
   │confirmation.php   │
   │ยืนยอง + ชำระเงิน  │
   └────┬──────────────┘
        │
        │ เลือกวิธีชำระ (เงินสด / โอนเงิน)
        │
   ┌────▼──────────────────┐
   │process_payment.php    │
   │ประมวลผลการชำระเงิน   │
   │บันทึกสถานะ            │
   └────┬──────────────────┘
        │
   ┌────▼──────────────┐
   │  สำเร็จ! ✓        │
   │ พิมพ์ใบเสร็จได้    │
   └───────────────────┘
```

---

## 💳 ระบบการชำระเงิน

### วิธีการชำระเงิน

#### 1️⃣ **เงินสด (Cash)**
- ชำระเงินสดแล้วปิดการจอง
- สถานะ: `completed`

#### 2️⃣ **โอนเงิน (Transfer)**
- สแกน QR Code ที่ให้ไว้
- อัปโหลดสลิปโอนเงิน
- สถานะ: `pending` → `paid` (หลังตรวจสอบ)

### ชื่อบัญชีธนาคาร
```
ชื่อ: นายกฤตพล วิริยะภูรี
```

---

## 📊 ตัวอย่างข้อมูล

### ตัวอย่างข้อมูล Major (สาขาวิชา)

```sql
INSERT INTO major (major_name, level) VALUES
('ช่างไฟฟ้า', 'ปวช.'),
('ช่างยนต์', 'ปวช.'),
('ช่างก่อสร้าง', 'ปวช.'),
('ช่างสถาปัตยกรรม', 'ปวส.'),
('ช่างไฟฟ้าระบบควบคุม', 'ปวส.');
```

### ตัวอย่างข้อมูล Products

```sql
INSERT INTO products (product_name, description) VALUES
('ชุดยูนิฟอร์ม', 'ชุดยูนิฟอร์มเทคนิคหาดใหญ่'),
('รองเท้า', 'รองเท้านักเรียนเทคนิค'),
('หมวก', 'หมวกเทคนิคหาดใหญ่');

INSERT INTO product_sizes (product_id, size, price) VALUES
(1, 'S', 150.00),
(1, 'M', 150.00),
(1, 'L', 150.00),
(2, '7', 300.00),
(2, '8', 300.00),
(3, 'One Size', 100.00);
```

---

## 🔐 ความปลอดภัย

### ⚠️ ปัญหาด้านความปลอดภัยที่มีอยู่

1. **SQL Injection Risk** - บางที่ยังใช้ `$_POST` โดยตรง
   - **แก้ไข:** ใช้ Prepared Statements ทั่วไป

2. **Session Security** - ไม่มี CSRF Token
   - **แก้ไข:** เพิ่ม CSRF Protection

3. **Input Validation** - ควรตรวจสอบข้อมูลเข้าให้มากขึ้น

### ✅ ข้อเสนอการปรับปรุง

```php
// ✅ ใช้ Prepared Statements
$stmt = $conn->prepare("SELECT * FROM student_info WHERE citizen_id = ?");
$stmt->bind_param("s", $citizen_id);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Input Validation
$citizen_id = preg_replace('/[^0-9]/', '', $_POST['citizen_id']);
if (strlen($citizen_id) !== 13) {
    die("Invalid citizen ID");
}

// ✅ CSRF Token
session_start();
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF token validation failed");
}
```

---

## 🐛 แก้ไขปัญหา (Troubleshooting)

### ❌ ปัญหา: "ไม่พบการเชื่อมต่อฐานข้อมูล"

**วิธีแก้:**
1. ตรวจสอบว่า MySQL/MariaDB ทำงานอยู่
2. ตรวจสอบชื่อผู้ใช้และรหัสผ่านใน `condb.php`
3. ตรวจสอบชื่อฐานข้อมูล

```bash
# เปิด MySQL
mysql -u root -p
CREATE DATABASE student_data;
```

---

### ❌ ปัญหา: "ไฟล์ไม่พบ 404"

**วิธีแก้:**
1. ตรวจสอบเส้นทาง URL
2. ตรวจสอบชื่อไฟล์ (ตรงกับตัวพิมพ์เล็ก/ใหญ่)
3. ตรวจสอบสิทธิ์ไฟล์

---

### ❌ ปัญหา: "Session หายไป"

**วิธีแก้:**
1. ตรวจสอบว่า `session_start()` อยู่ด้านบนของไฟล์ PHP
2. ลบไฟล์ session เก่า
3. ตรวจสอบ Session Timeout

---

## 📞 ข้อมูลผู้พัฒนา

- **ผู้พัฒนา:** MONDAE333
- **สถาบัน:** เทคนิคหาดใหญ่
- **ที่อยู่:** https://github.com/MONDAE333/project_htc
- **ประเภทโปรเจกต์:** ระบบจองอุปกรณ์การเรียน (Booking System)
- **ถูกสร้างเมื่อ:** 18 กุมภาพันธ์ 2025

---

## 📄 ใบอนุญาต

โปรเจกต์นี้ยังไม่มีใบอนุญาตที่ระบุไว้ (Open Source)

---

## 🤝 การมีส่วนร่วม

ยินดีต้อนรับการช่วยเหลือและข้อเสนอแนะ! สามารถ:

1. Fork Repository
2. สร้าง Pull Request
3. เปิด Issue สำหรับปัญหาหรือคำแนะนำ

---

## 📞 ติดต่อ

หากมีคำถามหรือข้อเสนอแนะ สามารถติดต่อได้ที่:

- **GitHub Issues:** https://github.com/MONDAE333/project_htc/issues
- **GitHub Discussion:** https://github.com/MONDAE333/project_htc/discussions

---

## 🎓 หมายเหตุ

ระบบนี้ออกแบบมาเพื่อศึกษาและพัฒนาทักษะด้าน Web Development โปรดใช้ในสภาพแวดล้อมการพัฒนา (Development Environment) ก่อนนำไปใช้งานจริง

---

**ขอบคุณที่ใช้งาน HTCS Booking System! 🙏**

