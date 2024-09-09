<?php
// ตรวจสอบสถานะของเซสชันก่อนที่จะเริ่มเซสชันใหม่
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('connection.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username']; // เก็บ username ที่ล็อกอิน

// ฟังก์ชันสำหรับดึงข้อมูลรายงานจากฐานข้อมูลที่ตรงกับ username
function getReports($conn, $username)
{
    $sql = "SELECT id, amphoe_name, subdistrict, quarter, fiscal_year, activity_type, activity_name, course_hours, profession_group, budget, male_registration, female_registration, total_registration, start_date, end_date 
            FROM reports 
            WHERE username = ?"; // เพิ่มเงื่อนไข WHERE เพื่อเลือกข้อมูลที่ตรงกับ username
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // ผูกพารามิเตอร์ username
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result;
    } else {
        return null;
    }
}

$reports = getReports($conn, $username);
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ดูรายงาน</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <style>
        .report-container {
            margin-top: 30px;
        }

        .table {
            font-size: 0.875rem;
            /* ปรับขนาดฟอนต์ให้เล็กลง */
        }

        .table th,
        .table td {
            vertical-align: middle;
            text-align: center;
            padding: 0.5rem;
            /* ลด padding */
        }

        .table th {
            background-color: #3a6073;
            color: #ffffff;
        }

        .btn-view {
            color: #ffffff;
            background-color: #00c6ff;
            border-color: #00c6ff;
            font-size: 0.75rem;
            /* ลดขนาดปุ่ม */
            padding: 0.25rem 0.5rem;
            /* ลด padding ปุ่ม */
        }

        .btn-view:hover {
            background-color: #008cbf;
            border-color: #008cbf;
        }

        .table-responsive {
            margin-top: 20px;
            border-radius: 0.25rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            font-size: 1.5rem;
            color: #3a6073;
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>

<body>
    <div class="container report-container">
        <h2>ตำบลที่รายงานแล้ว</h2>

        <div class="table-responsive">
            <?php if ($reports): ?>
                <table id="reportTable" class="table table-bordered table-hover table-striped">
                    <thead>
                        <tr>
                            <th>ตำบล</th>
                            <th>ไตรมาส</th>
                            <th>ประเภทกิจกรรม</th>
                            <th>ชื่อกิจกรรม</th>
                            <th>ชั่วโมงหลักสูตร</th>
                            <th>กลุ่มอาชีพ</th>
                            <th>งบประมาณ</th>
                            <th>ลงทะเบียน (ชาย)</th>
                            <th>ลงทะเบียน (หญิง)</th>
                            <th>ลงทะเบียนรวม</th>
                            <th>วันที่เริ่ม</th>
                            <th>วันที่สิ้นสุด</th>
                            <th>ดูรายละเอียด</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $reports->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['subdistrict']); ?></td>
                                <td><?php echo htmlspecialchars($row['quarter']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['activity_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['course_hours']); ?></td>
                                <td><?php echo htmlspecialchars($row['profession_group']); ?></td>
                                <td><?php echo htmlspecialchars($row['budget']); ?></td>
                                <td><?php echo htmlspecialchars($row['male_registration']); ?></td>
                                <td><?php echo htmlspecialchars($row['female_registration']); ?></td>
                                <td><?php echo htmlspecialchars($row['total_registration']); ?></td>
                                <td><?php echo htmlspecialchars($row['start_date']); ?></td>
                                <td><?php echo htmlspecialchars($row['end_date']); ?></td>
                                <td>
                                    <button type="button" class="btn btn-view" data-bs-toggle="modal" data-bs-target="#reportDetailModal" data-id="<?php echo $row['id']; ?>">
                                        ดูรายงาน
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center">ไม่พบรายงาน</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal สำหรับแสดงรายละเอียดรายงาน -->
    <div class="modal fade" id="reportDetailModal" tabindex="-1" aria-labelledby="reportDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <!-- <h5 class="modal-title" id="reportDetailModalLabel">รายละเอียดรายงาน</h5> -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- เนื้อหาในโมดอลจะถูกโหลดผ่าน AJAX -->
                </div>
            </div>
        </div>
    </div>

    <!-- เพิ่ม JavaScript ของ Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>


    <script>
        // ใช้ jQuery สำหรับการจัดการ AJAX (ตรวจสอบว่าโหลด jQuery ไว้แล้ว)
        document.addEventListener('DOMContentLoaded', function() {
            var reportDetailModal = document.getElementById('reportDetailModal');
            reportDetailModal.addEventListener('show.bs.modal', function(event) {
                var button = event.relatedTarget; // ปุ่มที่ทำให้เกิดการแสดง modal
                var reportId = button.getAttribute('data-id'); // รับค่า ID ของรายงานจาก data-id

                // ส่ง AJAX request ไปที่ view_report_detail.php เพื่อดึงข้อมูล
                var modalBody = reportDetailModal.querySelector('.modal-body');
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '/saraban/nfecrud/view_report_detail.php?id=' + reportId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        modalBody.innerHTML = xhr.responseText; // แสดงผลข้อมูลในโมดอล
                    }
                };
                xhr.send();
            });
        });
        $(document).ready(function() {
            $('#reportTable').DataTable({
                "paging": true,
                "pageLength": 10,
                "searching": true,
                "ordering": true,
                "info": true,
                "language": {
                    "lengthMenu": "แสดง _MENU_ รายการต่อหน้า",
                    "zeroRecords": "ไม่พบข้อมูล",
                    "info": "หน้าที่ _PAGE_ จาก _PAGES_",
                    "infoEmpty": "ไม่มีข้อมูล",
                    "infoFiltered": "(กรองข้อมูลจาก _MAX_ รายการทั้งหมด)",
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