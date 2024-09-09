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
<html lang="th">

<head>
    <title>บันทึกรับเอกสารต่อเนื่อง</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
            /* สีพื้นหลัง */
        }

        .container {
            max-width: 600px;
            /* กำหนดความกว้างของฟอร์ม */
            background-color: #ffffff;
            /* สีพื้นหลังของฟอร์ม */
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            color: #495057;
            /* สีข้อความของ label */
        }

        .form-control,
        .form-select {
            border-color: #ced4da;
            /* สีขอบของช่องกรอกข้อมูล */
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #80bdff;
            /* สีขอบเมื่อมีการ focus */
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h3 class="mb-4">บันทึกรับเอกสารต่อเนื่อง</h3>

        <form id="myForm" method="post" action="process_form.php" onsubmit="return validateForm(event)">
            <div class="mb-3">
                <label for="sender" class="form-label">ผู้ส่ง:</label>
                <input type="text" class="form-control" id="sender" name="sender" value="งานต่อเนื่อง" readonly>
            </div>

            <div class="mb-3">
                <label for="district" class="form-label">สกร.ระดับอำเภอ:</label>
                <select class="form-select" id="district" name="district" required>
                    <option value="เลือก">เลือกอำเภอ</option>
                    <option value="1280190000">จุฬาภรณ์</option>
                    <option value="1280040000">ฉวาง</option>
                    <option value="1280150000">ขนอม</option>
                    <option value="1280230000">เฉลิมพระเกียรติ</option>
                    <option value="1280070000">ชะอวด</option>
                    <option value="1280220000">ช้างกลาง</option>
                    <option value="1280060000">เชียรใหญ่</option>
                    <option value="1280180000">ถ้ำพรรณรา</option>
                    <option value="1280080000">ท่าศาลา</option>
                    <option value="1280090000">ทุ่งสง</option>
                    <option value="1280110000">ทุ่งใหญ่</option>
                    <option value="1280210000">นบพิตำ</option>
                    <option value="1280100000">นาบอน</option>
                    <option value="1280170000">บางขัน</option>
                    <option value="1280120000">ปากพนัง</option>
                    <option value="1280020000">พรหมคีรี</option>
                    <option value="1280200001">พระพรหม</option>
                    <option value="1280050000">พิปูน</option>
                    <option value="1280010000">เมืองนครศรีธรรมราช</option>
                    <option value="1280030000">ลานสกา</option>
                    <option value="1280140000">สิชล</option>
                    <option value="1280160000">หัวไทร</option>
                    <option value="1280130000">ร่อนพิบูลย์</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="book_type" class="form-label">ประเภทหนังสือ:</label>
                <select class="form-select" id="book_type" name="book_type" required>
                    <option value="เลือก">เลือกประเภท</option>
                    <option value="กลุ่มสนใจ">กลุ่มสนใจ</option>
                    <option value="ชั้นเรียน 31 ชม.ขึ้นไป">ชั้นเรียน 31 ชม.ขึ้นไป</option>
                    <option value="1 อำเภอ 1 อาชีพ">1 อำเภอ 1 อาชีพ</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="book_number" class="form-label">เลขที่หนังสือ:</label>
                <input type="text" class="form-control" id="book_number" name="book_number" value="ศธ. 07051/" required>
            </div>

            <div class="mb-3">
                <label for="title" class="form-label">ชื่อเรื่อง:</label>
                <select class="form-select" id="title" name="title" onchange="setReceiver()">
                    <option value="">เลือกชื่อเรื่อง</option>
                    <option value="เบิกค่าตอบวิทยากร">เบิกค่าตอบวิทยากร</option>
                    <option value="เบิกค่าวัสดุ">เบิกค่าวัสดุ</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="budget" class="form-label">งบประมาณ:</label>
                <input type="number" class="form-control" id="budget" name="budget">
            </div>

            <div class="mb-3">
                <label for="receiver" class="form-label">ผู้รับ:</label>
                <select class="form-select" id="receiver" name="receiver" required>
                    <option value="">กรุณาเลือก</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="receive_date" class="form-label">วันที่รับ:</label>
                <input type="date" class="form-control" id="receive_date" name="receive_date" readonly>
            </div>

            <div class="mb-3">
                <label for="reference_number" class="form-label">ID:</label>
                <input type="text" class="form-control" id="reference_number" name="reference_number">
            </div>

            <button type="submit" class="btn btn-primary w-100">บันทึก</button>
        </form>
    </div>

    <script>
        function setReceiver() {
            var titleSelect = document.getElementById("title");
            var receiverSelect = document.getElementById("receiver");

            // เช็คค่าที่ถูกเลือกในช่อง "ชื่อเรื่อง"
            var selectedTitle = titleSelect.value;

            // รีเซ็ตค่าในช่อง "ผู้รับ" เป็นว่างทุกครั้งที่มีการเลือกชื่อเรื่องใหม่
            receiverSelect.value = "";

            // ถ้าเลือกเบิกค่าตอบวิทยากร
            if (selectedTitle === "เบิกค่าตอบวิทยากร") {
                receiverSelect.innerHTML = '<option value="งานการเงิน">งานการเงิน</option>';
            }
            // ถ้าเลือกเบิกค่าวัสดุ
            else if (selectedTitle === "เบิกค่าวัสดุ") {
                receiverSelect.innerHTML = '<option value="งานพัสดุ">งานพัสดุ</option>';
            }
            // ถ้าเลือกชื่อเรื่องอื่นๆ
            else {
                // ไม่มีตัวเลือกในช่อง "ผู้รับ"
                receiverSelect.innerHTML = '<option value="">กรุณาเลือก</option>';
            }
        }

        // สร้างวันที่ปัจจุบัน
        var currentDate = new Date();

        // กำหนดรูปแบบวันที่ให้เป็น YYYY-MM-DD
        var formattedDate = currentDate.getFullYear() + '-' + ('0' + (currentDate.getMonth() + 1)).slice(-2) + '-' + ('0' + currentDate.getDate()).slice(-2);

        // เซ็ตค่าวันที่ให้กับ input
        document.getElementById('receive_date').value = formattedDate;

        function validateForm(event) {
            // หยุดการส่งฟอร์มโดยอัตโนมัติ
            event.preventDefault();

            var sender = document.getElementById("sender").value;
            var district = document.getElementById("district").value;
            var bookType = document.getElementById("book_type").value;
            var bookNumber = document.getElementById("book_number").value;
            var title = document.getElementById("title").value;
            var budget = document.getElementById("budget").value;
            var receiver = document.getElementById("receiver").value;
            var receiveDate = document.getElementById("receive_date").value;

            if (sender === "" || district === "เลือก" || bookType === "เลือก" || bookNumber === "" || title === "" || budget === "" || receiver === "" || receiveDate === "") {
                Swal.fire({
                    icon: 'error',
                    title: 'กรุณากรอกข้อมูลให้ครบทุกช่อง',
                });
            } else {
                // บันทึกข้อมูล
                // หลังจากนั้นก็ส่งฟอร์ม
                document.getElementById("myForm").submit();
            }
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</body>

</html>