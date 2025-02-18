//Pie Chart วิธีการชำระเงิน
// ตรวจสอบว่ามี element ที่ต้องการหรือไม่ก่อนเริ่มต้นการวาด Chart
document.addEventListener("DOMContentLoaded", function () {
  var paymentChartCanvas = document.getElementById("paymentPieChart");
  if (paymentChartCanvas) {
    var ctx = paymentChartCanvas.getContext('2d');

    // ค่าจำนวนการชำระเงินจากตัวแปรที่ PHP ส่งมา
    var cashCount = parseInt(paymentChartCanvas.getAttribute("data-cash")) || 0;
    var transferCount = parseInt(paymentChartCanvas.getAttribute("data-transfer")) || 0;

    var paymentPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ["เงินสด", "เงินโอน"],
        datasets: [{
          data: [cashCount, transferCount],
          backgroundColor: ['#28a745', '#007bff'],
        }],
      },
    });
  }
});

//Pie Chart สถานะการชำระเงิน
document.addEventListener("DOMContentLoaded", function () {
  var statusChartCanvas = document.getElementById("statusPieChart");
  if (statusChartCanvas) {
    var ctx = statusChartCanvas.getContext('2d');

    // ค่าจำนวนแต่ละสถานะจาก data-* attributes
    var pendingCount = parseInt(statusChartCanvas.getAttribute("data-pending")) || 0;
    var completedCount = parseInt(statusChartCanvas.getAttribute("data-completed")) || 0;
    var failedCount = parseInt(statusChartCanvas.getAttribute("data-failed")) || 0;

    var statusPieChart = new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ["รอตรวจสอบ", "ชำระเงินเสร็จสมบูรณ์", "ไม่ผ่าน"],
        datasets: [{
          data: [pendingCount, completedCount, failedCount],
          backgroundColor: ['#ffc107', '#28a745', '#dc3545'],
        }],
      },
    });
  }
});

// รับค่าจำนวน ปวช. และ ปวส. จาก PHP
// const vocationalStudents = <?php echo $vocational_students; ?>;
// const technicalStudents = <?php echo $technical_students; ?>;

// สร้าง Pie Chart ปวช. และ ปวส.
var ctx2 = document.getElementById("educationPieChart").getContext('2d');
var educationPieChart = new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: ["ปวช.", "ปวส."], // Labels ของ Pie Chart
        datasets: [{
            data: [vocationalStudents, technicalStudents], // ใช้ค่าจาก PHP
            backgroundColor: ['#28a745', '#ffc107'], // สีของแต่ละหมวดหมู่
        }],
    },
});


// Pie Chart Example
var ctx = document.getElementById("myPieChart").getContext('2d');
var myPieChart = new Chart(ctx, {
  type: 'pie',
  data: {
    labels: ["ชาย", "หญิง"], // Labels แสดงกลุ่มชายและหญิง
    datasets: [{
      data: [maleStudents, femaleStudents], // ใช้ค่าจาก PHP
      backgroundColor: ['#007bff', '#dc3545'], // สีสำหรับแต่ละกลุ่ม
    }],
  },
});
