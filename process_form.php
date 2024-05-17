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
    echo "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error;
}

?>
