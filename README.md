# 📚 ระบบจองอุปกรณ์การเรียน - Hatyai Technical College (HTCS)

ระบบจองอุปกรณ์การเรียนแบบออนไลน์สำหรับเทคนิคหาดใหญ่ ที่ช่วยให้นักศึกษาสามารถจองอุปกรณ์การเรียนได้อย่างสะดวกและรวดเร็ว พร้อมระบบการชำระเงิน การตรวจสอบสลิปอัตโนมัติ และแดชบอร์ดจัดการสำหรับผู้ดูแล

---

## 🎯 ลักษณะเด่นของระบบ

✅ **ระบบจองอัตโนมัติ** - ใช้งานง่าย จองได้ทุกเวลา  
✅ **เปิดให้บริการ 24 ชั่วโมง** - ตอบโจทย์ทุกความต้องการ  
✅ **จองได้ในไม่กี่ขั้นตอน** - สะดวก ปลอดภัย ทุกแพลตฟอร์ม  
✅ **ระบบแยกประเภทนักศึกษา** - สำหรับ ปวช. และ ปวส.  
✅ **ระบบชำระเงิน** - โอนเงิน และ เงินสด พร้อมตรวจสอบสลิป  
✅ **ตรวจสอบสลิปอัตโนมัติ** - เชื่อมกับ EasySlip API  
✅ **แดชบอร์ดแอดมิน** - จัดการและติดตามสถิติการจอง  

---

## 📋 สารบัญ

1. [ลักษณะเด่นของระบบ](#-ลักษณะเด่นของระบบ)
2. [ความต้องการของระบบ](#ความต้องการของระบบ)
3. [การติดตั้ง](#การติดตั้ง)
4. [โครงสร้างฐานข้อมูล](#-โครงสร้างฐานข้อมูล)
5. [ไฟล์และโฟลเดอร์](#-ไฟล์และโฟลเดอร์)
6. [วิธีใช้งาน](#-วิธีใช้งาน)
7. [ขั้นตอนการจอง](#-ขั้นตอนการจอง)
8. [ระบบการชำระเงิน](#-ระบบการชำระเงิน)
9. [API Integration](#-api-integration)
10. [API และ Endpoints](#-api-และ-endpoints)
11. [ความปลอดภัย](#-ความปลอดภัย)
12. [แก้ไขปัญหา](#-แก้ไขปัญหา)
13. [ข้อมูลผู้พัฒนา](#-ข้อมูลผู้พัฒนา)

---

## ความต้องการของระบบ

### 💾 ซอฟต์แวร์

- **PHP** >= 7.4
- **MySQL** >= 5.7
- **Apache Server** หรือเซิร์ฟเวอร์อื่นที่รองรับ PHP
- **Python** >= 3.7 (สำหรับ OCR บนเซิร์ฟเวอร์)
- **Web Browser** ที่สมัยใหม่ (Chrome, Firefox, Safari, Edge)

### 📦 Libraries และ Frameworks

**Frontend:**
- Bootstrap 5.3.0 - สำหรับ UI และ Responsive Design
- Chart.js 2.8.0 - สำหรับสร้างกราฟสถิติ
- SweetAlert2 - สำหรับแจ้งเตือนและ Dialog
- Google Fonts (Poppins) - สำหรับฟอนต์
- FontAwesome 6.3.0 - สำหรับไอคอน

**Backend:**
- cURL - สำหรับเรียก API

**Python Libraries:**
- Pytesseract - สำหรับ OCR
- Pillow (PIL) - สำหรับจัดการรูปภาพ
- pdfplumber - สำหรับอ่านไฟล์ PDF
- fuzzywuzzy - สำหรับเปรียบเทียบความคล้ายกันของข้อความ
- numpy - สำหรับการคำนวณ

### 🔧 Third-Party API

- **EasySlip API** - ตรวจสอบและตรวจแจงการโอนเงิน

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

```bash
# สำหรับ XAMPP
cp -r project_htc /Applications/XAMPP/htdocs/

# สำหรับ Laragon
cp -r project_htc C:\laragon\www\
```

### ขั้นตอนที่ 3: สร้างฐานข้อมูล

1. เปิด **phpMyAdmin** (http://localhost/phpmyadmin)
2. สร้างฐานข้อมูลใหม่ชื่อ `student_data`
3. Import ไฟล์ SQL หรือสร้างตารางตามรายละเอียดด้านล่าง

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

### ขั้นตอนที่ 5: ตั้งค่า EasySlip API

สร้างไฟล์ `config.php`:

```php
<?php
// EasySlip API Configuration
define('ACCESS_TOKEN', 'YOUR_EASYSLIP_ACCESS_TOKEN_HERE');
?>
```

**ข้อมูลการติดตั้ง:**
1. ลงทะเบียนที่ https://developer.easyslip.com
2. สร้าง Application และ generate Access Token
3. คัดลอก Token ใส่ใน `config.php`

### ขั้นตอนที่ 6: ติดตั้ง Python Dependencies

```bash
# ติดตั้ง Python 3
python --version

# ติดตั้ง OCR
pip install pytesseract pillow pdfplumber fuzzywuzzy numpy

# สำหรับ Windows ต้องติดตั้ง Tesseract
# ดาวน์โหลดจาก: https://github.com/UB-Mannheim/tesseract/wiki
# หลังจากติดตั้ง ให้ตั้งค่า path ใน Python:

# เพิ่มบรรทัดนี้ใน ocr_qr_reader.py
import pytesseract
pytesseract.pytesseract.pytesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
```

### ขั้นตอนที่ 7: สำหรับแอดมิน

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
    education_level VARCHAR(10) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
| created_at | TIMESTAMP | วันที่สร้างบัญชี |

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

### ตาราง: `payment_status` - สถานะการชำระเงิน

```sql
CREATE TABLE payment_status (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    citizen_id VARCHAR(13) NOT NULL UNIQUE,
    payment_method VARCHAR(20) NOT NULL DEFAULT 'pending',
    slip_file VARCHAR(255),
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    trans_ref VARCHAR(255),
    verified_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (citizen_id) REFERENCES student_info(citizen_id)
);
```

| Column | Type | Description |
|--------|------|-------------|
| payment_id | INT | รหัสการชำระเงิน |
| citizen_id | VARCHAR(13) | รหัสบัตรประชาชน |
| payment_method | VARCHAR(20) | วิธีการชำระ (cash / transfer) |
| slip_file | VARCHAR(255) | เส้นทางไฟล์สลิป |
| status | VARCHAR(20) | สถานะ (pending / completed / failed) |
| trans_ref | VARCHAR(255) | หมายเลขอ้างอิงการโอนจาก EasySlip |
| verified_at | TIMESTAMP | เวลาตรวจสอบ |
| created_at | TIMESTAMP | เวลาสร้าง |
| updated_at | TIMESTAMP | เวลาแก้ไข |

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
├── 📄 config.php                   # ไฟล์ตั้งค่า EasySlip API
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
│   ├── 📄 process_payment.php      # ประมวลผลการชำระเงิน + EasySlip API
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
│   ├── 📁 uploads/
│   │   └── slips/                  # โฟลเดอร์เก็บไฟล์สลิปที่อัปโหลด
│   └── 📁 demo/
│       ├── chart-area-demo.js
│       └── chart-bar-demo.js
│
├── 📄 head.php                     # Header HTML
├── 📄 header.php                   # Navigation Bar
├── 📄 footer.php                   # Footer
│
├── 🐍 ocr_qr_reader.py             # Python Script สำหรับ OCR/QR
│
└── 📄 README.md                    # ไฟล์นี้

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

## 🔄 ขั้นตอนการจอง

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
   ┌─────────────────┴─────────────────┐
   │                                   │
┌──▼─────────────┐  ┌────────────────▼──┐
│voc_cert_...php │  │high_voc_cert...php│
│ (ปวช./ปวส.)    │  │ (ปวช./ปวส.)       │
└──┬─────────────┘  └────────────┬──────┘
   │                              │
   └──────────────┬───────────────┘
                  │
          เลือกสินค้า/ขนาด/จำนวน
                  │
          ┌───────▼────────┐
          │save_booking.php│
          │ บันทึกการจอง   │
          │ ลงใน DB       │
          └───────┬────────┘
                  │
          ┌───────▼──────────────┐
          │confirmation.php      │
          │ยืนยอง + ชำระเงิน     │
          └───────┬──────────────┘
                  │
          เลือกวิธีชำระเงิน
                  │
     ┌────────────┴───────────┐
     │                        │
  ┌──▼──────┐         ┌──────▼──────┐
  │เงินสด   │         │โอนเงิน      │
  └──┬──────┘         └──────┬──────┘
     │                       │
     │              ┌────────▼────────┐
     │              │อัปโหลดสลิป     │
     │              └────────┬────────┘
     │                       │
     │              ┌────────▼──────────────┐
     │              │process_payment.php   │
     │              │เรียก EasySlip API    │
     │              │ตรวจสอบสลิป           │
     │              └────────┬──────────────┘
     │                       │
     └───────────┬───────────┘
                 │
        ┌────────▼──────────┐
        │ สำเร็จ! ✓         │
        │ พิมพ์ใบเสร็จได้   │
        └───────────────────┘
```

---

## 💳 ระบบการชำระเงิน

### วิธีการชำระเงิน

#### 1️⃣ **เงินสด (Cash)**

```
ขั้นตอน:
1. เลือก "เงินสด" ในฟอร์มชำระเงิน
2. บันทึกสถานะ = completed
3. ไม่ต้องอัปโหลดสลิป
4. พิมพ์ใบเสร็จได้ทันที
```

- สถานะ: `completed`

#### 2️⃣ **โอนเงิน (Transfer)**

```
ขั้นตอน:
1. เลือก "โอนเงิน" ในฟอร์มชำระเงิน
2. แสดง QR Code เพื่อชำระเงิน
3. อัปโหลดสลิปโอนเงิน
4. ระบบเรียก EasySlip API ตรวจสอบ
5. บันทึกสถานะ = completed หรือ failed
```

- สถานะเริ่มต้น: `pending`
- สถานะหลังตรวจสอบ: `completed` หรือ `failed`

### ชื่อบัญชีธนาคาร

```
ชื่อ: นายกฤตพล วิริยะภูรี
```

---

## 🔌 API Integration

### EasySlip API - ตรวจสอบสลิปโอนเงิน

ระบบใช้ **EasySlip API** เพื่อยืนยันความถูกต้องของสลิปโอนเงิน และป้องกันสลิปปลอม

#### 📡 ทำงานของ API

**ไฟล์ที่จัดการ:** `process_payment.php` (บรรทัด 48-108)

```php
// 1. อัปโหลดไฟล์สลิป
if ($_FILES['slip']['error'] == 0) {
    // ตรวจสอบประเภท (JPEG/PNG) และขนาด (2MB)
    move_uploaded_file($_FILES['slip']['tmp_name'], $slip_file);
}

// 2. เรียก EasySlip API
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://developer.easyslip.com/api/v1/verify',
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('file'=> new CURLFILE($slip_file)),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . ACCESS_TOKEN
    ),
));
$response = curl_exec($curl);
curl_close($curl);

// 3. ตรวจสอบผลลัพธ์
$result = json_decode($response, true);
if ($result['status'] == 200) {
    // ✅ ตรวจสอบสำเร็จ
    $transRef = $result['data']['transRef'];
    $amount = $result['data']['amount']['amount'];
    $receiver_name = $result['data']['receiver']['account']['name']['th'];
    
    // ตรวจสอบยอดเงินและชื่อผู้รับ
    if ($amount == $total_price && strpos($receiver_name, "นายกฤตพล ว") !== false) {
        $status = "completed";
    }
}
```

#### ⚙️ ตั้งค่า API

**ไฟล์:** `config.php`

```php
<?php
// EasySlip API Configuration
define('ACCESS_TOKEN', 'YOUR_EASYSLIP_ACCESS_TOKEN_HERE');
?>
```

**ขั้นตอนการลงทะเบียน:**

1. เข้าที่ https://developer.easyslip.com
2. สร้าง Application
3. Generate Access Token
4. คัดลอก Token ใส่ใน `config.php`

#### ✅ การตรวจสอบ

ระบบตรวจสอบ 3 ประการ:

| ประเภท | รายละเอียด | สภาพผล |
|--------|-----------|--------|
| **ความถูกต้องของสลิป** | ตรวจสอบรูปแบบ และเนื้อหา | ✅/❌ |
| **ยอดเงิน** | ตรวจสอบว่าจำนวนเงินตรงกับการจอง | ✅/❌ |
| **ชื่อผู้รับ** | ตรวจสอบว่าเป็นชื่อบัญชีที่ถูกต้อง | ✅/❌ |

#### 📊 Response จาก API

**ตัวอย่าง Response สำเร็จ:**

```json
{
    "status": 200,
    "data": {
        "transRef": "230512KDJF9304",
        "amount": {
            "amount": 1000.00,
            "currency": "THB"
        },
        "receiver": {
            "account": {
                "name": {
                    "th": "นายกฤตพล วิริยะภูรี"
                }
            }
        }
    }
}
```

**ตัวอย่าง Response ล้มเหลว:**

```json
{
    "status": 404,
    "message": "slip_not_found"
}
```

---

### OCR Verification - การตรวจสอบด้วย OCR (ตัวเลือก)

**ไฟล์:** `ocr_qr_reader.py`

สามารถใช้ Python Script เพื่อตรวจสอบสลิปโดยใช้ Optical Character Recognition (OCR)

#### 🔍 ฟังก์ชัน OCR

```python
# ฟังก์ชันอ่านข้อความจากรูปภาพ
def extract_text_from_image(image_path):
    img = Image.open(image_path)
    text = pytesseract.image_to_string(img, lang='tha+eng')
    return text

# ฟังก์ชันตรวจสอบข้อมูล
def verify_slip_data(text, expected_name, expected_amount):
    # ตรวจสอบชื่อบัญชี (ใช้ fuzzywuzzy สำหรับการเปรียบเทียบความคล้ายกัน)
    name_match = fuzz.partial_ratio(expected_name, text)
    
    # ตรวจสอบจำนวนเงิน
    amounts_found = re.findall(r"\d{1,3}(?:,\d{3})*(?:\.\d{2})?", text)
    
    return {
        "status": "success/error",
        "message": "ข้อความแจ้งผล"
    }
```

#### 🎯 การใช้งาน

```bash
python ocr_qr_reader.py <image_path> <expected_name> <expected_amount>
```

**ตัวอย่าง:**

```bash
python ocr_qr_reader.py slip.jpg "นายกฤตพล วิริยะภูรี" 1000.00
```

---

## 📡 API และ Endpoints

| Method | Endpoint | ไฟล์ | ลักษณะ | คำอธิบาย |
|--------|----------|------|-------|---------|
| GET | `/` | index.php | Public | หน้าแรก |
| POST | `/form_input_voc.php` | form_input_voc.php | Public | ฟอร์ม ปวช. |
| POST | `/form_input_high_voc.php` | form_input_high_voc.php | Public | ฟอร์ม ปวส. |
| POST | `/insert_student.php` | insert_student.php | Public | บันทึกข้อมูลผู้ใช้ |
| GET | `/voc_cert_male.php` | voc_cert_male.php | Public | เลือกสินค้า (ปวช. ชาย) |
| GET | `/voc_cert_female.php` | voc_cert_female.php | Public | เลือกสินค้า (ปวช. หญิง) |
| GET | `/high_voc_cert_male.php` | high_voc_cert_male.php | Public | เลือกสินค้า (ปวส. ชาย) |
| GET | `/high_voc_cert_female.php` | high_voc_cert_female.php | Public | เลือกสินค้า (ปวส. หญิง) |
| POST | `/save_booking.php` | save_booking.php | Public | บันทึกการจอง |
| GET | `/confirmation.php` | confirmation.php | Public | ยืนยันการจองและชำระเงิน |
| POST | `/process_payment.php` | process_payment.php | Public | ประมวลผลการชำระเงิน + EasySlip API |
| GET/POST | `/search_booking.php` | search_booking.php | Public | ค้นหาการจอง |
| GET | `/print_order.php` | print_order.php | Public | พิมพ์ใบเสร็จ |
| GET | `/admin/` | admin/index.php | Protected | แดชบอร์ดแอดมิน |
| POST | `/admin/login.php` | admin/login.php | Public | เข้าสู่ระบบ Admin |
| POST | `/admin/search_booking.php` | admin/search_booking.php | Protected | ค้นหาและแก้ไขการจอง |

---

## 🔐 ความปลอดภัย

### ⚠️ ปัญหาด้านความปลอดภัยที่มีอยู่

1. **SQL Injection Risk** - บางที่ยังใช้ `$_POST` โดยตรง
   - **ตำแหน่ง:** `save_booking.php` (บรรทัด 68-70)
   - **แก้ไข:** ใช้ Prepared Statements ทั่วไป

2. **Session Security** - ไม่มี CSRF Token
   - **แก้ไข:** เพิ่ม CSRF Protection

3. **Input Validation** - ควรตรวจสอบข้อมูลเข้าให้มากขึ้น

4. **File Upload Security** - ควรตรวจสอบนามสกุลไฟล์เพิ่มเติม

### ✅ ข้อเสนอการปรับปรุง

```php
// ✅ ใช้ Prepared Statements (ทำแล้ว)
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
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    die("CSRF token validation failed");
}

// ✅ File Upload Security
$allowed_extensions = ['jpg', 'jpeg', 'png'];
$file_extension = strtolower(pathinfo($_FILES['slip']['name'], PATHINFO_EXTENSION));
if (!in_array($file_extension, $allowed_extensions)) {
    die("Invalid file type");
}

// ✅ File size validation (ทำแล้ว)
if ($_FILES['slip']['size'] > 2000000) { // 2MB
    die("File too large");
}
```

---

## 🐛 แก้ไขปัญหา

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

### ❌ ปัญหา: "EasySlip API ไม่ทำงาน"

**วิธีแก้:**
1. ตรวจสอบ ACCESS_TOKEN ใน `config.php`
2. ตรวจสอบว่า cURL ติดตั้งบนเซิร์ฟเวอร์
3. ตรวจสอบการเชื่อมต่ออินเทอร์เน็ต
4. เข้า https://developer.easyslip.com ตรวจสอบสถานะ API

```bash
# ตรวจสอบ cURL ใน PHP
php -r "phpinfo();" | grep -i curl
```

---

### ❌ ปัญหา: "OCR ไม่ทำงาน"

**วิธีแก้:**
1. ตรวจสอบว่า Python ติดตั้ง

```bash
python --version
```

2. ติดตั้ง libraries ที่ต้องการ

```bash
pip install pytesseract pillow pdfplumber fuzzywuzzy numpy
```

3. สำหรับ Windows ติดตั้ง Tesseract

```bash
# ดาวน์โหลดจาก: https://github.com/UB-Mannheim/tesseract/wiki
# ตั้งค่า path ใน ocr_qr_reader.py:
import pytesseract
pytesseract.pytesseract.pytesseract_cmd = r'C:\Program Files\Tesseract-OCR\tesseract.exe'
```

---

## 📞 ข้อมูลผู้พัฒนา

- **ผู้พัฒนา:** MONDAE333
- **สถาบัน:** เทคนิคหาดใหญ่
- **ที่อยู่ Repository:** https://github.com/MONDAE333/project_htc
- **ประเภทโปรเจกต์:** ระบบจองอุปกรณ์การเรียน (Booking System)
- **ถูกสร้างเมื่อ:** 18 กุมภาพันธ์ 2025
- **ภาษาหลัก:** PHP (46.1%), CSS (46.3%), JavaScript (2.3%)

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
