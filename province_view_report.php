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

$username = $_SESSION['username']; // เก็บ username ที่ล็อกอิน

// ดึงข้อมูล amphoe_name จากตาราง tambon ที่มี code ตรงกับ username
$query = "SELECT amphoe_name FROM tambon WHERE code = ?";
$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
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

// ปิดการใช้งาน stmt หลังจากที่ใช้งานเสร็จแล้ว
$stmt->close();

include('functions_province_view_report.php');

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานผลการดำเนินงานโครงการศูนย์ฝึกอาชีพชุมชน</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Prompt', sans-serif;
            margin: 0;
            padding: 10px;
            /* ลดขนาด padding */
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
            /* ลดขนาด margin */
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            /* ลดขนาดฟอนต์ */
        }

        .table-container {
            background-color: #ffffff;
            padding: 15px;
            /* ลดขนาด padding */
            border-radius: 8px;
            /* ลดขนาด border-radius */
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            /* ลดขนาด shadow */
        }

        .table {
            width: 100%;
            margin-bottom: 15px;
            /* ลดขนาด margin */
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background-color: #3a6073;
            color: white;
            text-align: center;
            padding: 8px;
            /* ลดขนาด padding */
            font-weight: bold;
            font-size: 12px;
            /* ลดขนาดฟอนต์ */
            transition: background-color 0.3s ease;
        }

        .table td {
            text-align: center;
            vertical-align: middle;
            padding: 8px;
            /* ลดขนาด padding */
            font-size: 11px;
            /* ลดขนาดฟอนต์ */
            transition: background-color 0.3s ease;
        }

        .group-header {
            background-color: #6c757d;
            color: white;
            font-weight: bold;
            font-size: 12px;
            /* ลดขนาดฟอนต์ */
        }

        .highlight {
            background-color: #ffc107;
            color: #212529;
            font-weight: bold;
            font-size: 12px;
            /* ลดขนาดฟอนต์ */
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #dee2e6;
        }

        .group-header td {
            background-color: #495057;
            color: white;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table th,
        .table td {
            border-right: 1px solid #dee2e6;
        }

        .table th:last-child,
        .table td:last-child {
            border-right: none;
        }
    </style>
</head>

<body>
    <div class="container table-container">
        <h2 class="text-center mb-4">แบบรายงานผลการดำเนินงานโครงการศูนย์ฝึกอาชีพชุมชน</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">ลำดับ</th>
                    <th rowspan="2">กลุ่มอาชีพ</th>
                    <th rowspan="2">หลักสูตรที่จัด</th>
                    <th rowspan="2">จำนวนผู้สมัคร (คน)</th>
                    <th rowspan="2">จำนวนผู้ผ่านการฝึก (คน)</th>
                    <th colspan="6">ผู้ผ่านการฝึกอาชีพนำความรู้ไปใช้</th>
                    <th rowspan="2">รวมทั้งสิ้น (คน)</th>
                    <th rowspan="2">ผู้ผ่านการฝึกอาชีพจบแล้วมีงานทำ (คน)</th>
                </tr>
                <tr>
                    <th>ไปสร้างอาชีพ</th>
                    <th>เพิ่มรายได้</th>
                    <th>มีคุณภาพชีวิตที่ดีขึ้น</th>
                    <th>ต่อยอดภูมิปัญญาท้องถิ่น</th>
                    <th>สร้างมูลค่าเพิ่ม</th>
                    <th>พัฒนาสู่วิสหกิจชุมชน</th>
                </tr>
            </thead>
            <tbody>
                <tr class="group-header">
                    <td colspan="13">1. กลุ่มอาชีพเกษตรกรรม</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ตั้งแต่ 1-30 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_agriculture_1_30; ?></td>
                    <td><?php echo $total_registration_agriculture_interest; ?></td>
                    <td><?php echo $total_trainees_agriculture_interest; ?></td>
                    <td><?php echo $total_create_job_agriculture_interest; ?></td>
                    <td><?php echo $total_income_agriculture_interest; ?></td>
                    <td><?php echo $total_quality_life_agriculture_interest; ?></td>
                    <td><?php echo $total_local_wisdom_agriculture_interest; ?></td>
                    <td><?php echo $total_community_enterprise_agriculture_interest; ?></td>
                    <td><?php echo $total_value_addition_agriculture_interest; ?></td>
                    <td><?php echo $total_trainees_knowledge_agriculture_interest; ?></td>
                    <td><?php echo $total_income_agriculture_interest; ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ตั้งแต่ 31 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_agriculture_combined; ?></td>
                    <td><?php echo $total_registration_agriculture_professional; ?></td>
                    <td><?php echo $total_trainees_agriculture_professional; ?></td>
                    <td><?php echo $total_create_job_agriculture_professional; ?></td>
                    <td><?php echo $total_income_agriculture_professional; ?></td>
                    <td><?php echo $total_quality_life_agriculture_professional; ?></td>
                    <td><?php echo $total_local_wisdom_agriculture_professional; ?></td>
                    <td><?php echo $total_community_enterprise_agriculture_professional; ?></td>
                    <td><?php echo $total_value_addition_agriculture_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_agriculture_professional; ?></td>
                    <td><?php echo $total_income_agriculture_professional; ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>100 ชั่วโมงขึ้นไป</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="2">รวม</td>
                    <td><?php echo $count_agriculture_1_30 + $count_agriculture_combined; ?></td>
                    <td><?php echo $total_registration_agriculture_interest + $total_registration_agriculture_professional; ?></td>
                    <td><?php echo $total_trainees_agriculture_interest + $total_trainees_agriculture_professional; ?></td>
                    <td><?php echo $total_create_job_agriculture_interest + $total_create_job_agriculture_professional; ?></td>
                    <td><?php echo $total_income_agriculture_interest + $total_income_agriculture_professional; ?></td>
                    <td><?php echo $total_quality_life_agriculture_interest + $total_quality_life_agriculture_professional; ?></td>
                    <td><?php echo $total_local_wisdom_agriculture_interest + $total_local_wisdom_agriculture_professional; ?></td>
                    <td><?php echo $total_community_enterprise_agriculture_interest + $total_community_enterprise_agriculture_professional; ?></td>
                    <td><?php echo $total_value_addition_agriculture_interest + $total_value_addition_agriculture_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_agriculture_interest + $total_trainees_knowledge_agriculture_professional; ?></td>
                    <td><?php echo $total_income_agriculture_interest + $total_income_agriculture_professional; ?></td>
                </tr>
                <tr class="group-header">
                    <td colspan="13">2. กลุ่มอาชีพอุตสาหกรรมหรือหัตถกรรม</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ตั้งแต่ 1-30 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_industry_1_30; ?></td>
                    <td><?php echo $total_registration_industry_interest; ?></td>
                    <td><?php echo $total_trainees_industry_interest; ?></td>
                    <td><?php echo $total_create_job_industry_interest; ?></td>
                    <td><?php echo $total_income_industry_interest; ?></td>
                    <td><?php echo $total_quality_life_industry_interest; ?></td>
                    <td><?php echo $total_local_wisdom_industry_interest; ?></td>
                    <td><?php echo $total_community_enterprise_industry_interest; ?></td>
                    <td><?php echo $total_value_addition_industry_interest; ?></td>
                    <td><?php echo $total_trainees_knowledge_industry_interest; ?></td>
                    <td><?php echo $total_income_industry_interest; ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ตั้งแต่ 31 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_industry_combined; ?></td>
                    <td><?php echo $total_registration_industry_professional; ?></td>
                    <td><?php echo $total_trainees_industry_professional; ?></td>
                    <td><?php echo $total_create_job_industry_professional; ?></td>
                    <td><?php echo $total_income_industry_professional; ?></td>
                    <td><?php echo $total_quality_life_industry_professional; ?></td>
                    <td><?php echo $total_local_wisdom_industry_professional; ?></td>
                    <td><?php echo $total_community_enterprise_industry_professional; ?></td>
                    <td><?php echo $total_value_addition_industry_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_industry_professional; ?></td>
                    <td><?php echo $total_income_industry_professional; ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>100 ชั่วโมงขึ้นไป</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="2">รวม</td>
                    <td><?php echo $count_industry_1_30 + $count_industry_combined; ?></td>
                    <td><?php echo $total_registration_industry_interest + $total_registration_industry_professional; ?></td>
                    <td><?php echo $total_trainees_industry_interest + $total_trainees_industry_professional; ?></td>
                    <td><?php echo $total_create_job_industry_interest + $total_create_job_industry_professional; ?></td>
                    <td><?php echo $total_income_industry_interest + $total_income_industry_professional; ?></td>
                    <td><?php echo $total_quality_life_industry_interest + $total_quality_life_industry_professional; ?></td>
                    <td><?php echo $total_local_wisdom_industry_interest + $total_local_wisdom_industry_professional; ?></td>
                    <td><?php echo $total_community_enterprise_industry_interest + $total_community_enterprise_industry_professional; ?></td>
                    <td><?php echo $total_value_addition_industry_interest + $total_value_addition_industry_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_industry_interest + $total_trainees_knowledge_industry_professional; ?></td>
                    <td><?php echo $total_income_industry_interest + $total_income_industry_professional; ?></td>
                </tr>
                <!-- Add more rows for other groups and categories as needed -->
                <tr class="group-header">
                    <td colspan="13">3. กลุ่มอาชีพพาณิชยกรรมและบริการ</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ตั้งแต่ 1-30 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_commerce_1_30; ?></td>
                    <td><?php echo $total_registration_commerce_interest; ?></td>
                    <td><?php echo $total_trainees_commerce_interest; ?></td>
                    <td><?php echo $total_create_job_commerce_interest; ?></td>
                    <td><?php echo $total_income_commerce_interest; ?></td>
                    <td><?php echo $total_quality_life_commerce_interest; ?></td>
                    <td><?php echo $total_local_wisdom_commerce_interest; ?></td>
                    <td><?php echo $total_community_enterprise_commerce_interest; ?></td>
                    <td><?php echo $total_value_addition_commerce_interest; ?></td>
                    <td><?php echo $total_trainees_knowledge_commerce_interest; ?></td>
                    <td><?php echo $total_income_commerce_interest; ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ตั้งแต่ 31 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_commerce_combined; ?></td>
                    <td><?php echo $total_registration_commerce_professional; ?></td>
                    <td><?php echo $total_trainees_commerce_professional; ?></td>
                    <td><?php echo $total_create_job_commerce_professional; ?></td>
                    <td><?php echo $total_income_commerce_professional; ?></td>
                    <td><?php echo $total_quality_life_commerce_professional; ?></td>
                    <td><?php echo $total_local_wisdom_commerce_professional; ?></td>
                    <td><?php echo $total_community_enterprise_commerce_professional; ?></td>
                    <td><?php echo $total_value_addition_commerce_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_commerce_professional; ?></td>
                    <td><?php echo $total_income_commerce_professional; ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>100 ชั่วโมงขึ้นไป</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="2">รวม</td>
                    <td><?php echo $count_commerce_1_30 + $count_commerce_combined; ?></td>
                    <td><?php echo $total_registration_commerce_interest + $total_registration_commerce_professional; ?></td>
                    <td><?php echo $total_trainees_commerce_interest + $total_trainees_commerce_professional; ?></td>
                    <td><?php echo $total_create_job_commerce_interest + $total_create_job_commerce_professional; ?></td>
                    <td><?php echo $total_income_commerce_interest + $total_income_commerce_professional; ?></td>
                    <td><?php echo $total_quality_life_commerce_interest + $total_quality_life_commerce_professional; ?></td>
                    <td><?php echo $total_local_wisdom_commerce_interest + $total_local_wisdom_commerce_professional; ?></td>
                    <td><?php echo $total_community_enterprise_commerce_interest + $total_community_enterprise_commerce_professional; ?></td>
                    <td><?php echo $total_value_addition_commerce_interest + $total_value_addition_commerce_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_commerce_interest + $total_trainees_knowledge_commerce_professional; ?></td>
                    <td><?php echo $total_income_commerce_interest + $total_income_commerce_professional; ?></td>
                </tr>
                <!-- Add more rows for other groups and categories as needed -->
                <tr class="group-header">
                    <td colspan="13">4. กลุ่มอาชีพสร้างสรรค์</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ตั้งแต่ 1-30 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_creative_1_30; ?></td>
                    <td><?php echo $total_registration_creative_interest; ?></td>
                    <td><?php echo $total_trainees_creative_interest; ?></td>
                    <td><?php echo $total_create_job_creative_interest; ?></td>
                    <td><?php echo $total_income_creative_interest; ?></td>
                    <td><?php echo $total_quality_life_creative_interest; ?></td>
                    <td><?php echo $total_local_wisdom_creative_interest; ?></td>
                    <td><?php echo $total_community_enterprise_creative_interest; ?></td>
                    <td><?php echo $total_value_addition_creative_interest; ?></td>
                    <td><?php echo $total_trainees_knowledge_creative_interest; ?></td>
                    <td><?php echo $total_income_creative_interest; ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ตั้งแต่ 31 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_creative_combined; ?></td>
                    <td><?php echo $total_registration_creative_professional; ?></td>
                    <td><?php echo $total_trainees_creative_professional; ?></td>
                    <td><?php echo $total_create_job_creative_professional; ?></td>
                    <td><?php echo $total_income_creative_professional; ?></td>
                    <td><?php echo $total_quality_life_creative_professional; ?></td>
                    <td><?php echo $total_local_wisdom_creative_professional; ?></td>
                    <td><?php echo $total_community_enterprise_creative_professional; ?></td>
                    <td><?php echo $total_value_addition_creative_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_creative_professional; ?></td>
                    <td><?php echo $total_income_creative_professional; ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>100 ชั่วโมงขึ้นไป</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="2">รวม</td>
                    <td><?php echo $count_creative_1_30 + $count_creative_combined; ?></td>
                    <td><?php echo $total_registration_creative_interest + $total_registration_creative_professional; ?></td>
                    <td><?php echo $total_trainees_creative_interest + $total_trainees_creative_professional; ?></td>
                    <td><?php echo $total_create_job_creative_interest + $total_create_job_creative_professional; ?></td>
                    <td><?php echo $total_income_creative_interest + $total_income_creative_professional; ?></td>
                    <td><?php echo $total_quality_life_creative_interest + $total_quality_life_creative_professional; ?></td>
                    <td><?php echo $total_local_wisdom_creative_interest + $total_local_wisdom_creative_professional; ?></td>
                    <td><?php echo $total_community_enterprise_creative_interest + $total_community_enterprise_creative_professional; ?></td>
                    <td><?php echo $total_value_addition_creative_interest + $total_value_addition_creative_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_creative_interest + $total_trainees_knowledge_creative_professional; ?></td>
                    <td><?php echo $total_income_creative_interest + $total_income_creative_professional; ?></td>
                </tr>
                <!-- Add more rows for other groups and categories as needed -->
                <tr class="group-header">
                    <td colspan="13">5. กลุ่มอาชีพเฉพาะทาง</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td>ตั้งแต่ 1-30 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_specialized_1_30; ?></td>
                    <td><?php echo $total_registration_specialized_interest; ?></td>
                    <td><?php echo $total_trainees_specialized_interest; ?></td>
                    <td><?php echo $total_create_job_specialized_interest; ?></td>
                    <td><?php echo $total_income_specialized_interest; ?></td>
                    <td><?php echo $total_quality_life_specialized_interest; ?></td>
                    <td><?php echo $total_local_wisdom_specialized_interest; ?></td>
                    <td><?php echo $total_community_enterprise_specialized_interest; ?></td>
                    <td><?php echo $total_value_addition_specialized_interest; ?></td>
                    <td><?php echo $total_trainees_knowledge_specialized_interest; ?></td>
                    <td><?php echo $total_income_specialized_interest; ?></td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>ตั้งแต่ 31 ชั่วโมงขึ้นไป</td>
                    <td><?php echo $count_specialized_combined; ?></td>
                    <td><?php echo $total_registration_specialized_professional; ?></td>
                    <td><?php echo $total_trainees_specialized_professional; ?></td>
                    <td><?php echo $total_create_job_specialized_professional; ?></td>
                    <td><?php echo $total_income_specialized_professional; ?></td>
                    <td><?php echo $total_quality_life_specialized_professional; ?></td>
                    <td><?php echo $total_local_wisdom_specialized_professional; ?></td>
                    <td><?php echo $total_community_enterprise_specialized_professional; ?></td>
                    <td><?php echo $total_value_addition_specialized_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_specialized_professional; ?></td>
                    <td><?php echo $total_income_specialized_professional; ?></td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>100 ชั่วโมงขึ้นไป</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
                <tr>
                    <td colspan="2">รวม</td>
                    <td><?php echo $count_specialized_1_30 + $count_specialized_combined; ?></td>
                    <td><?php echo $total_registration_specialized_interest + $total_registration_specialized_professional; ?></td>
                    <td><?php echo $total_trainees_specialized_interest + $total_trainees_specialized_professional; ?></td>
                    <td><?php echo $total_create_job_specialized_interest + $total_create_job_specialized_professional; ?></td>
                    <td><?php echo $total_income_specialized_interest + $total_income_specialized_professional; ?></td>
                    <td><?php echo $total_quality_life_specialized_interest + $total_quality_life_specialized_professional; ?></td>
                    <td><?php echo $total_local_wisdom_specialized_interest + $total_local_wisdom_specialized_professional; ?></td>
                    <td><?php echo $total_community_enterprise_specialized_interest + $total_community_enterprise_specialized_professional; ?></td>
                    <td><?php echo $total_value_addition_specialized_interest + $total_value_addition_specialized_professional; ?></td>
                    <td><?php echo $total_trainees_knowledge_specialized_interest + $total_trainees_knowledge_specialized_professional; ?></td>
                    <td><?php echo $total_income_specialized_interest + $total_income_specialized_professional; ?></td>
                </tr>
                <!-- Add more rows for other groups and categories as needed -->
                <tr class="group-header highlight">
                    <td colspan="2">รวมทั้ง 5 กลุ่มอาชีพ</td>
                    <td><?php
                        echo ($count_agriculture_1_30 + $count_agriculture_combined)
                            + ($count_industry_1_30 + $count_industry_combined)
                            + ($count_commerce_1_30 + $count_commerce_combined)
                            + ($count_creative_1_30 + $count_creative_combined)
                            + ($count_specialized_1_30 + $count_specialized_combined);
                        ?></td>
                    <td><?php
                        echo ($total_registration_agriculture_interest + $total_registration_agriculture_professional)
                            + ($total_registration_industry_interest + $total_registration_industry_professional)
                            + ($total_registration_commerce_interest + $total_registration_commerce_professional)
                            + ($total_registration_creative_interest + $total_registration_creative_professional)
                            + ($total_registration_specialized_interest + $total_registration_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_trainees_agriculture_interest + $total_trainees_agriculture_professional)
                            + ($total_trainees_industry_interest + $total_trainees_industry_professional)
                            + ($total_trainees_commerce_interest + $total_trainees_commerce_professional)
                            + ($total_trainees_creative_interest + $total_trainees_creative_professional)
                            + ($total_trainees_specialized_interest + $total_trainees_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_create_job_agriculture_interest + $total_create_job_agriculture_professional)
                            + ($total_create_job_industry_interest + $total_create_job_industry_professional)
                            + ($total_create_job_commerce_interest + $total_create_job_commerce_professional)
                            + ($total_create_job_creative_interest + $total_create_job_creative_professional)
                            + ($total_create_job_specialized_interest + $total_create_job_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_income_agriculture_interest + $total_income_agriculture_professional)
                            + ($total_income_industry_interest + $total_income_industry_professional)
                            + ($total_income_commerce_interest + $total_income_commerce_professional)
                            + ($total_income_creative_interest + $total_income_creative_professional)
                            + ($total_income_specialized_interest + $total_income_specialized_professional);
                        ?></td>
                    <td> <?php
                            echo ($total_quality_life_agriculture_interest + $total_quality_life_agriculture_professional)
                                + ($total_quality_life_industry_interest + $total_quality_life_industry_professional)
                                + ($total_quality_life_commerce_interest + $total_quality_life_commerce_professional)
                                + ($total_quality_life_creative_interest + $total_quality_life_creative_professional)
                                + ($total_quality_life_specialized_interest + $total_quality_life_specialized_professional);
                            ?></td>
                    <td><?php
                        echo ($total_local_wisdom_agriculture_interest + $total_local_wisdom_agriculture_professional)
                            + ($total_local_wisdom_industry_interest + $total_local_wisdom_industry_professional)
                            + ($total_local_wisdom_commerce_interest + $total_local_wisdom_commerce_professional)
                            + ($total_local_wisdom_creative_interest + $total_local_wisdom_creative_professional)
                            + ($total_local_wisdom_specialized_interest + $total_local_wisdom_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_community_enterprise_agriculture_interest + $total_community_enterprise_agriculture_professional)
                            + ($total_community_enterprise_industry_interest + $total_community_enterprise_industry_professional)
                            + ($total_community_enterprise_commerce_interest + $total_community_enterprise_commerce_professional)
                            + ($total_community_enterprise_creative_interest + $total_community_enterprise_creative_professional)
                            + ($total_community_enterprise_specialized_interest + $total_community_enterprise_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_value_addition_agriculture_interest + $total_value_addition_agriculture_professional)
                            + ($total_value_addition_industry_interest + $total_value_addition_industry_professional)
                            + ($total_value_addition_commerce_interest + $total_value_addition_commerce_professional)
                            + ($total_value_addition_creative_interest + $total_value_addition_creative_professional)
                            + ($total_value_addition_specialized_interest + $total_value_addition_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_trainees_knowledge_agriculture_interest + $total_trainees_knowledge_agriculture_professional)
                            + ($total_trainees_knowledge_industry_interest + $total_trainees_knowledge_industry_professional)
                            + ($total_trainees_knowledge_commerce_interest + $total_trainees_knowledge_commerce_professional)
                            + ($total_trainees_knowledge_creative_interest + $total_trainees_knowledge_creative_professional)
                            + ($total_trainees_knowledge_specialized_interest + $total_trainees_knowledge_specialized_professional);
                        ?></td>
                    <td><?php
                        echo ($total_income_agriculture_interest + $total_income_agriculture_professional)
                            + ($total_income_industry_interest + $total_income_industry_professional)
                            + ($total_income_commerce_interest + $total_income_commerce_professional)
                            + ($total_income_creative_interest + $total_income_creative_professional)
                            + ($total_income_specialized_interest + $total_income_specialized_professional);
                        ?></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="container table-container">
        <h2 class="text-center mb-4">จำนวนกลุ่มเป้าหมายที่ผ่านการฝึกอาชีพ จบแล้วมีงานทำ (Learn to Earn)</h2>
        <table class="table table-bordered">
            <thead>
                <tr>

                    <th rowspan="2">ผู้ผ่านการฝึกอาชีพจบแล้วมีงานทำ</th>
                    <th colspan="8">รายได้ที่เพิ่มขึ้น หลังจากเข้ารับการฝึกอาชีพของประชาชน โดยเฉลี่ยต่อเดือน</th>
                </tr>
                <tr>
                    <th>ต่ำกว่า 3,000 บาท</th>
                    <th>3,001 - 5,000 บาท</th>
                    <th>5,001 - 10,000 บาท</th>
                    <th>10,001 - 15,000 บาท</th>
                    <th>15,001 - 30,000 บาท</th>
                    <th>30,001 - 50,000 บาท</th>
                    <th>50,001 - 100,000 บาท</th>
                    <th>มากกว่า 100,000 บาท</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo $total_income_sum; ?></td>
                    <td><?php echo number_format($total_income['income_below_3000']); ?></td>
                    <td><?php echo number_format($total_income['income_3001_5000']); ?></td>
                    <td><?php echo number_format($total_income['income_5001_10000']); ?></td>
                    <td><?php echo number_format($total_income['income_10001_15000']); ?></td>
                    <td><?php echo number_format($total_income['income_15001_30000']); ?></td>
                    <td><?php echo number_format($total_income['income_30001_50000']); ?></td>
                    <td><?php echo number_format($total_income['income_50001_100000']); ?></td>
                    <td><?php echo number_format($total_income['income_above_100000']); ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="container table-container">
        <h2 class="text-center mb-4">จำนวนกลุ่มเป้าหมายที่ผ่านการฝึกอาชีพ จำแนกตาม ช่วงอายุและเพศ </h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="3">ที่</th>
                    <th rowspan="3">สกร.อำเภอ/เขต/สถานศึกษา</th>
                    <th colspan="14">จำแนกตามช่วงอายุและเพศ (จำนวนคน)</th>
                    <th rowspan="3">จำนวนผู้ผ่านการฝึกอาชีพ</th>
                </tr>
                <tr>
                    <th colspan="2">(อายุต่ำกว่า 15 ปี)</th>
                    <th colspan="2">(อายุ 15-29 ปี)</th>
                    <th colspan="2">(อายุ 30-39 ปี)</th>
                    <th colspan="2">(อายุ 40-49 ปี)</th>
                    <th colspan="2">(อายุ 50-59 ปี)</th>
                    <th colspan="2">(อายุ 60 ปีขึ้นไป)</th>
                    <th colspan="2">รวม</th>
                </tr>
                <tr>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                    <th>ชาย</th>
                    <th>หญิง</th>
                </tr>
            </thead>
            <tbody>
                <tr class="group-header">
                    <td colspan="17">กลุ่มสนใจ</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td><?php echo htmlspecialchars($amphoe_name); ?></td>
                    <td><?php echo $group_interest['total_male_under_15']; ?></td>
                    <td><?php echo $group_interest['total_female_under_15']; ?></td>
                    <td><?php echo $group_interest['total_male_15_29']; ?></td>
                    <td><?php echo $group_interest['total_female_15_29']; ?></td>
                    <td><?php echo $group_interest['total_male_30_39']; ?></td>
                    <td><?php echo $group_interest['total_female_30_39']; ?></td>
                    <td><?php echo $group_interest['total_male_40_49']; ?></td>
                    <td><?php echo $group_interest['total_female_40_49']; ?></td>
                    <td><?php echo $group_interest['total_male_50_59']; ?></td>
                    <td><?php echo $group_interest['total_female_50_59']; ?></td>
                    <td><?php echo $group_interest['total_male_60_above']; ?></td>
                    <td><?php echo $group_interest['total_female_60_above']; ?></td>
                    <td><?php echo $group_interest['total_male_trainees']; ?></td>
                    <td><?php echo $group_interest['total_female_trainees']; ?></td>
                    <td><?php echo $group_interest['total_trainees']; ?></td>
                </tr>
                <tr class="highlight">
                    <td colspan="2">รวม</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr class="group-header">
                    <td colspan="17">ชั้นเรียนวิชาชีพ</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td><?php echo htmlspecialchars($amphoe_name); ?></td>
                    <td><?php echo $professional_class['total_male_under_15']; ?></td>
                    <td><?php echo $professional_class['total_female_under_15']; ?></td>
                    <td><?php echo $professional_class['total_male_15_29']; ?></td>
                    <td><?php echo $professional_class['total_female_15_29']; ?></td>
                    <td><?php echo $professional_class['total_male_30_39']; ?></td>
                    <td><?php echo $professional_class['total_female_30_39']; ?></td>
                    <td><?php echo $professional_class['total_male_40_49']; ?></td>
                    <td><?php echo $professional_class['total_female_40_49']; ?></td>
                    <td><?php echo $professional_class['total_male_50_59']; ?></td>
                    <td><?php echo $professional_class['total_female_50_59']; ?></td>
                    <td><?php echo $professional_class['total_male_60_above']; ?></td>
                    <td><?php echo $professional_class['total_female_60_above']; ?></td>
                    <td><?php echo $professional_class['total_male_trainees']; ?></td>
                    <td><?php echo $professional_class['total_female_trainees']; ?></td>
                    <td><?php echo $professional_class['total_trainees']; ?></td>
                </tr>
                <tr class="highlight">
                    <td colspan="2">รวม</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr class="group-header">
                    <td colspan="17">1 อำเภอ 1 อาชีพ</td>
                </tr>
                <tr>
                    <td>1</td>
                    <td><?php echo htmlspecialchars($amphoe_name); ?></td>
                    <td><?php echo $one_district_one_job['total_male_under_15']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_under_15']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_15_29']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_15_29']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_30_39']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_30_39']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_40_49']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_40_49']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_50_59']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_50_59']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_60_above']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_60_above']; ?></td>
                    <td><?php echo $one_district_one_job['total_male_trainees']; ?></td>
                    <td><?php echo $one_district_one_job['total_female_trainees']; ?></td>
                    <td><?php echo $one_district_one_job['total_trainees']; ?></td>
                </tr>
                <tr class="highlight">
                    <td colspan="2">รวม</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <tr class="highlight">
                    <td colspan="2">รวมผู้ผ่านการฝึกอาชีพทั้งหมด</td>
                    <td><?php
                        echo $group_interest['total_male_under_15']
                            + $professional_class['total_male_under_15']
                            + $one_district_one_job['total_male_under_15'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_under_15']
                            + $professional_class['total_female_under_15']
                            + $one_district_one_job['total_female_under_15'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_male_15_29']
                            + $professional_class['total_male_15_29']
                            + $one_district_one_job['total_male_15_29'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_15_29']
                            + $professional_class['total_female_15_29']
                            + $one_district_one_job['total_female_15_29'];
                        ?></td>
                    <td> <?php
                            echo $group_interest['total_male_30_39']
                                + $professional_class['total_male_30_39']
                                + $one_district_one_job['total_male_30_39'];
                            ?></td>
                    <td><?php
                        echo $group_interest['total_female_30_39']
                            + $professional_class['total_female_30_39']
                            + $one_district_one_job['total_female_30_39'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_male_40_49']
                            + $professional_class['total_male_40_49']
                            + $one_district_one_job['total_male_40_49'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_40_49']
                            + $professional_class['total_female_40_49']
                            + $one_district_one_job['total_female_40_49'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_male_50_59']
                            + $professional_class['total_male_50_59']
                            + $one_district_one_job['total_male_50_59'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_50_59']
                            + $professional_class['total_female_50_59']
                            + $one_district_one_job['total_female_50_59'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_male_60_above']
                            + $professional_class['total_male_60_above']
                            + $one_district_one_job['total_male_60_above'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_60_above']
                            + $professional_class['total_female_60_above']
                            + $one_district_one_job['total_female_60_above'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_male_trainees']
                            + $professional_class['total_male_trainees']
                            + $one_district_one_job['total_male_trainees'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_female_trainees']
                            + $professional_class['total_female_trainees']
                            + $one_district_one_job['total_female_trainees'];
                        ?></td>
                    <td><?php
                        echo $group_interest['total_trainees']
                            + $professional_class['total_trainees']
                            + $one_district_one_job['total_trainees'];
                        ?></td>
                </tr>
            </tbody>
        </table>

    </div>
    <div class="container table-container">
        <h2 class="text-center mb-4">หลักสูตรอาชีพที่ได้รับความนิยม โดยเรียงตามลำดับ 1 - 5 อันดับ</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">ที่</th>
                    <th rowspan="2">กลุ่มอาชีพ</th>
                    <th colspan="2">ลำดับ 1</th>
                    <th colspan="2">ลำดับ 2</th>
                    <th colspan="2">ลำดับ 3</th>
                    <th colspan="2">ลำดับ 4</th>
                    <th colspan="2">ลำดับ 5</th>
                </tr>
                <tr>
                    <th>ชื่อหลักสูตร</th>
                    <th>ชั่วโมงเรียน</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ชั่วโมงเรียน</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ชั่วโมงเรียน</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ชั่วโมงเรียน</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ชั่วโมงเรียน</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $groups = ['กลุ่มอาชีพเกษตรกรรม', 'กลุ่มอาชีพอุตสาหกรรมหรือหัตถกรรม', 'กลุ่มอาชีพพาณิชยกรรมและบริการ', 'กลุ่มอาชีพสร้างสรรค์', 'กลุ่มอาชีพเฉพาะทาง'];

                foreach ($groups as $index => $group) {
                    echo "<tr>";
                    echo "<td>" . ($index + 1) . "</td>";
                    echo "<td>$group</td>";

                    for ($i = 0; $i < 5; $i++) {
                        if (isset($data[$i]) && $data[$i]['profession_group'] == $group) {
                            echo "<td>" . $data[$i]['activity_name'] . "</td>";
                            echo "<td>" . $data[$i]['course_hours'] . "</td>";
                        } else {
                            echo "<td></td>";
                            echo "<td></td>";
                        }
                    }

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="container table-container">
        <h2 class="text-center mb-4">หลักสูตรที่ได้รับการฝึกอาชีพ ที่ผู้เรียนจบแล้วมีงานทำมากที่สุด เรียงลำดับ 5 หลักสูตร (จากมากไปหาน้อย)</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th rowspan="2">ที่</th>
                    <th rowspan="2">กลุ่มอาชีพ</th>
                    <th colspan="3">ลำดับ 1</th>
                    <th colspan="3">ลำดับ 2</th>
                    <th colspan="3">ลำดับ 3</th>
                    <th colspan="3">ลำดับ 4</th>
                    <th colspan="3">ลำดับ 5</th>
                </tr>
                <tr>
                    <th>ชื่อหลักสูตร</th>
                    <th>ผู้ผ่านการฝึกอาชีพ</th>
                    <th>ผู้จบแล้วมีงานทำ</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ผู้ผ่านการฝึกอาชีพ</th>
                    <th>ผู้จบแล้วมีงานทำ</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ผู้ผ่านการฝึกอาชีพ</th>
                    <th>ผู้จบแล้วมีงานทำ</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ผู้ผ่านการฝึกอาชีพ</th>
                    <th>ผู้จบแล้วมีงานทำ</th>
                    <th>ชื่อหลักสูตร</th>
                    <th>ผู้ผ่านการฝึกอาชีพ</th>
                    <th>ผู้จบแล้วมีงานทำ</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $groups1 = ['กลุ่มอาชีพเกษตรกรรม', 'กลุ่มอาชีพอุตสาหกรรมหรือหัตถกรรม', 'กลุ่มอาชีพพาณิชยกรรมและบริการ', 'กลุ่มอาชีพสร้างสรรค์', 'กลุ่มอาชีพเฉพาะทาง'];

                // แสดงข้อมูลในตาราง
                foreach ($groups1 as $index1 => $group1) {
                    echo "<tr>";
                    echo "<td>" . ($index1 + 1) . "</td>";
                    echo "<td>$group1</td>";

                    for ($j = 0; $j < 5; $j++) {
                        if (isset($data_income[$j]) && $data_income[$j]['profession_group'] == $group1) {
                            echo "<td>" . ($data_income[$j]['activity_name'] ?? '') . "</td>";
                            echo "<td>" . ($data_income[$j]['total_trainees_knowledge'] ?? '') . "</td>";
                            echo "<td>" . ($data_income[$j]['total_income'] ?? '') . "</td>";
                        } else {
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                        }
                    }
                    echo "</tr>";
                }
                ?>

            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>

</html>