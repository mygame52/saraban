<!DOCTYPE html>
<html>

<head>
    <title>เอกสารลงรับ งานเนื่อง</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <!-- เพิ่ม CSS เพื่อปรับแต่งสไตล์ของปุ่ม -->
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
    </style>
</head>

<body>
    <div class="container mt-5">
        <h4 class="mb-4">เอกสารลงรับ งานต่อเนื่อง</h4>
        <table id="mainTable" class="table table-striped">
            <thead>
                <tr>
                    <th>id</th>
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

                $sql = "SELECT * FROM form_submissions ORDER by id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['district']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['budget']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['receiver']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['continuously_data']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['reference_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['continuously_status']) . "</td>";
                        echo "<td>";
                        if (empty($row['continuously_status'])) {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        } elseif ($row['continuously_status'] === 'รับแล้ว') {
                            echo "<button class='editBtn btn btn-warning'><i class='bi bi-pencil'></i> แก้ไข</button>";
                        } elseif ($row['continuously_status'] === 'แก้ไขงานต่อเนื่อง') {
                            echo "<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>";
                        }
                        echo " <button class='deleteBtn btn btn-danger'><i class='bi bi-trash'></i> ลบ</button>";
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
            const table = $('#mainTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานต่อเนื่อง',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }]
            });

            $('#mainTable').on('click', '.receiveBtn', function() {
                const row = $(this).closest('tr');
                const id = row.find('td:nth-child(9)').text();
                const status = row.find('td:nth-child(10)').text();

                if (status === 'รับแล้ว') {
                    alert('เอกสารนี้ได้รับแล้ว');
                    return;
                }

                $.ajax({
                    url: 'receive_main.php',
                    type: 'POST',
                    data: {
                        id: id,
                        status: 'รับแล้ว'
                    },
                    success: function(response) {
                        if (response === 'success') {
                            row.find('td:nth-child(10)').text('รับแล้ว');
                            row.find('.receiveBtn').remove();
                            row.find('td:last').append("<button class='editBtn btn btn-warning'><i class='bi bi-pencil'></i> แก้ไข</button>");
                            const currentDate = new Date().toISOString().slice(0, 10);
                            row.find('td:nth-child(8)').text(currentDate);
                        } else {
                            alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                        }
                    }
                });
            });

            $('#mainTable').on('click', '.editBtn', function() {
                const row = $(this).closest('tr');
                const id = row.find('td:nth-child(9)').text();

                $.ajax({
                    url: 'receive_main.php',
                    type: 'POST',
                    data: {
                        id: id,
                        status: 'แก้ไขงานต่อเนื่อง'
                    },
                    success: function(response) {
                        if (response === 'success') {
                            row.find('td:nth-child(10)').text('แก้ไขงานต่อเนื่อง');
                            row.find('.editBtn').remove();
                            row.find('td:last').append("<button class='receiveBtn btn btn-success'><i class='bi bi-receipt'></i> รับเอกสาร</button>");
                            const currentDate = new Date().toISOString().slice(0, 10);
                            row.find('td:nth-child(8)').text(currentDate);
                        } else {
                            alert('มีข้อผิดพลาดในการบันทึกข้อมูล');
                        }
                    }
                });
            });

            $('#mainTable').on('click', '.deleteBtn', function() {
                const row = $(this).closest('tr');
                // const id = row.find('td:nth-child(9)').text();

                console.log('id xxx', id)
                if (confirm('คุณแน่ใจหรือไม่ว่าต้องการลบเอกสารนี้?')) {
                    $.ajax({
                        url: 'receive_main.php',
                        type: 'POST',
                        data: {
                            id: id,
                            action: 'delete'
                        },
                        success: function(response) {
                            console.log('response', response)
                            if (response === 'success') {
                               // table.row(row).remove().draw();
                                alert('ลบเอกสารเรียบร้อยแล้ว');
                            } else {
                                alert('มีข้อผิดพลาดในการลบเอกสาร');
                            }
                        }
                    });
                }
            });
        });
    </script>
</body>

</html>
