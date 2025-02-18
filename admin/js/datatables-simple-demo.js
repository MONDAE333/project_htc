window.addEventListener('DOMContentLoaded', event => {
    // Simple-DataTables
    // https://github.com/fiduswriter/Simple-DataTables/wiki

    const datatablesSimple = document.getElementById('datatablesSimple');
    if (datatablesSimple) {
        new simpleDatatables.DataTable(datatablesSimple, {
            perPage: 10,         // กำหนดจำนวนเริ่มต้นที่แสดง
            perPageSelect: [10, 25, 50, 100] // ตัวเลือกจำนวนแถวที่แสดง
        });
    }
});