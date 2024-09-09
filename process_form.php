<?php
// ข้อมูลสำหรับการเชื่อมต่อฐานข้อมูล MySQL
require_once('connection.php');

// รับค่าจากแบบฟอร์ม
$sender = $_POST['sender'];
$district = $_POST['district'];
$book_type = $_POST['book_type'];
$book_number = $_POST['book_number'];
$title = $_POST['title'];
$budget = $_POST['budget'];
$receiver = $_POST['receiver'];
$receive_date = $_POST['receive_date'];
$reference_number = $_POST['reference_number'];

// ตรวจสอบว่า reference number ซ้ำกันหรือไม่
$check_duplicate_sql = "SELECT COUNT(*) as count FROM form_submissions WHERE reference_number = '$reference_number'";
$check_duplicate_result = $conn->query($check_duplicate_sql);
$check_duplicate_row = $check_duplicate_result->fetch_assoc();

if ($check_duplicate_row['count'] > 0) {
    // ถ้า reference number ซ้ำกัน แสดงข้อความแจ้งเตือนและย้อนกลับไปยังหน้า form.php
    echo "<script>alert('ID ซ้ำกัน กรุณากรอกใหม่');</script>";
    echo "<script>window.location = 'form.php';</script>";
    exit(); // ออกจากสคริปต์
}

// เตรียมคำสั่ง SQL สำหรับ INSERT
$sql = "INSERT INTO form_submissions (sender, district, book_type, book_number, title, budget, receiver, receive_date, reference_number)
        VALUES ('$sender', '$district', '$book_type', '$book_number', '$title', '$budget', '$receiver', '$receive_date', '$reference_number')";

// ทำการ INSERT ข้อมูล
if ($conn->query($sql) === TRUE) {
    // แสดงข้อความบันทึกข้อมูลเรียบร้อยแล้ว
    echo "<script>alert('บันทึกข้อมูลเรียบร้อยแล้ว');</script>";
    // กลับไปยังหน้า form.php
    echo "<script>window.location = 'form.php';</script>";
} else {
    // แสดงข้อผิดพลาดในการบันทึกข้อมูล
    echo "<script>alert('เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error . "');</script>";
    // กลับไปยังหน้า form.php
    echo "<script>window.location = 'form.php';</script>";
}

?>
