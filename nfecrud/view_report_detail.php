<?php
// ตรวจสอบสถานะของเซสชันก่อนที่จะเริ่มเซสชันใหม่
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('../connection.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    echo "กรุณาเข้าสู่ระบบ";
    exit;
}

// ตรวจสอบว่ามีการส่ง ID ของรายงานมาไหม
if (!isset($_GET['id'])) {
    echo "ไม่พบข้อมูลรายงาน";
    exit;
}

$report_id = $_GET['id'];
$username = $_SESSION['username']; // เก็บ username ที่ล็อกอิน

// ฟังก์ชันสำหรับดึงรายละเอียดรายงานจากฐานข้อมูลที่ตรงกับ ID
function getReportDetail($conn, $report_id, $username) {
    $sql = "SELECT amphoe_name, subdistrict, quarter, fiscal_year, activity_type, activity_name, course_hours, profession_group, budget, 
            male_registration, female_registration, total_registration, start_date, end_date,
            total_male_trainees, total_female_trainees, total_trainees, male_under_15, female_under_15, male_15_29, female_15_29, 
            male_30_39, female_30_39, male_40_49, female_40_49, male_50_59, female_50_59, male_60_above, female_60_above,
            total_male_trainees_knowledge, total_female_trainees_knowledge, total_trainees_knowledge, male_create_job, female_create_job, 
            male_increase_income, female_increase_income, male_quality_life, female_quality_life,
            male_local_wisdom, female_local_wisdom, male_community_enterprise, female_community_enterprise, male_value_addition, female_value_addition, 
            total_male_income, total_female_income, total_income,
            male_income_below_3000, female_income_below_3000, male_income_3001_5000, female_income_3001_5000, male_income_5001_10000, 
            female_income_5001_10000, male_income_10001_15000, female_income_10001_15000,
            male_income_15001_30000, female_income_15001_30000, male_income_30001_50000, female_income_30001_50000, 
            male_income_50001_100000, female_income_50001_100000, male_income_above_100000, female_income_above_100000,
            problem_issues, suggestions, image1, image2, image3, image4
            FROM reports 
            WHERE id = ? AND username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $report_id, $username);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$report = getReportDetail($conn, $report_id, $username);

if ($report):
?>
    <div class="container mt-5">
        <div class="card shadow-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-file-earmark-text-fill me-2"></i>รายละเอียด</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-info-circle-fill icon-color-blue"></i> ข้อมูลทั่วไป</h6>
                        <p>อำเภอ: <?php echo htmlspecialchars($report['amphoe_name']); ?></p>
                        <p>ตำบล: <?php echo htmlspecialchars($report['subdistrict']); ?></p>
                        <p>ไตรมาส: <?php echo htmlspecialchars($report['quarter']); ?></p>
                        <p>ปีงบประมาณ: <?php echo htmlspecialchars($report['fiscal_year']); ?></p>
                        <p>ประเภทกิจกรรม: <?php echo htmlspecialchars($report['activity_type']); ?></p>
                        <p>ชื่อกิจกรรม: <?php echo htmlspecialchars($report['activity_name']); ?></p>
                        <p>ชั่วโมงหลักสูตร: <?php echo htmlspecialchars($report['course_hours']); ?> ชั่วโมง</p>
                        <p>กลุ่มอาชีพ: <?php echo htmlspecialchars($report['profession_group']); ?></p>
                        <p>งบประมาณ: <?php echo htmlspecialchars(number_format($report['budget'])); ?> บาท</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-person-lines-fill icon-color-red"></i> การลงทะเบียน</h6>
                        <p>จำนวนผู้ลงทะเบียน (ชาย): <?php echo htmlspecialchars($report['male_registration']); ?> คน</p>
                        <p>จำนวนผู้ลงทะเบียน (หญิง): <?php echo htmlspecialchars($report['female_registration']); ?> คน</p>
                        <p>จำนวนผู้ลงทะเบียนรวม: <?php echo htmlspecialchars($report['total_registration']); ?> คน</p>
                        <p>วันที่เริ่มกิจกรรม: <?php echo htmlspecialchars($report['start_date']); ?></p>
                        <p>วันที่สิ้นสุดกิจกรรม: <?php echo htmlspecialchars($report['end_date']); ?></p>
                    </div>
                </div>
                <hr>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="bi bi-people-fill icon-color-green"></i> ผู้ผ่านการฝึกอบรม</h6>
                        <p>จำนวนผู้ผ่านการฝึกอบรม (ชาย): <?php echo htmlspecialchars($report['total_male_trainees']); ?> คน</p>
                        <p>จำนวนผู้ผ่านการฝึกอบรม (หญิง): <?php echo htmlspecialchars($report['total_female_trainees']); ?> คน</p>
                        <p>จำนวนผู้ผ่านการฝึกอบรมรวม: <?php echo htmlspecialchars($report['total_trainees']); ?> คน</p>
                        <p>ผู้ที่ได้รับความรู้นำไปใช้ (ชาย): <?php echo htmlspecialchars($report['total_male_trainees_knowledge']); ?> คน</p>
                        <p>ผู้ที่ได้รับความรู้นำไปใช้ (หญิง): <?php echo htmlspecialchars($report['total_female_trainees_knowledge']); ?> คน</p>
                        <p>รวมผู้ที่ได้รับความรู้นำไปใช้: <?php echo htmlspecialchars($report['total_trainees_knowledge']); ?> คน</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-bar-chart-fill icon-color-yellow"></i> ผลลัพธ์หลังการฝึกอบรม</h6>
                        <p>ผู้สร้างอาชีพ (ชาย): <?php echo htmlspecialchars($report['male_create_job']); ?> คน</p>
                        <p>ผู้สร้างอาชีพ (หญิง): <?php echo htmlspecialchars($report['female_create_job']); ?> คน</p>
                        <p>ผู้เพิ่มรายได้ (ชาย): <?php echo htmlspecialchars($report['male_increase_income']); ?> คน</p>
                        <p>ผู้เพิ่มรายได้ (หญิง): <?php echo htmlspecialchars($report['female_increase_income']); ?> คน</p>
                        <p>คุณภาพชีวิตดีขึ้น (ชาย): <?php echo htmlspecialchars($report['male_quality_life']); ?> คน</p>
                        <p>คุณภาพชีวิตดีขึ้น (หญิง): <?php echo htmlspecialchars($report['female_quality_life']); ?> คน</p>
                        <p>ต่อยอดภูมิปัญญา (ชาย): <?php echo htmlspecialchars($report['male_local_wisdom']); ?> คน</p>
                        <p>ต่อยอดภูมิปัญญา (หญิง): <?php echo htmlspecialchars($report['female_local_wisdom']); ?> คน</p>
                        <p>วิสาหกิจชุมชน (ชาย): <?php echo htmlspecialchars($report['male_community_enterprise']); ?> คน</p>
                        <p>วิสาหกิจชุมชน (หญิง): <?php echo htmlspecialchars($report['female_community_enterprise']); ?> คน</p>
                        <p>สร้างมูลค่าเพิ่ม (ชาย): <?php echo htmlspecialchars($report['male_value_addition']); ?> คน</p>
                        <p>สร้างมูลค่าเพิ่ม (หญิง): <?php echo htmlspecialchars($report['female_value_addition']); ?> คน</p>
                    </div>
                </div>
                <hr>
                <h6><i class="bi bi-currency-exchange icon-color-orange"></i> รายได้ที่เพิ่มขึ้น</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <p>รายได้ต่ำกว่า 3,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_below_3000']); ?> บาท</p>
                        <p>รายได้ต่ำกว่า 3,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_below_3000']); ?> บาท</p>
                        <p>รายได้ 3,001 - 5,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_3001_5000']); ?> บาท</p>
                        <p>รายได้ 3,001 - 5,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_3001_5000']); ?> บาท</p>
                        <p>รายได้ 5,001 - 10,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_5001_10000']); ?> บาท</p>
                        <p>รายได้ 5,001 - 10,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_5001_10000']); ?> บาท</p>
                    </div>
                    <div class="col-md-6">
                        <p>รายได้ 10,001 - 15,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_10001_15000']); ?> บาท</p>
                        <p>รายได้ 10,001 - 15,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_10001_15000']); ?> บาท</p>
                        <p>รายได้ 15,001 - 30,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_15001_30000']); ?> บาท</p>
                        <p>รายได้ 15,001 - 30,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_15001_30000']); ?> บาท</p>
                        <p>รายได้ 30,001 - 50,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_30001_50000']); ?> บาท</p>
                        <p>รายได้ 30,001 - 50,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_30001_50000']); ?> บาท</p>
                        <p>รายได้ 50,001 - 100,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_50001_100000']); ?> บาท</p>
                        <p>รายได้ 50,001 - 100,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_50001_100000']); ?> บาท</p>
                        <p>รายได้มากกว่า 100,000 บาท (ชาย): <?php echo htmlspecialchars($report['male_income_above_100000']); ?> บาท</p>
                        <p>รายได้มากกว่า 100,000 บาท (หญิง): <?php echo htmlspecialchars($report['female_income_above_100000']); ?> บาท</p>
                    </div>
                </div>
                <hr>
                <h6><i class="bi bi-exclamation-circle-fill icon-color-red"></i> สภาพปัญหา/อุปสรรคและข้อเสนอแนะ</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p>สภาพปัญหา/อุปสรรค: <?php echo nl2br(htmlspecialchars($report['problem_issues'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p>ข้อเสนอแนะ: <?php echo nl2br(htmlspecialchars($report['suggestions'])); ?></p>
                    </div>
                </div>
                <hr>
                <h6><i class="bi bi-images icon-color-purple"></i> ภาพกิจกรรม</h6>
                <div class="image-container">
                    <?php 
                    if ($report['image1']) {
                        echo '<img src="/saraban/nfecrud/uploads/' . htmlspecialchars($report['image1']) . '" alt="ภาพที่ 1" class="report-image">';
                    }
                    if ($report['image2']) {
                        echo '<img src="/saraban/nfecrud/uploads/' . htmlspecialchars($report['image2']) . '" alt="ภาพที่ 2" class="report-image">';
                    }
                    if ($report['image3']) {
                        echo '<img src="/saraban/nfecrud/uploads/' . htmlspecialchars($report['image3']) . '" alt="ภาพที่ 3" class="report-image">';
                    }
                    if ($report['image4']) {
                        echo '<img src="/saraban/nfecrud/uploads/' . htmlspecialchars($report['image4']) . '" alt="ภาพที่ 4" class="report-image">';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Prompt', sans-serif;
        }

        .card-header {
            background: linear-gradient(45deg, #3a6073, #16222a);
            color: white;
            padding: 15px;
        }

        .card-body h6 {
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 15px;
            border-left: 4px solid #007bff;
            padding-left: 10px;
        }

        .report-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            margin: 10px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .icon-color-blue {
            color: #007bff;
        }

        .icon-color-red {
            color: #dc3545;
        }

        .icon-color-green {
            color: #28a745;
        }

        .icon-color-yellow {
            color: #ffc107;
        }

        .icon-color-orange {
            color: #fd7e14;
        }

        .icon-color-purple {
            color: #6f42c1;
        }

        hr {
            margin: 25px 0;
            border-color: #ddd;
        }

        p {
            font-size: 0.9rem;
            color: #555;
        }
    </style>
<?php
else:
    echo "<div class='container mt-5'><p class='text-center text-danger'>ไม่พบรายละเอียดของรายงานนี้</p></div>";
endif;
?>
