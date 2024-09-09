<?php
require_once('connection.php');

// รับข้อมูลจากฟอร์ม
$sender = $_POST['sender'];
$district = $_POST['district'];
$book_type = $_POST['book_type'];
$book_number = $_POST['book_number'];
$title = $_POST['title'];
$budget = $_POST['budget'];
$receiver = $_POST['receiver'];
$parcel_data = $_POST['parcel_data'];
$reference_number = $_POST['reference_number'];
$parcel_status = $_POST['parcel_status'];
$document1 = $_POST['document1'];
$document2 = $_POST['document2'];
$document3 = $_POST['document3'];
$document4 = $_POST['document4'];
$document5 = $_POST['document5'];
$user = $_POST['user'];

// คำสั่ง SQL
$sql = "INSERT INTO data_edit (sender, district, book_type, book_number, title, budget, receiver, parcel_data, reference_number, parcel_status, document1, document2, document3, document4, document5, user)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Error preparing the statement: " . $conn->error);
}

// การผูกข้อมูลเข้ากับคำสั่ง SQL
$stmt->bind_param("ssssssssssssssss", $sender, $district, $book_type, $book_number, $title, $budget, $receiver, $parcel_data, $reference_number, $parcel_status, $document1, $document2, $document3, $document4, $document5, $user);

// การรันคำสั่ง SQL และการจัดการข้อผิดพลาด
if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $stmt->error;
}

// ปิดคำสั่งและการเชื่อมต่อ
$stmt->close();
$conn->close();
?>
