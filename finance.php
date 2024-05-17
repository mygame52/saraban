<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ งานการเงิน</title>
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
</head>

<body>

    <h2>เอกสารลงรับ งานการเงิน</h2>

    <!-- ตารางเอกสารงานการเงิน -->
    <h3>เบิกค่าตอบแทนวิทยากร</h3>
    <table id="financeTable" class="display">
        <thead>
            <tr>
                <th>ผู้ส่ง</th>
                <th>สกร.ระดับอำเภอ</th>
                <th>ประเภทหนังสือ</th>
                <th>เลขที่หนังสือ</th>
                <th>ชื่อเรื่อง</th>
                <th>งบประมาณ</th>
                <th>ผู้รับ</th>
                <th>วันที่รับ</th>
                <th>ID</th>
                <th>สถานะ</th>
                <th>ดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <!-- PHP สำหรับดึงข้อมูลเอกสารงานการเงิน -->
            <?php
            require_once('connection.php');
            $sql_finance = "SELECT * FROM form_submissions WHERE receiver = 'งานการเงิน' ORDER by id DESC";
            $result_finance = $conn->query($sql_finance);
            if ($result_finance->num_rows > 0) {
                while ($row_finance = $result_finance->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_finance['sender'] . "</td>";
                    echo "<td>" . $row_finance['district'] . "</td>";
                    echo "<td>" . $row_finance['book_type'] . "</td>";
                    echo "<td>" . $row_finance['book_number'] . "</td>";
                    echo "<td>" . $row_finance['title'] . "</td>";
                    echo "<td>" . $row_finance['budget'] . "</td>";
                    echo "<td>" . $row_finance['receiver'] . "</td>";
                    echo "<td>" . $row_finance['finance_data'] . "</td>";
                    echo "<td>" . $row_finance['reference_number'] . "</td>";
                    echo "<td>" . $row_finance['finance_status'] . "</td>"; // แสดงค่าสถานะ
                    echo "<td>";
                    if ($row_finance['finance_status'] != 'รับแล้ว') {
                        echo "<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>"; // เพิ่มปุ่ม "รับเอกสาร" และกำหนดสีแดง
                    } else {
                        echo "<button class='editBtn' style='color: blue;'>แก้ไข</button>"; // เพิ่มปุ่ม "แก้ไข" เมื่อเอกสารได้รับแล้ว
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>ไม่พบข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <!-- ตารางเอกสารงานพัสดุ -->
    <h3>เบิกค่าวัสดุ</h3>
    <table id="parcelTable" class="display">
        <thead>
            <tr>
                <th>ผู้ส่ง</th>
                <th>สกร.ระดับอำเภอ</th>
                <th>ประเภทหนังสือ</th>
                <th>เลขที่หนังสือ</th>
                <th>ชื่อเรื่อง</th>
                <th>งบประมาณ</th>
                <th>ผู้รับ</th>
                <th>วันที่รับ</th>
                <th>ID</th>
                <th>สถานะ</th>
                <th>ดำเนินการ</th>
            </tr>
        </thead>
        <tbody>
            <!-- PHP สำหรับดึงข้อมูลเอกสารงานพัสดุ -->
            <?php
            $sql_parcel = "SELECT * FROM form_submissions WHERE parcel_status = 'รับแล้ว' ORDER BY id DESC";
            $result_parcel = $conn->query($sql_parcel);
            if ($result_parcel->num_rows > 0) {
                while ($row_parcel = $result_parcel->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row_parcel['sender'] . "</td>";
                    echo "<td>" . $row_parcel['district'] . "</td>";
                    echo "<td>" . $row_parcel['book_type'] . "</td>";
                    echo "<td>" . $row_parcel['book_number'] . "</td>";
                    echo "<td>" . $row_parcel['title'] . "</td>";
                    echo "<td>" . $row_parcel['budget'] . "</td>";
                    echo "<td>" . $row_parcel['receiver'] . "</td>";
                    echo "<td>" . $row_parcel['financial_time'] . "</td>";
                    echo "<td>" . $row_parcel['reference_number'] . "</td>";
                    echo "<td>" . $row_parcel['financial_status'] . "</td>"; // แสดงค่าสถานะ
                    echo "<td>";
                    if ($row_parcel['financial_status'] != 'รับแล้ว') {
                        echo "<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>"; // เพิ่มปุ่ม "รับเอกสาร" และกำหนดสีแดง
                    } else {
                        echo "<button class='editBtn' style='color: blue;'>แก้ไข</button>"; // เพิ่มปุ่ม "แก้ไข" เมื่อเอกสารได้รับแล้ว
                    }
                    echo "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='11'>ไม่พบข้อมูล</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script>
       $(document).ready(function() {
    // DataTable เอกสารงานการเงิน
    $('#financeTable').DataTable({
        dom: 'lBfrtip',
        buttons: [{
            extend: 'print',
            text: 'พิมพ์',
            title: 'เอกสารลงรับ งานการเงิน',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
            }
        }]
    });
    
    // DataTable เอกสารงานพัสดุ
    $('#parcelTable').DataTable({
        dom: 'lBfrtip',
        buttons: [{
            extend: 'print',
            text: 'พิมพ์',
            title: 'เอกสารลงรับ งานพัสดุ',
            exportOptions: {
                columns: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]
            }
        }]
    });

    // เพิ่มการดำเนินการสำหรับปุ่ม "รับเอกสาร" ในตารางงานการเงิน
    $('#financeTable').on('click', '.receiveBtn', function() {
        var row = $(this).closest('tr');
        var rowData = $('#financeTable').DataTable().row(row).data();
        var status = rowData[9]; // ดึงค่าสถานะเอกสาร

        // ตรวจสอบสถานะเอกสาร
        if (status === 'รับแล้ว') {
            alert('เอกสารนี้ได้รับแล้ว');
            return; // ไม่ทำอะไรเพิ่มเติมถ้าเอกสารได้รับแล้ว
        }

        // บันทึกข้อมูลลงฐานข้อมูล
        $.ajax({
            url: 'receive_finance.php',
            type: 'POST',
            data: {
                id: rowData[8], // ส่ง ID เอกสารไปยัง receive_finance.php
                action: 'receive'
            },
            success: function(response) {
                if (response == 'success') {
                    // อัปเดตข้อมูลในตารางโดยการเปลี่ยนค่า financial_status เป็น 'รับแล้ว'
                    $('#financeTable').DataTable().cell(row, 9).data('รับแล้ว').draw();
                    // ลบปุ่ม "รับเอกสาร"
                    row.find('.receiveBtn').remove();
                    row.find('td:last').append("<button class='editBtn' style='color: blue;'>แก้ไข</button>");
                    // อัปเดตวันที่และเวลาปัจจุบันในคอลัมน์ financial_time ของแถวนั้น
                    var currentDate = new Date().toISOString().slice(0, 10); // ใช้เฉพาะวันที่
                    $('#financeTable').DataTable().cell(row, 7).data(currentDate).draw();
                } else {
                    alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                }
            }
        });
    });

    // เพิ่มการดำเนินการสำหรับปุ่ม "แก้ไข"
    $('#financeTable').on('click', '.editBtn', function() {
        var row = $(this).closest('tr');
        var rowData = $('#financeTable').DataTable().row(row).data();

        // บันทึกข้อมูลลงฐานข้อมูล
        $.ajax({
            url: 'receive_finance.php',
            type: 'POST',
            data: {
                id: rowData[8], // ส่ง ID เอกสารไปยัง receive_finance.php
                action: 'edit'
            },
            success: function(response) {
                if (response == 'success') {
                    // อัปเดตข้อมูลในตารางโดยการเปลี่ยนค่า finance_status เป็น 'แก้ไขงานพัสดุ'
                    $('#financeTable').DataTable().cell(row, 9).data('แก้ไขงานการเงิน').draw();
                    // ลบปุ่ม "แก้ไข" และเพิ่มปุ่ม "รับเอกสาร"
                    row.find('.editBtn').remove();
                    row.find('td:last').append("<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>");
                    // อัปเดตวันที่และเวลาปัจจุบันในคอลัมน์ finance_time ของแถวนั้น
                    var currentDate = new Date().toISOString().slice(0, 10); // ใช้เฉพาะวันที่
                    $('#financeTable').DataTable().cell(row, 7).data(currentDate).draw();
                } else {
                    alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                }
            }
        });
    });
    // เพิ่มการดำเนินการสำหรับปุ่ม "รับเอกสาร" ในตารางงานพัสดุ
    $('#parcelTable').on('click', '.receiveBtn', function() {
        var row = $(this).closest('tr');
        var rowData = $('#parcelTable').DataTable().row(row).data();
        var status = rowData[9]; // ดึงค่าสถานะเอกสาร

        // ตรวจสอบสถานะเอกสาร
        if (status === 'รับแล้ว') {
            alert('เอกสารนี้ได้รับแล้ว');
            return; // ไม่ทำอะไรเพิ่มเติมถ้าเอกสารได้รับแล้ว
        }

        // บันทึกข้อมูลลงฐานข้อมูล
        $.ajax({
            url: 'receive_finance.php',
            type: 'POST',
            data: {
                id: rowData[8], // ส่ง ID เอกสารไปยัง receive_finance.php
                action: 'receive'
            },
            success: function(response) {
                if (response == 'success') {
                    // อัปเดตข้อมูลในตารางโดยการเปลี่ยนค่า financial_status เป็น 'รับแล้ว'
                    $('#parcelTable').DataTable().cell(row, 9).data('รับแล้ว').draw();
                    // ลบปุ่ม "รับเอกสาร"
                    row.find('.receiveBtn').remove();
                    row.find('td:last').append("<button class='editBtn' style='color: blue;'>แก้ไข</button>");
                    // อัปเดตวันที่และเวลาปัจจุบันในคอลัมน์ financial_time ของแถวนั้น
                    var currentDate = new Date().toISOString().slice(0, 10); // ใช้เฉพาะวันที่
                    $('#parcelTable').DataTable().cell(row, 7).data(currentDate).draw();
                } else {
                    alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                }
            }
        });
    });

    // เพิ่มการดำเนินการสำหรับปุ่ม "แก้ไข"
    $('#parcelTable').on('click', '.editBtn', function() {
        var row = $(this).closest('tr');
        var rowData = $('#parcelTable').DataTable().row(row).data();

        // บันทึกข้อมูลลงฐานข้อมูล
        $.ajax({
            url: 'receive_finance.php',
            type: 'POST',
            data: {
                id: rowData[8], // ส่ง ID เอกสารไปยัง receive_finance.php
                action: 'edit'
            },
            success: function(response) {
                if (response == 'success') {
                    // อัปเดตข้อมูลในตารางโดยการเปลี่ยนค่า finance_status เป็น 'แก้ไขงานพัสดุ'
                    $('#parcelTable').DataTable().cell(row, 9).data('แก้ไขงานการเงิน').draw();
                    // ลบปุ่ม "แก้ไข" และเพิ่มปุ่ม "รับเอกสาร"
                    row.find('.editBtn').remove();
                    row.find('td:last').append("<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>");
                    // อัปเดตวันที่และเวลาปัจจุบันในคอลัมน์ finance_time ของแถวนั้น
                    var currentDate = new Date().toISOString().slice(0, 10); // ใช้เฉพาะวันที่
                    $('#parcelTable').DataTable().cell(row, 7).data(currentDate).draw();
                } else {
                    alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                }
            }
        });
    });
});

    </script>

</body>

</html>