<?php
session_start();
include('connection.php'); // เชื่อมต่อฐานข้อมูล

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ใช้ prepared statements เพื่อป้องกัน SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['username'] = $user['username'];
        $role = $user['role'];

        // ตรวจสอบ role และกำหนด Location
        switch ($role) {
            case 1:
            case 2:
            case 3:
                header("Location: main_menubar.php");
                break;
            case 4:
                header("Location: welcome.php");
                break;
            default:
                header("Location: login.php?error=บทบาทผู้ใช้ไม่ถูกต้อง");
                break;
        }
    } else {
        header("Location: login.php?error=ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง");
    }

    $stmt->close();
}
?>
