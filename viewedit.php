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

if ($role != 1) {
    header("Location: login.php");
    exit;
}

// ดึงข้อมูลทั้งหมดจาก data_edit เรียงตาม edited_at ล่าสุด
$sql = "SELECT id, district, book_type, book_number, title, budget, reference_number, document1, document2, document3, document4, document5, edited_at, user 
        FROM data_edit 
        ORDER BY edited_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บันทึกข้อความ เอกสารที่แก้ไข</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.1.1/css/buttons.bootstrap5.min.css">
    <style>
        .modal-header {
            background-color: #007bff;
            color: white;
        }

        .btn-primary,
        .btn-warning,
        .btn-danger {
            margin-right: 5px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            padding: 0;
        }

        .table-responsive {
            overflow-x: auto;
        }

        @media (min-width: 1200px) {
            .container {
                max-width: 1200px;
            }
        }

        .header-title {
            font-weight: bold;
            font-size: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container my-4">
        <div class="card">
            <div class="card-header">
                <span class="header-title">บันทึกข้อความ เอกสารที่แก้ไข</span>
            </div>
            <div class="card-body">
                <?php if ($result->num_rows > 0) : ?>
                    <div class="table-responsive">
                        <table id="data-table" class="table table-hover table-bordered" style="width:100%">
                            <thead class="table-dark">
                                <tr>
                                    <th>เขต</th>
                                    <th>ประเภทหนังสือ</th>
                                    <th>เลขที่หนังสือ</th>
                                    <th>ชื่อเรื่อง</th>
                                    <th>งบประมาณ</th>
                                    <th>เลขที่ ID</th>
                                    <th>แก้ไข 1</th>
                                    <th>แก้ไข 2</th>
                                    <th>แก้ไข 3</th>
                                    <th>แก้ไข 4</th>
                                    <th>แก้ไข 5</th>
                                    <th>เวลาบันทึก</th>
                                    <th>บันทึกโดย</th>
                                    <th>การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) : ?>
                                    <tr data-id="<?php echo $row['id']; ?>">
                                        <td><?php echo htmlspecialchars($row["district"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["book_type"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["book_number"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["title"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["budget"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["reference_number"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["document1"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["document2"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["document3"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["document4"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["document5"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["edited_at"]); ?></td>
                                        <td><?php echo htmlspecialchars($row["user"]); ?></td>
                                        <td>
                                            <button class="btn btn-primary btn-sm printBtn">พิมพ์</button>
                                            <button class="btn btn-danger btn-sm deleteBtn">ลบ</button>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="alert alert-warning" role="alert">
                        ไม่มีข้อมูล
                    </div>
                <?php endif; ?>
                <?php $conn->close(); ?>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.1.1/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#data-table').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": true,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
                "dom": 'Bfrtip',
                "buttons": [
                    'copy', 'excel', 'print'
                ],
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
        });

        $(document).on('click', '.printBtn', function() {
            const referenceNumber = $(this).closest('tr').find('td:nth-child(6)').text(); // ตรวจสอบว่าคอลัมน์ถูกต้อง

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
                    try {
                        const editData = JSON.parse(response);
                        console.log(editData); // เพิ่มบรรทัดนี้เพื่อตรวจสอบข้อมูลที่ได้รับ

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
                    } catch (error) {
                        console.error("Error processing response data:", error);
                        console.error("Response received:", response);
                    }
                },
                error: function(xhr, status, error) {
                    alert('เกิดข้อผิดพลาดในการดึงข้อมูล: ' + error);
                }
            });
        });

        $(document).on('click', '.deleteBtn', function() {
            const row = $(this).closest('tr');
            const id = row.data('id');

            if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบรายการนี้?')) {
                $.ajax({
                    url: 'updata_viewedit.php',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        if (response === 'success') {
                            row.remove();
                        } else {
                            alert('เกิดข้อผิดพลาดในการลบข้อมูล');
                        }
                    },
                    error: function(xhr, status, error) {
                        alert('เกิดข้อผิดพลาดในการลบข้อมูล: ' + error);
                    }
                });
            }
        });
    </script>


</body>

</html>