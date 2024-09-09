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
// ฟังก์ชันดึงข้อมูลผู้ใช้ทั้งหมดจากฐานข้อมูล
function fetchAllUsers($conn) {
    $sql = "SELECT id, username, password, role FROM users";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        return $result;
    } else {
        return [];
    }
}

// เพิ่มผู้ใช้ใหม่
if (isset($_POST['create'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$password', '$role')";
    $conn->query($sql);
    header("Location: manager.php");
}

// อัปเดตข้อมูลผู้ใช้
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $sql = "UPDATE users SET username='$username', password='$password', role='$role' WHERE id=$id";
    $conn->query($sql);
    header("Location: manager.php");
}

// ลบผู้ใช้
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM users WHERE id=$id";
    $conn->query($sql);
    header("Location: manager.php");
}

// ดึงข้อมูลผู้ใช้
$users = fetchAllUsers($conn);

// ฟังก์ชันสำหรับการแปลง role เป็นข้อความ
function getRoleText($role) {
    switch ($role) {
        case 1:
            return 'admin';
        case 2:
            return 'การเงิน';
        case 3:
            return 'พัสดุ';
        case 4:
            return 'สถานศึกษา';
        default:
            return 'ไม่ทราบ';
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>หน้าจัดการผู้ใช้</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-header {
            background-color: #007bff;
            color: white;
        }
        .btn-primary, .btn-warning, .btn-danger {
            margin-right: 5px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h3 class="mb-4">จัดการ Username</h3>
        <div class="card mb-4">
            <div class="card-header">เพิ่ม Username</div>
            <div class="card-body">
                <form method="POST" action="manager.php">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <input type="text" name="username" class="form-control" placeholder="ชื่อผู้ใช้" required>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="password" class="form-control" placeholder="รหัสผ่าน" required>
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select" required>
                                <option value="" disabled selected>เลือกสิทธิการใช้งาน</option>
                                <option value="1">admin</option>
                                <option value="2">การเงิน</option>
                                <option value="3">พัสดุ</option>
                                <option value="4">สถานศึกษา</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" name="create" class="btn btn-primary w-100">เพิ่มผู้ใช้</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>รหัสผ่าน</th>
                    <th>สิทธิการใช้งาน</th>
                    <th>การจัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($users)) {
                    while($row = $users->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['username'] . "</td>";
                        echo "<td>" . $row['password'] . "</td>";
                        echo "<td>" . getRoleText($row['role']) . "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-warning btn-sm' onclick='editUser(" . $row['id'] . ", \"" . $row['username'] . "\", \"" . $row['password'] . "\", " . $row['role'] . ")'>แก้ไข</button>";
                        echo "<a href='manager.php?delete=" . $row['id'] . "' class='btn btn-danger btn-sm'>ลบ</a>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>ไม่พบข้อมูลผู้ใช้</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal สำหรับแก้ไขข้อมูลผู้ใช้ -->
    <div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel">แก้ไขข้อมูลผู้ใช้</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="manager.php">
                        <input type="hidden" name="id" id="editUserId">
                        <div class="mb-3">
                            <label for="editUsername" class="form-label">ชื่อผู้ใช้</label>
                            <input type="text" class="form-control" id="editUsername" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPassword" class="form-label">รหัสผ่าน</label>
                            <input type="text" class="form-control" id="editPassword" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="editRole" class="form-label">สิทธิการใช้งาน</label>
                            <select class="form-select" id="editRole" name="role" required>
                                <option value="1">admin</option>
                                <option value="2">การเงิน</option>
                                <option value="3">พัสดุ</option>
                                <option value="4">สถานศึกษา</option>
                            </select>
                        </div>
                        <button type="submit" name="update" class="btn btn-primary">บันทึกการเปลี่ยนแปลง</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"></script>
    <script>
        function editUser(id, username, password, role) {
            document.getElementById('editUserId').value = id;
            document.getElementById('editUsername').value = username;
            document.getElementById('editPassword').value = password;
            document.getElementById('editRole').value = role;
            var editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
            editUserModal.show();
        }
    </script>
</body>
</html>
