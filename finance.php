<?php
session_start();
include('connection.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ตรวจสอบ role ของผู้ใช้
$sql = "SELECT role FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role != 2) {
    header("Location: login.php");
    exit;
}

// ฟังก์ชันสำหรับแปลง district code และ username code เป็นชื่อเขต
function getDistrictName($code)
{
    $districts = array(
        '1280010000' => 'สกร.อำเภอเมืองนครศรีธรรมราช',
        '1280020000' => 'สกร.อำเภอพรหมคีรี',
        '1280030000' => 'สกร.อำเภอลานสกา',
        '1280040000' => 'สกร.อำเภอฉวาง',
        '1280050000' => 'สกร.อำเภอพิปูน',
        '1280060000' => 'สกร.อำเภอเชียรใหญ่',
        '1280070000' => 'สกร.อำเภอชะอวด',
        '1280080000' => 'สกร.อำเภอท่าศาลา',
        '1280090000' => 'สกร.อำเภอทุ่งสง',
        '1280100000' => 'สกร.อำเภอนาบอน',
        '1280110000' => 'สกร.อำเภอทุ่งใหญ่',
        '1280120000' => 'สกร.อำเภอปากพนัง',
        '1280130000' => 'สกร.อำเภอร่อนพิบูลย์',
        '1280140000' => 'สกร.อำเภอสิชล',
        '1280150000' => 'สกร.อำเภอขนอม',
        '1280160000' => 'สกร.อำเภอหัวไทร',
        '1280170000' => 'สกร.อำเภอบางขัน',
        '1280180000' => 'สกร.อำเภอถ้ำพรรณรา',
        '1280190000' => 'สกร.อำเภอจุฬาภรณ์',
        '1280200001' => 'สกร.อำเภอพระพรหม',
        '1280210000' => 'สกร.อำเภอนบพิตำ',
        '1280220000' => 'สกร.อำเภอช้างกลาง',
        '1280230000' => 'สกร.อำเภอเฉลิมพระเกียรติ'
    );

    return isset($districts[$code]) ? $districts[$code] : 'ค่าที่ไม่ระบุ';
}
?>
<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ งานการเงิน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.7.2/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .status-btn {
            padding: 6px 12px;
            border-radius: 4px;
            color: #fff;
        }

        .status-btn.continuous {
            background-color: #28a745;
            border-color: #28a745;
        }

        .status-btn.finance {
            background-color: #007bff;
            border-color: #007bff;
        }

        .status-btn.logistics {
            background-color: #ffc107;
            border-color: #ffc107;
        }

        .status-btn.finance-edit {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .status-btn.logistics-edit {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .blinking-icon {
            color: red;
            animation: blink 1s infinite;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h4 class="mb-4">เบิกค่าตอบแทนวิทยากร</h4>
        <table id="financeTable" class="table table-striped">
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
                    <th>Id</th>
                    <th>สถานะ</th>
                    <th>ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('connection.php');
                $sql_finance = "SELECT * FROM form_submissions WHERE receiver = 'งานการเงิน' AND continuously_status = 'รับแล้ว' ORDER BY id DESC";
                $result_finance = $conn->query($sql_finance);
                if ($result_finance->num_rows > 0) {
                    while ($row_finance = $result_finance->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_finance['sender'] . "</td>";
                        $district = $row_finance['district'];
                        switch ($district) {
                            case '1280010000':
                                $district_text = 'สกร.อำเภอเมืองนครศรีธรรมราช';
                                break;
                            case '1280020000':
                                $district_text = 'สกร.อำเภอพรหมคีรี';
                                break;
                            case '1280030000':
                                $district_text = 'สกร.อำเภอลานสกา';
                                break;
                            case '1280040000':
                                $district_text = 'สกร.อำเภอฉวาง';
                                break;
                            case '1280050000':
                                $district_text = 'สกร.อำเภอพิปูน';
                                break;
                            case '1280060000':
                                $district_text = 'สกร.อำเภอเชียรใหญ่';
                                break;
                            case '1280070000':
                                $district_text = 'สกร.อำเภอชะอวด';
                                break;
                            case '1280080000':
                                $district_text = 'สกร.อำเภอท่าศาลา';
                                break;
                            case '1280090000':
                                $district_text = 'สกร.อำเภอทุ่งสง';
                                break;
                            case '1280100000':
                                $district_text = 'สกร.อำเภอนาบอน';
                                break;
                            case '1280110000':
                                $district_text = 'สกร.อำเภอทุ่งใหญ่';
                                break;
                            case '1280120000':
                                $district_text = 'สกร.อำเภอปากพนัง';
                                break;
                            case '1280130000':
                                $district_text = 'สกร.อำเภอร่อนพิบูลย์';
                                break;
                            case '1280140000':
                                $district_text = 'สกร.อำเภอสิชล';
                                break;
                            case '1280150000':
                                $district_text = 'สกร.อำเภอขนอม';
                                break;
                            case '1280160000':
                                $district_text = 'สกร.อำเภอหัวไทร';
                                break;
                            case '1280170000':
                                $district_text = 'สกร.อำเภอบางขัน';
                                break;
                            case '1280180000':
                                $district_text = 'สกร.อำเภอถ้ำพรรณรา';
                                break;
                            case '1280190000':
                                $district_text = 'สกร.อำเภอจุฬาภรณ์';
                                break;
                            case '1280200000':
                                $district_text = 'สกร.จังหวัดนครศรีธรรมราช';
                                break;
                            case '1280200001':
                                $district_text = 'สกร.อำเภอพระพรหม';
                                break;
                            case '1280210000':
                                $district_text = 'สกร.อำเภอนบพิตำ';
                                break;
                            case '1280220000':
                                $district_text = 'สกร.อำเภอช้างกลาง';
                                break;
                            case '1280230000':
                                $district_text = 'สกร.อำเภอเฉลิมพระเกียรติ';
                                break;
                            default:
                                $district_text = 'ค่าที่ไม่ระบุ';
                                break;
                        }
                        echo "<td>" . $district_text . "</td>";
                        echo "<td>" . $row_finance['book_type'] . "</td>";
                        echo "<td>" . $row_finance['book_number'] . "</td>";
                        echo "<td>" . $row_finance['title'] . "</td>";
                        echo "<td>" . $row_finance['budget'] . "</td>";
                        echo "<td>" . $row_finance['receiver'] . "</td>";
                        echo "<td>" . $row_finance['finance_data'] . "</td>";
                        echo "<td>" . $row_finance['reference_number'] . "</td>";
                        echo "<td>";
                        if ($row_finance['finance_status'] === 'แก้ไขงานการเงิน') {
                            echo '<i class="bi bi-lightbulb blinking-icon"></i> ' . $row_finance['finance_status'];
                        } else {
                            echo $row_finance['finance_status'];
                        }
                        echo "</td>";

                        echo "<td>";
                        if (empty($row_finance['finance_status'])) {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        } elseif ($row_finance['finance_status'] === 'รับแล้ว') {
                            echo "<button class='editBtn btn btn-danger'><i class='bi bi-pencil'></i> แก้ไข</button>";
                        } elseif ($row_finance['finance_status'] === 'แก้ไขงานการเงิน') {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        }
                        echo "<button class='printBtn btn btn-secondary'><i class='bi bi-printer'></i> พิมพ์</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>ไม่พบข้อมูล</td></tr>";
                }

                ?>
            </tbody>
        </table>

        <h4 class="mb-4">เบิกค่าวัสดุ</h4>
        <table id="parcelTable" class="table table-striped">
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
                    <th>Id</th>
                    <th>สถานะ</th>
                    <th>ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('connection.php');
                $sql_parcel = "SELECT * FROM form_submissions WHERE parcel_status = 'รับแล้ว' ORDER BY id DESC";
                $result_parcel = $conn->query($sql_parcel);
                if ($result_parcel->num_rows > 0) {
                    while ($row_parcel = $result_parcel->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row_parcel['sender'] . "</td>";
                        $district = $row_parcel['district'];
                        switch ($district) {
                            case '1280010000':
                                $district_text = 'สกร.อำเภอเมืองนครศรีธรรมราช';
                                break;
                            case '1280020000':
                                $district_text = 'สกร.อำเภอพรหมคีรี';
                                break;
                            case '1280030000':
                                $district_text = 'สกร.อำเภอลานสกา';
                                break;
                            case '1280040000':
                                $district_text = 'สกร.อำเภอฉวาง';
                                break;
                            case '1280050000':
                                $district_text = 'สกร.อำเภอพิปูน';
                                break;
                            case '1280060000':
                                $district_text = 'สกร.อำเภอเชียรใหญ่';
                                break;
                            case '1280070000':
                                $district_text = 'สกร.อำเภอชะอวด';
                                break;
                            case '1280080000':
                                $district_text = 'สกร.อำเภอท่าศาลา';
                                break;
                            case '1280090000':
                                $district_text = 'สกร.อำเภอทุ่งสง';
                                break;
                            case '1280100000':
                                $district_text = 'สกร.อำเภอนาบอน';
                                break;
                            case '1280110000':
                                $district_text = 'สกร.อำเภอทุ่งใหญ่';
                                break;
                            case '1280120000':
                                $district_text = 'สกร.อำเภอปากพนัง';
                                break;
                            case '1280130000':
                                $district_text = 'สกร.อำเภอร่อนพิบูลย์';
                                break;
                            case '1280140000':
                                $district_text = 'สกร.อำเภอสิชล';
                                break;
                            case '1280150000':
                                $district_text = 'สกร.อำเภอขนอม';
                                break;
                            case '1280160000':
                                $district_text = 'สกร.อำเภอหัวไทร';
                                break;
                            case '1280170000':
                                $district_text = 'สกร.อำเภอบางขัน';
                                break;
                            case '1280180000':
                                $district_text = 'สกร.อำเภอถ้ำพรรณรา';
                                break;
                            case '1280190000':
                                $district_text = 'สกร.อำเภอจุฬาภรณ์';
                                break;
                            case '1280200000':
                                $district_text = 'สกร.จังหวัดนครศรีธรรมราช';
                                break;
                            case '1280200001':
                                $district_text = 'สกร.อำเภอพระพรหม';
                                break;
                            case '1280210000':
                                $district_text = 'สกร.อำเภอนบพิตำ';
                                break;
                            case '1280220000':
                                $district_text = 'สกร.อำเภอช้างกลาง';
                                break;
                            case '1280230000':
                                $district_text = 'สกร.อำเภอเฉลิมพระเกียรติ';
                                break;
                            default:
                                $district_text = 'ค่าที่ไม่ระบุ';
                                break;
                        }
                        echo "<td>" . $district_text . "</td>";
                        echo "<td>" . $row_parcel['book_type'] . "</td>";
                        echo "<td>" . $row_parcel['book_number'] . "</td>";
                        echo "<td>" . $row_parcel['title'] . "</td>";
                        echo "<td>" . $row_parcel['budget'] . "</td>";
                        echo "<td>" . $row_parcel['receiver'] . "</td>";
                        echo "<td>" . $row_parcel['finance_data'] . "</td>";
                        echo "<td>" . $row_parcel['reference_number'] . "</td>";
                        echo "<td>";
                        if ($row_parcel['finance_status'] === 'แก้ไขงานการเงิน') {
                            echo '<i class="bi bi-lightbulb blinking-icon"></i> ' . $row_parcel['finance_status'];
                        } else {
                            echo $row_parcel['finance_status'];
                        }
                        echo "</td>";

                        echo "<td>";
                        if (empty($row_parcel['finance_status'])) {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        } elseif ($row_parcel['finance_status'] === 'รับแล้ว') {
                            echo "<button class='editBtn btn btn-danger'><i class='bi bi-pencil'></i> แก้ไข</button>";
                        } elseif ($row_parcel['finance_status'] === 'แก้ไขงานการเงิน') {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        }
                        echo "<button class='printBtn btn btn-secondary'><i class='bi bi-printer'></i> พิมพ์</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='11'>ไม่พบข้อมูล</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <?php
    include('modal_fade.php');
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#financeTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานการเงิน',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }],
                language: {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });
            $('#parcelTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานการเงิน',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }],
                language: {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "แสดงหน้า _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)",
                    "search": "ค้นหา:",
                    "paginate": {
                        "first": "หน้าแรก",
                        "last": "หน้าสุดท้าย",
                        "next": "ถัดไป",
                        "previous": "ก่อนหน้า"
                    }
                }
            });

            function sendAjaxRequest(action, referenceNumber, status = null, document = null) {
                const currentDate = new Date().toISOString().slice(0, 10);
                let data = {
                    reference_number: referenceNumber,
                    action: action
                };

                if (status) {
                    data.status = status;
                    data.date = currentDate;
                }

                if (document) {
                    data.document = document;
                }

                $.ajax({
                    url: 'updata_finance.php',
                    type: 'POST',
                    data: data,
                    success: function(response) {
                        if (response === 'success') {
                            alert(action === 'delete' ? 'ลบข้อมูลสำเร็จ' : 'อัปเดตข้อมูลสำเร็จ');
                            if (action === 'delete') {
                                table.row($(`td:contains(${referenceNumber})`).closest('tr')).remove().draw(false);
                            } else {
                                let row = $(`td:contains(${referenceNumber})`).closest('tr');
                                row.find('td:nth-child(10)').text(status);
                                row.find('td:nth-child(8)').text(currentDate);
                            }
                            location.reload(); // บรรทัดนี้เพื่อรีเฟรชหน้าเว็บ
                        } else {
                            alert('เกิดข้อผิดพลาดในการดำเนินการ: ' + response);
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('เกิดข้อผิดพลาดในการส่งคำขอ: ' + error);
                    }
                });
            }

            $(document).on('click', '.receiveBtn', function() {
                const referenceNumber = $(this).closest('tr').find('td:nth-child(9)').text();
                if (confirm('ต้องการรับเอกสาร ID: ' + referenceNumber + ' หรือไม่?')) {
                    sendAjaxRequest('receive', referenceNumber, 'รับแล้ว');
                }
            });

            $(document).on('click', '.editBtn', function() {
                const referenceNumber = $(this).closest('tr').find('td:nth-child(9)').text();
                const row = $(this).closest('tr');
                $('#editSender').val(row.find('td:nth-child(1)').text());
                $('#editDistrict').val(row.find('td:nth-child(2)').text());
                $('#editBookType').val(row.find('td:nth-child(3)').text());
                $('#editBookNumber').val(row.find('td:nth-child(4)').text());
                $('#editTitle').val(row.find('td:nth-child(5)').text());
                $('#editBudget').val(row.find('td:nth-child(6)').text());
                $('#editReceiver').val(row.find('td:nth-child(7)').text());
                $('#editParcelData').val(row.find('td:nth-child(8)').text());
                $('#editReferenceNumber').val(row.find('td:nth-child(9)').text());
                $('#editParcelStatus').val(row.find('td:nth-child(10)').text());
                $('#editDocument1').val('');
                $('#editDocument2').val('');
                $('#editDocument3').val('');
                $('#editDocument4').val('');
                $('#editDocument5').val('');

                $('#editModal').modal('show');

                $('#saveEdit').off('click').on('click', function() {
                    const document1 = $('#editDocument1').val();
                    const document2 = $('#editDocument2').val();
                    const document3 = $('#editDocument3').val();
                    const document4 = $('#editDocument4').val();
                    const document5 = $('#editDocument5').val();
                    const editData = {
                        sender: $('#editSender').val(),
                        district: $('#editDistrict').val(),
                        book_type: $('#editBookType').val(),
                        book_number: $('#editBookNumber').val(),
                        title: $('#editTitle').val(),
                        budget: $('#editBudget').val(),
                        receiver: $('#editReceiver').val(),
                        parcel_data: $('#editParcelData').val(),
                        reference_number: $('#editReferenceNumber').val(),
                        parcel_status: $('#editParcelStatus').val(),
                        document1: document1,
                        document2: document2,
                        document3: document3,
                        document4: document4,
                        document5: document5,
                        user: 'งานการเงิน'
                    };

                    if (confirm('ต้องการบันทึกการแก้ไขเอกสาร ID: ' + referenceNumber + ' หรือไม่?')) {
                        $.ajax({
                            url: 'save_edit.php',
                            type: 'POST',
                            data: editData,
                            success: function(response) {
                                if (response === 'success') {
                                    alert('บันทึกข้อมูลสำเร็จ');
                                    sendAjaxRequest('edit', referenceNumber, 'แก้ไขงานการเงิน');
                                    let row = $(`td:contains(${referenceNumber})`).closest('tr');
                                    row.find('.editBtn')
                                        .removeClass('btn btn-warning editBtn')
                                        .addClass('btn btn-success receiveBtn')
                                        .html('<i class="bi bi-receipt"></i> รับเอกสาร');
                                    $('#editModal').modal('hide');
                                } else {
                                    alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' + response);
                                }
                            },
                            error: function(xhr, status, error) {
                                alert('เกิดข้อผิดพลาดในการส่งคำขอ: ' + error);
                            }
                        });
                    }
                });
            });

            $(document).on('click', '.deleteBtn', function() {
                const referenceNumber = $(this).closest('tr').find('td:nth-child(9)').text();
                if (confirm('ต้องการลบเอกสาร ID: ' + referenceNumber + ' หรือไม่?')) {
                    sendAjaxRequest('delete', referenceNumber);
                }
            });
            $(document).on('click', '.printBtn', function() {
                const referenceNumber = $(this).closest('tr').find('td:nth-child(9)').text();

                // ฟังก์ชันเพื่อแปลงวันที่เป็นวันที่ไทย
                function getThaiDate() {
                    const months = [
                        "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                        "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                    ];
                    const now = new Date();
                    const day = now.getDate();
                    const month = months[now.getMonth()];
                    const year = now.getFullYear() + 543; // แปลงเป็นปีพุทธศักราช
                    return `${day} ${month} ${year}`;
                }

                // ฟังก์ชันเพื่อแปลงจำนวนเงินเป็นคำอ่าน
                function convertNumberToThaiText(num) {
                    const thaiNumbers = ["ศูนย์", "หนึ่ง", "สอง", "สาม", "สี่", "ห้า", "หก", "เจ็ด", "แปด", "เก้า"];
                    const thaiUnits = ["", "สิบ", "ร้อย", "พัน", "หมื่น", "แสน", "ล้าน"];

                    let bahtText = "";
                    let numberStr = num.toString();
                    let isTeen = false;

                    for (let i = 0; i < numberStr.length; i++) {
                        let digit = parseInt(numberStr[i]);
                        let position = numberStr.length - i - 1;

                        if (position > 0 && position % 6 === 0 && digit !== 0) {
                            bahtText += "ล้าน";
                        }

                        if (digit !== 0) {
                            if (position === 1 && digit === 1 && !isTeen) {
                                bahtText += "สิบ";
                            } else if (position === 1 && digit === 2) {
                                bahtText += "ยี่สิบ";
                            } else if (position === 1 && digit === 1 && isTeen) {
                                bahtText += "เอ็ด";
                            } else {
                                bahtText += thaiNumbers[digit] + thaiUnits[position % 6];
                            }
                        }

                        if (position === 1 && digit !== 0) {
                            isTeen = true;
                        } else {
                            isTeen = false;
                        }
                    }

                    return bahtText + "บาทถ้วน";
                }

                const currentDate = getThaiDate();
                $.ajax({
                    url: 'get_edit_data.php', // URL ของไฟล์ PHP ที่ใช้ดึงข้อมูลจาก form data_edit
                    type: 'POST',
                    data: {
                        reference_number: referenceNumber
                    },
                    success: function(response) {
                        const editData = JSON.parse(response);

                        // การตรวจสอบและกำหนดค่าของกลุ่มตาม editData.user
                        let additionalGroup = "";
                        if (editData.user == 'งานพัสดุ' || editData.user == 'งานการเงิน') {
                            additionalGroup = " กลุ่มอำนวยการ";
                        } else if (editData.user == 'งานการศึกษาต่อเนื่อง') {
                            additionalGroup = " กลุ่มส่งเสริมการศึกษา";
                        }

                        // การตรวจสอบและกำหนดชื่อผู้รับผิดชอบตาม editData.user
                        let userDisplayName = "";
                        if (editData.user == 'งานพัสดุ') {
                            userDisplayName = "(นางสาวสีตลา จิตบรรเจิด)";
                        } else if (editData.user == 'งานการเงิน') {
                            userDisplayName = "(นางสาวนฤมล คำจันทร์)";
                        } else if (editData.user == 'งานการศึกษาต่อเนื่อง') {
                            userDisplayName = "(นายวิทวัส ธานีรัตน์)";
                        }

                        // การตรวจสอบและกำหนดตำแหน่งผู้รับผิดชอบตาม editData.user
                        let userPosition = "";
                        if (editData.user == 'งานพัสดุ') {
                            userPosition = "นักวิชาการพัสดุ";
                        } else if (editData.user == 'งานการเงิน') {
                            userPosition = "นักวิชาการเงินและบัญชี";
                        } else if (editData.user == 'งานการศึกษาต่อเนื่อง') {
                            userPosition = "นักวิชาการศึกษา";
                        }

                        // แปลงจำนวนเงินเป็นคำอ่าน
                        const budget = parseFloat(editData.budget).toLocaleString('th-TH');
                        const budgetText = convertNumberToThaiText(parseFloat(editData.budget));

                        // การสร้างเนื้อหาเอกสารเพื่อพิมพ์
                        let documentContent = `
            <div style="margin-top: 1cm; margin-bottom: 2cm; margin-left: 2.5cm; margin-right: 2cm; font-size: 16pt; font-family: 'TH Sarabun PSK', sans-serif;">
                <div style="text-align: center; margin-bottom: 35pt; position: relative;">
                    <img src="logo/logo1.png" style="width: 1.5cm; height: 1.5cm; position: absolute; top: -0.5cm; left: 0;">
                    <h3 style="font-size: 29pt; font-weight: bold;">บันทึกข้อความ</h3>
                </div>
                <p style="margin-top: -1cm; font-size: 20pt; line-height: 8pt;"><strong>ส่วนราชการ:</strong> <span style="font-size: 16pt;">${editData.user} ${additionalGroup}</span></p>
                <p style="text-align: center; transform: translateX(2cm); margin-bottom: 4pt;"><span style="font-size: 20pt; font-weight: bold;">วันที่:</span> <span style="font-size: 16pt;">${currentDate}</span></p>
                <p style="margin-bottom: 6pt;"><span style="font-size: 20pt; font-weight: bold;">เรื่อง:</span> ส่งคืนเอกสาร ขอ${editData.title} โครงการศูนย์ฝึกอาชีพชุมชน</p>
                <p style="margin-bottom: 6pt;"><span style="font-size: 20pt; font-weight: bold;">เรียน:</span> ผู้อำนวยการศูนย์ส่งเสริมการเรียนรู้ระดับ${editData.district.replace('สกร.', '')}</p>
                <p style="text-indent: 2.5cm; text-align: justify;">ตามที่ ศูนย์ส่งเสริมการเรียนรู้ระดับ${editData.district.replace('สกร.', '')} ได้ส่งเอกสารขอ${editData.title} โครงการศูนย์ฝึกอาชีพชุมชน ประเภท ${editData.book_type} จากเงินงบประมาณปี พ.ศ. 2567 ไตรมาส 3 - 4 เอกสารชุดเบิก ID ${editData.reference_number} จำนวนเงิน ${budget} บาท (${budgetText}) พบว่ารายละเอียดในเอกสารขอ${editData.title} ไม่ถูกต้องตามเกณฑ์การเบิกเงินงบประมาณโครงการศูนย์ฝึกอาชีพชุมชน ดังนี้</p>
            `;

                        if (editData.document1) {
                            documentContent += `<p style="text-indent: 2.5cm; line-height: 1.2; margin-bottom: 2pt;">1. ${editData.document1}</p>`;
                        }
                        if (editData.document2) {
                            documentContent += `<p style="text-indent: 2.5cm; line-height: 1.2; margin-bottom: 2pt;">2. ${editData.document2}</p>`;
                        }
                        if (editData.document3) {
                            documentContent += `<p style="text-indent: 2.5cm; line-height: 1.2; margin-bottom: 2pt;">3. ${editData.document3}</p>`;
                        }
                        if (editData.document4) {
                            documentContent += `<p style="text-indent: 2.5cm; line-height: 1.2; margin-bottom: 2pt;">4. ${editData.document4}</p>`;
                        }
                        if (editData.document5) {
                            documentContent += `<p style="text-indent: 2.5cm; line-height: 1.2; margin-bottom: 2pt;">5. ${editData.document5}</p>`;
                        }

                        documentContent += `
                <p style="text-indent: 2.5cm; margin-top: 6pt; margin-bottom: 50pt; justify;">จึงขอให้สถานศึกษาดำเนินการแก้ไข เอกสารขอ${editData.title} ID ${editData.reference_number} ตามที่แจ้งให้ดำเนินการแก้ไขให้ถูกต้องตามเกณฑ์การเบิกเงินงบประมาณโครงการศูนย์ฝึกอาชีพชุมชน ต่อไป</p>
                <p style="text-align: center; transform: translateX(2cm); margin-bottom: 2pt;">${userDisplayName}</p>
                <p style="text-align: center; transform: translateX(2cm); margin-top: 2pt;">${userPosition}</p>
            </div>
            `;

                        const printWindow = window.open('', '_blank');
                        printWindow.document.write('<html><head><title>พิมพ์เอกสาร</title>');
                        printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">');
                        printWindow.document.write('<style>@font-face { font-family: "TH Sarabun PSK"; src: url("font/THSarabun.ttf") format("truetype"); }</style>');
                        printWindow.document.write('</head><body>');
                        printWindow.document.write(documentContent);
                        printWindow.document.write('</body></html>');
                        printWindow.document.close();
                        printWindow.print();
                    },
                    error: function(xhr, status, error) {
                        alert('เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error);
                    }
                });
            });
        });
    </script>
</body>

</html>