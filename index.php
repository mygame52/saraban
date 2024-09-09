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
?>
<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ ทั้งหมด</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles_index.css">
    <!-- เพิ่ม CSS เพื่อปรับแต่งสไตล์ของปุ่ม -->
    <style>
        .blinking-icon {
            color: yellow;
            animation: blinking 1.5s infinite;
        }

        @keyframes blinking {
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
    </style>
</head>

<body>

    <div class="container mt-5">
        <h4 class="mb-4">เอกสารวางเบิกศูนย์ฝึกอาชีพชุมชน งานการศึกษาต่อเนื่อง</h4>

        <table id="formTable" class="table table-striped">
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
                    <th>สถานะ</th> <!-- แก้ไขที่นี่ -->
                </tr>
            </thead>

            <tbody>
                <?php
                // เรียกใช้ไฟล์ connection.php เพื่อเชื่อมต่อกับฐานข้อมูล
                require_once('connection.php');

                // คำสั่ง SQL สำหรับดึงข้อมูลทั้งหมดจากตาราง form_submissions
                $sql = "SELECT * FROM form_submissions ORDER BY id DESC;";

                // ทำการ query ข้อมูล
                $result = $conn->query($sql);

                // ตรวจสอบว่ามีข้อมูลหรือไม่
                if ($result->num_rows > 0) {
                    // วนลูปเพื่อแสดงข้อมูล
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['sender'] . "</td>";
                        $district = $row['district'];
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
                        echo "<td>" . $row['book_type'] . "</td>";
                        echo "<td>" . $row['book_number'] . "</td>";
                        echo "<td>" . $row['title'] . "</td>";
                        echo "<td>" . $row['budget'] . "</td>";
                        echo "<td>" . $row['receiver'] . "</td>";
                        echo "<td>" . $row['receive_date'] . "</td>";
                        echo "<td>" . $row['reference_number'] . "</td>";

                        // เพิ่มเงื่อนไขสำหรับแปลงค่าของฟิลด์ "status" เป็นข้อความ
                        echo "<td>";
                        switch ($row['status']) {
                            case 1:
                                echo "<button class='status-btn continuous'>งานต่อเนื่อง</button>";
                                break;
                            case 2:
                                echo "<button class='status-btn continuous-edit'><i class='bi bi-lightbulb blinking-icon'></i>แก้ไขงานต่อเนื่อง</button>";
                                break;
                            case 3:
                                echo "<button class='status-btn logistics'>งานพัสดุ</button>";
                                break;
                            case 4:
                                echo "<button class='status-btn logistics-edit'><i class='bi bi-lightbulb blinking-icon'></i>แก้ไขงานพัสดุ</button>";
                                break;
                            case 5:
                                echo "<button class='status-btn finance'>งานการเงิน</button>";
                                break;
                            case 6:
                                echo "<button class='status-btn finance-edit'><i class='bi bi-lightbulb blinking-icon'></i>แก้ไขงานการเงิน</button>";
                                break;
                            default:
                                echo "<button class='status-btn'>ไม่ระบุ</button>";
                                break;
                        }
                        echo "</td>";

                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>ไม่พบข้อมูล</td></tr>";
                }

                // ปิดการเชื่อมต่อกับฐานข้อมูล
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <!-- เพิ่ม JavaScript ของ Bootstrap 5 และ DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#formTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานต่อเนื่อง',
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
        });
    </script>

</body>

</html>