<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ งานพัสดุ</title>
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
            background-color: #dc3545;
            border-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="container mt-5">

    <h4 class="mb-4">เอกสารลงรับ งานพัสดุ</h4>

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
                    <th>ID</th>
                    <th>สถานะ</th>
                    <th>ดำเนินการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                require_once('connection.php');

                $sql = "SELECT * FROM form_submissions WHERE receiver = 'งานพัสดุ' ORDER by id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['sender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['district']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['budget']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['receiver']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['parcel_data']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['reference_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['parcel_status']) . "</td>";
                        echo "<td>";
                        if ($row['parcel_status'] != 'รับแล้ว') {
                            echo "<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>";
                        } else {
                            echo "<button class='editBtn' style='color: blue;'>แก้ไข</button>";
                        }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script>
        $(document).ready(function() {
            const table = $('#parcelTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานพัสดุ',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }]
            });

            $('#parcelTable').on('click', '.receiveBtn', function() {
                const row = $(this).closest('tr');
                const rowData = table.row(row).data();
                const id = rowData[8];
                const status = rowData[9];

                if (status === 'รับแล้ว') {
                    alert('เอกสารนี้ได้รับแล้ว');
                    return;
                }

                $.ajax({
                    url: 'receive_parcel.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'receive'
                    },
                    success: function(response) {
                        if (response === 'success') {
                            table.cell(row, 9).data('รับแล้ว').draw();
                            row.find('.receiveBtn').remove();
                            row.find('td:last').append("<button class='editBtn' style='color: blue;'>แก้ไข</button>");
                            const currentDate = new Date().toISOString().slice(0, 10);
                            table.cell(row, 7).data(currentDate).draw();
                        } else {
                            alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                        }
                    }
                });
            });

            $('#parcelTable').on('click', '.editBtn', function() {
                const row = $(this).closest('tr');
                const rowData = table.row(row).data();
                const id = rowData[8];

                $.ajax({
                    url: 'receive_parcel.php',
                    type: 'POST',
                    data: {
                        id: id,
                        action: 'edit'
                    },
                    success: function(response) {
                        if (response === 'success') {
                            table.cell(row, 9).data('แก้ไขงานพัสดุ').draw();
                            row.find('.editBtn').remove();
                            row.find('td:last').append("<button class='receiveBtn' style='color: red;'>รับเอกสาร</button>");
                            const currentDate = new Date().toISOString().slice(0, 10);
                            table.cell(row, 7).data(currentDate).draw();
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