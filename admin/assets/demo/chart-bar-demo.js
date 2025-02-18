var ctx = document.getElementById('myBarChart').getContext('2d');
var myBarChart;

// สร้างกราฟแยกตามเพศ
function showGenderChart() {
    if (myBarChart) myBarChart.destroy(); // ลบกราฟเดิม

    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productNamesMale.concat(productNamesFemale), // รวมชื่อสินค้าจากชายและหญิง
            datasets: [{
                label: 'จำนวนที่ถูกจอง (ชาย)',
                data: totalQuantitiesMale, // จำนวนที่ถูกจองจากเพศชาย
                backgroundColor: 'rgba(54, 162, 235, 0.2)', // สีฟ้าสำหรับชาย
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'จำนวนที่ถูกจอง (หญิง)',
                data: totalQuantitiesFemale, // จำนวนที่ถูกจองจากเพศหญิง
                backgroundColor: 'rgba(255, 99, 132, 0.2)', // สีชมพูสำหรับหญิง
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }, {
                label: 'จำนวนที่ถูกจอง (อื่นๆ)',
                data: totalQuantitiesOther, // จำนวนที่ถูกจองจากเพศอื่นๆ
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // สีเขียวสำหรับอื่นๆ
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}


function showNoGenderChart() {
    console.log("Showing no gender chart...");
    if (myBarChart) myBarChart.destroy(); // ลบกราฟเดิม

    myBarChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: productNamesNoGender, // ใช้ชื่อสินค้าที่ส่งมาจาก PHP
            datasets: [{
                label: 'จำนวนที่ถูกจอง',
                data: totalQuantitiesNoGender, // ใช้จำนวนสินค้าที่ถูกจองที่ส่งมาจาก PHP
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    min: 0, // บังคับให้เริ่มที่ศูนย์
                    ticks: {
                        stepSize: 1 // บังคับให้เพิ่มทีละ 1
                    }
                }
            }
        }
    });
}

// เริ่มต้นแสดงกราฟที่ไม่แยกเพศ
showNoGenderChart();
