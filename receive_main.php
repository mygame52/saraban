<?php
// เชื่อมต่อกับฐานข้อมูล
require_once('connection.php');

// ตรวจสอบว่ามีข้อมูลที่ส่งมาผ่าน POST หรือไม่
if (isset($_POST['id'])) {
    $id = $_POST['id'];  // แปลงเป็นตัวเลขเพื่อป้องกัน SQL Injection
    // $id = intval($_POST['id']);  // แปลงเป็นตัวเลขเพื่อป้องกัน SQL Injection
    
    echo ":" . $id;
    //echo "<script>console.log('Debug Objects: " . $id . "' );</script>";
    // ตรวจสอบว่าสถานะเป็นการลบหรือไม่
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        // สร้างคำสั่ง SQL สำหรับลบข้อมูล
        $sql = "DELETE FROM form_submissions WHERE id = $id";

        // ทำการลบข้อมูล
        if ($conn->query($sql) === TRUE) {
            // ส่งค่ากลับเป็น 'success' ถ้าลบสำเร็จ
            echo 'success';
        } else {
            // ส่งค่ากลับเป็น 'error' ถ้าเกิดข้อผิดพลาดในการลบ
            echo 'error';
        }
    } else if (isset($_POST['status'])) {
        // รับค่าจาก POST
        $status = $_POST['status'];

        // สร้างคำสั่ง SQL สำหรับอัปเดตข้อมูล
        $sql = "UPDATE form_submissions SET continuously_status = '$status', continuously_data = NOW() WHERE id = $id";

        // ทำการอัปเดตข้อมูล
        if ($conn->query($sql) === TRUE) {
            // ส่งค่ากลับเป็น 'success' ถ้าอัปเดตสำเร็จ
            echo 'success';
        } else {
            // ส่งค่ากลับเป็น 'error' ถ้าเกิดข้อผิดพลาดในการอัปเดต
            echo 'error';
        }
    } else {
        // ถ้าไม่มีข้อมูลที่ส่งมาผ่าน POST ให้ส่งค่ากลับเป็น 'error'
        echo 'error';
    }
} else {
    // ถ้าไม่มีข้อมูลที่ส่งมาผ่าน POST ให้ส่งค่ากลับเป็น 'error'
    echo 'error';
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
