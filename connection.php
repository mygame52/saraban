<?php
// ข้อมูลสำหรับการเชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost"; // หรือที่อยู่ IP ของ MySQL Server
$username = "root"; // ชื่อผู้ใช้ MySQL
$password = ""; // รหัสผ่าน MySQL
$dbname = "form_data"; // ชื่อฐานข้อมูลที่สร้างไว้

// เชื่อมต่อกับ MySQL
$conn = mysqli_connect($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("การเชื่อมต่อล้มเหลว: " . mysqli_connect_error());
}
?>
