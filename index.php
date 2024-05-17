<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ ทั้งหมด</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <!-- เพิ่ม CSS เพื่อปรับแต่งสไตล์ของปุ่ม -->
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
            background-color: #17a2b8;
            border-color: #17a2b8;
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
                        echo "<td>" . $row['district'] . "</td>";
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
                                echo "<button class='status-btn finance'>งานการเงิน</button>";
                                break;
                            case 3:
                                echo "<button class='status-btn logistics'>งานพัสดุ</button>";
                                break;
                            case 4:
                                echo "<button class='status-btn finance-edit'>แก้ไขงานการเงิน</button>";
                                break;
                            case 5:
                                echo "<button class='status-btn logistics-edit'>แก้ไขงานพัสดุ</button>";
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
            $('#formTable').DataTable({
                dom: 'lBfrtip',
                buttons: [
                    'print'
                ]
            });
        });
    </script>
    <div class="text-center mt-5">
        <p>กลุ่มส่งเสริมการเรียนรู้ งานการศึกษาต่อเนื่อง </p>
        <p>สำนักงานส่งการเรียนรู้ประจำจังหวัดนครศรีธรรมราช</p>
    </div>
</body>

</html>
