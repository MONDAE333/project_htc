<!DOCTYPE html>
<html lang="en">

<?php 
$page_title = "Home"; // กำหนดชื่อหน้า
include 'head.php'; 
include 'header.php'; 
?>

  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content" data-aos="fade-up" data-aos-delay="200">
              <div class="company-badge mb-4">
                <i class="bi bi-gear-fill me-2"></i>
                คำแนะนำการจอง
              </div>

              <h1 class="mb-4">
                คำแนะนำการจอง <br>

                <span class="accent-text">โปรดอ่านก่อนทำการจอง</span>
              </h1>

              <p class="mb-4 mb-md-5">
              เมื่อทำการจองแล้วจะไม่สามารถยกเลิกหรือแก้ไขได้ ให้ทำการแจ้งเจ้าหน้าที่ในวันที่มารับตามที่วิทยาลัยประกาศ
              </p>

              <div class="hero-buttons">
                <a href="#Educationclass" class="btn btn-primary me-0 me-sm-2 mx-1">จองอุปกรณ์การเรียน</a>
              </div>
            </div>
          </div>

          <div class="col-lg-6">
            <div class="hero-image" data-aos="zoom-out" data-aos-delay="300">
              <img src="assets/img/Advice.png" alt="Hero Image" class="img-fluid">

              <!-- <div class="customers-badge">
                <p class="mb-0 mt-2"></p>
              </div> -->
            </div>
          </div>
        </div>

        <div class="row stats-row gy-4 mt-5" data-aos="fade-up" data-aos-delay="500">
          <div class="col-lg-4 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-cart-check"></i>
              </div>
              <div class="stat-content">
                <h4>ระบบจองอัตโนมัติ</h4>
                <p class="mb-0">ใช้งานง่าย จองได้ทุกเวลา</p>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-clock"></i>
              </div>
              <div class="stat-content">
                <h4>พร้อมจองตลอด 24 ชั่วโมง</h4>
                <p class="mb-0">ตอบโจทย์ทุกความต้องการ ทุกวัน</p>
              </div>
            </div>
          </div>
          <div class="col-lg-4 col-md-6">
            <div class="stat-item">
              <div class="stat-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <div class="stat-content">
                <h4>จองได้ในไม่กี่ขั้นตอน</h4>
                <p class="mb-0">สะดวก ปลอดภัย ทุกแพลตฟอร์ม</p>
              </div>
            </div>
          </div>
        </div>


    </section><!-- /Hero Section -->

    <!-- Features Section -->
    <section id="Educationclass" class="features section">

      <!-- Section Title -->
      <div class="container section-title" data-aos="fade-up">
        <h2>จองอุปกรณ์การเรียน</h2>
        <p>เลือกระดับชั้นการศึกษา</p>
      </div><!-- End Section Title -->

      <div class="container">
  <div class="d-flex justify-content-center">
    <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">
      <li class="nav-item">
        <a class="nav-link active show" data-bs-toggle="tab" data-bs-target="#features-tab-1">
          <h4>ปวช.</h4>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link" data-bs-toggle="tab" data-bs-target="#features-tab-2">
          <h4>ปวส.</h4>
        </a>
      </li>
    </ul>
  </div>
  <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
    <!-- ฟอร์มสำหรับ ปวช. -->
    <div class="tab-pane fade active show" id="features-tab-1">
      <form action="insert_student.php" method="post" id="form_voc">
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
          <?php include 'form_input_voc.php'; ?>
        </div>
      </form>
    </div>
    <!-- ฟอร์มสำหรับ ปวส. -->
    <div class="tab-pane fade" id="features-tab-2">
      <form action="insert_student.php" method="post" id="form_high_voc">
        <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
          <?php include 'form_input_high_voc.php'; ?>
        </div>
      </form>
    </div>
  </div>
</div>


    </section><!-- /Features Section -->

    

    


  </main>

  <?php include 'footer.php'?>

</html>