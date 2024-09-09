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

// ตรวจสอบว่ามีการส่งข้อมูลฟอร์มหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('connection.php');
    
    foreach ($_POST['budget'] as $province => $budget) {
        $stmt = $conn->prepare("UPDATE budget34_2567 SET budget = ? WHERE province = ?");
        $stmt->bind_param("ds", $budget, $province);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();

    // ตั้งค่าสถานะการแจ้งเตือน
    $_SESSION['notification'] = "บันทึกข้อมูลเรียบร้อยแล้ว";
    // รีเฟรชหน้าเว็บ
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <title>บันทึกงบประมาณ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
            background-color: #e9ecef;
            /* สีพื้นหลังของช่องกรอกข้อมูล */
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

        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            border-radius: .25rem;
            padding: .75rem 1.25rem;
            z-index: 9999;
            display: none;
        }
    </style>
</head>

<body>
    <div class="container mt-3">
        <h3 class="text-center mb-4">บันทึกงบประมาณที่ได้รับจัดสรร</h3>
        <form action="" method="post">
            <div class="row">
                <?php
                require_once('connection.php');

                $sql = "SELECT province, budget FROM budget34_2567";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="col-sm-6">';
                        echo '<div class="mb-3">';
                        echo '<label for="budget_' . $row["province"] . '" class="form-label">' . $row["province"] . '</label>';
                        echo '<input type="number" step="0.01" class="form-control" id="budget_' . $row["province"] . '" name="budget[' . $row["province"] . ']" value="' . $row["budget"] . '" required>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo '<div class="alert alert-warning" role="alert">ไม่พบข้อมูลงบประมาณ</div>';
                }

                $conn->close();
                ?>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary">บันทึก</button>
            </div>
        </form>
    </div>
    <div id="notification" class="notification">
        <?php
        if (isset($_SESSION['notification'])) {
            echo $_SESSION['notification'];
            unset($_SESSION['notification']);
        }
        ?>
    </div>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // แสดงการแจ้งเตือนเมื่อมีการบันทึกข้อมูล
        window.onload = function() {
            var notification = document.getElementById('notification');
            if (notification.innerHTML.trim() !== "") {
                notification.style.display = 'block';
                setTimeout(function() {
                    notification.style.display = 'none';
                }, 3000);
            }
        };
    </script>
</body>

</html>
