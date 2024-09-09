<?php
session_start();
include('connection.php'); // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเข้าสู่ระบบ
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ดึงข้อมูล role จากฐานข้อมูล
$query = $conn->prepare("SELECT role FROM users WHERE username = ?");
$query->bind_param("s", $username);
$query->execute();
$query->bind_result($role);
$query->fetch();
$query->close();

$role_names = array(
    1 => 'งานต่อเนื่อง',
    2 => 'งานการเงิน',
    3 => 'งานพัสดุ'
);

$role_name = isset($role_names[$role]) ? $role_names[$role] : 'ผู้ใช้ทั่วไป';
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ระบบจัดการเอกสาร</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.9.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            font-family: 'Prompt', sans-serif;
            background-color: #f0f2f5;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(135deg, #5a189a, #ffffff); /* ไล่สีจากม่วงเข้มไปสีขาว */
            color: white;
            display: flex;
            flex-direction: column;
            padding: 20px;
            position: relative;
            overflow-y: auto;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-text {
            font-size: 1.2rem;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #ffffff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .menu-header {
            font-weight: bold;
            padding: 12px 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            color: #ffffff;
            position: relative;
        }

        .menu-header:hover {
            background-color: rgba(255, 255, 255, 0.3);
            transform: translateX(8px);
            color: #fff3e0;
        }

        .submenu {
            list-style: none;
            padding-left: 20px;
            margin: 5px 0;
            display: none;
        }

        .submenu a {
            color: #ffffff;
            text-decoration: none;
            padding: 8px 15px;
            display: flex;
            align-items: center;
            border-radius: 5px;
            transition: all 0.3s ease;
            margin: 3px 0;
            position: relative;
        }

        .submenu a:hover {
            background-color: rgba(255, 255, 255, 0.2);
            padding-left: 25px;
            color: #ffd54f;
        }

        .submenu a i {
            margin-right: 10px;
            transition: transform 0.3s ease;
        }

        .submenu a:hover i {
            transform: rotate(10deg) scale(1.1);
        }

        /* การตั้งค่าสีไอคอนแต่ละเมนู */
        .bi-house-door { color: #ff9800; } 
        .bi-layout-text-sidebar, .bi-chevron-down, .bi-chevron-up { color: #f39c12; } 
        .bi-file-earmark-text { color: #3498db; } 
        .bi-currency-dollar { color: #2ecc71; } 
        .bi-pencil-square { color: #e74c3c; } 
        .bi-clipboard-data { color: #9b59b6; } 
        .bi-people, .bi-people-fill { color: #f1c40f; } 
        .bi-gear, .bi-person-lines-fill { color: #16a085; } 

        .btn-logout {
            background-color: #ff7675;
            color: #ffffff;
            margin: 20px auto;
            padding: 14px 30px;
            text-align: center;
            border-radius: 30px;
            transition: all 0.3s ease;
            border: none;
            position: relative;
            width: 80%;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-logout:hover {
            background-color: #e55039;
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        }

        .content {
            flex-grow: 1;
            padding: 20px;
            margin-bottom: 40px;
        }

        iframe {
            border: none;
            width: 100%;
            height: calc(100vh - 60px);
        }

        .card-footer {
            text-align: center;
            width: 100%;
            background-color: #f8f9fa;
            padding: 10px;
            border-top: 1px solid #dee2e6;
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin-left: 280px;
        }
    </style>
</head>

<body>
    <div class="sidebar">
        <div class="navbar-text">
            ผู้ใช้: <?php echo htmlspecialchars($role_name); ?>
        </div>
        <!-- เพิ่มหน้าหลักไว้ด้านบน -->
        <a class="menu-header" href="index.php" target="contentFrame">
            <i class="bi bi-house-door"></i> หน้าหลัก
        </a>
        <div class="menu-header" onclick="toggleMenu(this)">
            <i class="bi bi-layout-text-sidebar"></i> งานต่อเนื่อง <i class="bi bi-chevron-down"></i>
        </div>
        <ul class="submenu">
            <li><a href="form.php" target="contentFrame"><i class="bi bi-file-earmark-text"></i>บันทึกเอกสาร</a></li>
            <li><a href="form_budget.php" target="contentFrame"><i class="bi bi-currency-dollar"></i>บันทึกงบประมาณ</a></li>
            <li><a href="viewedit.php" target="contentFrame"><i class="bi bi-pencil-square"></i>บันทึกข้อความ เอกสารที่แก้ไข</a></li>
            <li><a href="budget.php" target="contentFrame"><i class="bi bi-clipboard-data"></i>สรุปการส่งเอกสาร</a></li>
            <li><a href="province_view_report.php" target="contentFrame"><i class="bi bi-clipboard-data"></i>สรุปรายงานผลศูนย์ฝึกฯ</a></li>
        </ul>

        <div class="menu-header" onclick="toggleMenu(this)">
            <i class="bi bi-people"></i> งานอัธยาศัย <i class="bi bi-chevron-down"></i>
        </div>
        <ul class="submenu">
            <li><a href="hospitality.php" target="contentFrame"><i class="bi bi-people-fill"></i>รายละเอียดงานอัธยาศัย</a></li>
        </ul>

        <div class="menu-header" onclick="toggleMenu(this)">
            <i class="bi bi-gear"></i> จัดการ <i class="bi bi-chevron-down"></i>
        </div>
        <ul class="submenu">
            <li><a href="manager.php" target="contentFrame"><i class="bi bi-person-lines-fill"></i>จัดการ USER</a></li>
        </ul>

        <a class="btn-logout" href="logout.php">ออกจากระบบ</a>
    </div>

    <div class="content">
        <iframe name="contentFrame" src="<?php
                                            if ($role == 3) {
                                                echo 'parcel.php';
                                            } elseif ($role == 2) {
                                                echo 'finance.php';
                                            } else {
                                                echo 'index.php';
                                            }
                                            ?>"></iframe>
    </div>

    <div class="card-footer text-muted">
        พัฒนาโดย &copy; นายวิทวัส ธานีรัตน์ กลุ่มส่งเสริมการศึกษา สำนักงานส่งการเรียนรู้ประจำจังหวัดนครศรีธรรมราช
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleMenu(element) {
            var submenu = element.nextElementSibling;
            var icon = element.querySelector('.bi-chevron-down, .bi-chevron-up');

            if (submenu.style.display === 'block') {
                submenu.style.display = 'none';
                icon.classList.remove('bi-chevron-up');
                icon.classList.add('bi-chevron-down');
            } else {
                var allMenus = document.querySelectorAll('.submenu');
                var allIcons = document.querySelectorAll('.bi-chevron-up');
                allMenus.forEach(function (item) {
                    item.style.display = 'none';
                });
                allIcons.forEach(function (item) {
                    item.classList.remove('bi-chevron-up');
                    item.classList.add('bi-chevron-down');
                });

                submenu.style.display = 'block';
                icon.classList.remove('bi-chevron-down');
                icon.classList.add('bi-chevron-up');
            }
        }
    </script>
</body>

</html>
