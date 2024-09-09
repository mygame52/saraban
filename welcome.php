<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>กิจกรรมศูนย์ฝึกอาชีพชุมชน</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .welcome-container {
            margin-top: 50px;
            /* กำหนดความกว้างเต็มพื้นที่ */
            width: 100%;
        }

        .welcome-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .action-btn {
            font-size: 1.1rem;
            padding: 10px 20px;
        }

        /* เพิ่ม CSS สำหรับให้ div แสดงเต็มพื้นที่ */
        #content {
            width: 100%;
        }
    </style>
</head>

<body>
    <!-- เริ่มต้นเมนูบาร์ -->
    <?php include('nfecrud/menubar_welcome.php'); ?>
    <!-- สิ้นสุดเมนูบาร์ -->

    <!-- แสดงข้อมูลที่มาจากลิงค์ -->
    <div class="container-fluid welcome-container py-1">
        <div id="content">
            <?php
            // ตรวจสอบค่า $page ที่ได้รับจาก URL
            $page = isset($_GET['page']) ? $_GET['page'] : "หน้าแรก";
            switch ($page) {
                case "หน้าแรก":
                    include 'nfecrud/District_view_report.php';
                    break;
                case "เอกสารชุดเบิกที่ส่งแล้ว":
                    include 'nfecrud/District_table.php';
                    break;
                case "รายงานผล":
                    include 'nfecrud/reports.php';
                    break;
                case "ดูรายงาน":
                    include 'nfecrud/view_reports.php';
                    break;
                default:
                    echo "<p>กรุณาเลือกหน้าที่ต้องการแสดง</p>";
                    break;
            }
            ?>
        </div>
    </div>


    <!-- เพิ่ม JavaScript ของ Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <?php include('nfecrud/footer_welcome.php'); ?>
</body>

</html>