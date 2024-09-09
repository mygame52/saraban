<?php
// ตรวจสอบสถานะของเซสชันก่อนที่จะเริ่มเซสชันใหม่
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('connection.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// ดึงข้อมูล amphoe_name จากตาราง tambon ที่มี code ตรงกับ username
$query = "SELECT amphoe_name FROM tambon WHERE code = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$amphoe_name = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $amphoe_name = $row['amphoe_name'];
} else {
    $amphoe_name = "ไม่พบข้อมูลอำเภอ";
}

?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- เพิ่ม Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Prompt', sans-serif;
        }

        .navbar {
            background: linear-gradient(90deg, #3a6073 0%, #16222a 100%);
            color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            padding: 0.5rem 1rem;
            min-height: 70px;
            /* ตั้งค่า min-height เพื่อให้ navbar มีขนาดคงที่ */
        }

        .navbar-brand {
            font-size: 1.3rem;
            /* เพิ่มขนาดฟอนต์เล็กน้อยให้โดดเด่น */
            font-weight: bold;
            color: #ffffff;
            transition: color 0.3s ease, text-shadow 0.3s ease;
        }

        .navbar-brand:hover {
            color: #00c6ff;
            text-shadow: 0px 0px 10px rgba(0, 198, 255, 0.7);
        }

        .nav-link {
            font-size: 1rem;
            /* ทำให้ฟอนต์มีขนาดเท่ากันทุกลิงก์ */
            color: #ffffff;
            margin-right: 1rem;
            /* ปรับระยะห่างให้สม่ำเสมอ */
            position: relative;
            transition: color 0.3s ease, background-color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            display: block;
            margin-top: 3px;
            right: 0;
            background: #00c6ff;
            transition: width 0.3s ease, background-color 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
            left: 0;
            background: #00c6ff;
        }

        .nav-link:hover {
            color: #00c6ff;
            background-color: rgba(0, 198, 255, 0.2);
            border-radius: 5px;
        }

        .navbar-text {
            font-size: 1rem;
            /* คงขนาดฟอนต์ให้เท่ากัน */
            color: #ffffff;
            font-weight: 500;
        }

        .btn-outline-info {
            border-color: #00c6ff;
            color: #ffffff !important;
            font-size: 1rem;
            /* ทำให้ฟอนต์มีขนาดเท่ากับลิงก์ */
            padding: 0.4rem 1rem;
            /* ปรับขนาดปุ่มให้มีความสมดุล */
            border-radius: 25px;
            transition: background-color 0.3s ease, color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-outline-info:hover {
            background-color: #00c6ff;
            color: #ffffff !important;
            transform: scale(1.05);
            box-shadow: 0px 0px 10px rgba(0, 198, 255, 0.5);
        }

        .navbar-toggler {
            border-color: #00c6ff;
        }

        .navbar-toggler-icon {
            color: #00c6ff;
        }
    </style>


</head>

<body>
    <nav class="navbar navbar-expand-lg shadow-sm py-2">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-light d-flex align-items-center" href="#">
                <i class="bi bi-file-earmark-text me-2"></i> กิจกรรมศูนย์ฝึกอาชีพชุมชน
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-light position-relative" href="welcome.php?page=หน้าแรก">
                            <i class="bi bi-house-door"></i> หน้าแรก
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-light position-relative" href="welcome.php?page=เอกสารชุดเบิกที่ส่งแล้ว">
                            <i class="bi bi-folder-check"></i> เอกสารชุดเบิกที่ส่งแล้ว
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-light position-relative" href="welcome.php?page=ดูรายงาน">
                            <i class="bi bi-file-earmark-bar-graph"></i> ดูรายงาน
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-semibold text-light position-relative" href="welcome.php?page=รายงานผล">
                            <i class="bi bi-bar-chart"></i> รายงานผล
                        </a>
                    </li>
                </ul>
                <span class="navbar-text d-flex align-items-center text-light">
                    ยินดีต้อนรับ, <span class="fw-bold text-info ms-1"><?php echo htmlspecialchars($amphoe_name); ?></span>
                    <a href="logout.php" class="btn btn-outline-info ms-4 px-2 py-1 d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i> ออกจากระบบ
                    </a>
                </span>
            </div>
        </div>
    </nav>
</body>


</html>