<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// ข้อมูลสำหรับการเชื่อมต่อฐานข้อมูล MySQL
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "form_data";

// เชื่อมต่อกับ MySQL
$conn = mysqli_connect($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("การเชื่อมต่อล้มเหลว: " . mysqli_connect_error());
}

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

// เช็คว่ามีการตั้งค่าการแจ้งเตือนในเซสชันหรือไม่
$alert = '';
if (isset($_SESSION['alert'])) {
    $alert = $_SESSION['alert'];
    unset($_SESSION['alert']); // ลบข้อมูลการแจ้งเตือนหลังจากแสดงแล้ว
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายงานกิจกรรมศูนย์ฝึกอาชีพชุมชน</title>
    <!-- เรียกใช้ Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="/saraban/nfecrud/css_reports.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php if ($alert): ?>
        <script>
            Swal.fire({
                position: 'center',
                icon: '<?php echo $alert['type']; ?>',
                title: '<?php echo $alert['message']; ?>',
                showConfirmButton: false,
                timer: 2000
            });
        </script>
    <?php endif; ?>

    <form id="reportForm" action="/saraban/nfecrud/insert_reports.php" method="post" enctype="multipart/form-data">
        <div class="container mt-4">
            <h3 class="text-center mb-4">
                <i class="bi bi-seedling icon-color-green"></i> รายงานกิจกรรมศูนย์ฝึกอาชีพชุมชน
            </h3>

            <div class="d-flex align-items-center">
                <h5 class="form-title mb-0 me-3">
                    <i class="bi bi-geo-alt-fill icon-color-yellow"></i> ศูนย์ส่งเสริมการเรียนรู้ระดับ <?php echo htmlspecialchars($amphoe_name); ?>
                </h5>
                <label for="subdistrict" class="form-label me-2 mb-0">เลือกตำบล</label>
                <select id="subdistrict" name="subdistrict" class="form-select w-auto" required>
                    <?php
                    // ดึงข้อมูลตำบลจากฐานข้อมูลที่มี amphoe_name ตรงกับชื่ออำเภอที่ดึงมา
                    $query_tambon = "SELECT tambon_name FROM tambon WHERE amphoe_name = ?";
                    $stmt_tambon = $conn->prepare($query_tambon);
                    $stmt_tambon->bind_param("s", $amphoe_name);
                    $stmt_tambon->execute();
                    $result_tambon = $stmt_tambon->get_result();

                    if ($result_tambon->num_rows > 0) {
                        while ($row_tambon = $result_tambon->fetch_assoc()) {
                            echo '<option value="' . htmlspecialchars($row_tambon['tambon_name']) . '">' . htmlspecialchars($row_tambon['tambon_name']) . '</option>';
                        }
                    } else {
                        echo '<option value="">ไม่มีข้อมูลตำบล</option>';
                    }
                    ?>
                </select>
            </div>


            <!-- ส่วนของไตรมาสและปีงบประมาณ -->
            <div class="form-section">
                <div class="row mb-3">
                    <div class="col-md-12 d-flex align-items-center flex-wrap">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="quarter" id="quarter1" value="1" required>
                            <label class="form-check-label" for="quarter1">
                                <i class="bi bi-calendar icon-color-blue"></i> ไตรมาส 1
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="quarter" id="quarter2" value="2" disabled>
                            <label class="form-check-label" for="quarter2">
                                <i class="bi bi-calendar icon-color-red"></i> ไตรมาส 2
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="quarter" id="quarter3" value="3" required>
                            <label class="form-check-label" for="quarter3">
                                <i class="bi bi-calendar icon-color-green"></i> ไตรมาส 3
                            </label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="quarter" id="quarter4" value="4" disabled>
                            <label class="form-check-label" for="quarter4">
                                <i class="bi bi-calendar icon-color-yellow"></i> ไตรมาส 4
                            </label>
                        </div>

                        <!-- ปีงบประมาณ -->
                        <label for="fiscal_year" class="form-label ms-4 me-2">
                            <i class="bi bi-cash-stack icon-color-blue"></i> ปีงบประมาณ:
                        </label>
                        <input type="text" id="fiscal_year" name="fiscal_year" class="form-control" value="2567" readonly style="max-width: 80px;">
                    </div>
                </div>
            </div>

            <!-- ส่วนของฟอร์มรายละเอียดกิจกรรม -->
            <div class="form-section">
                <div class="row mb-4">
                    <div class="col-md-5">
                        <label for="activity_type" class="form-label">
                            <i class="bi bi-list-ul icon-color-green"></i> รูปแบบกิจกรรม
                        </label>
                        <select id="activity_type" name="activity_type" class="form-select" required>
                            <option value="">โปรดเลือกรูปแบบกิจกรรม</option>
                            <option value="รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)">รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)</option>
                            <option value="รูปแบบชั้นเรียนวิชาชีพ (31 ชั่วโมงขึ้นไป)">รูปแบบชั้นเรียนวิชาชีพ (31 ชั่วโมงขึ้นไป)</option>
                            <option value="รูปแบบ 1 อำเภอ 1 อาชีพ">รูปแบบ 1 อำเภอ 1 อาชีพ</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="activity_name" class="form-label">
                            <i class="bi bi-journal-text icon-color-blue"></i> ชื่อกิจกรรม
                        </label>
                        <input type="text" id="activity_name" name="activity_name" class="form-control" placeholder="ระบุชื่อกิจกรรม" required>
                    </div>

                    <div class="col-md-3">
                        <label for="course_hours" class="form-label">
                            <i class="bi bi-clock-history icon-color-red"></i> หลักสูตร (ชั่วโมง)
                        </label>
                        <input type="number" id="course_hours" name="course_hours" class="form-control" placeholder="จำนวนชั่วโมง" required>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="profession_group" class="form-label">
                            <i class="bi bi-people icon-color-yellow"></i> กลุ่มอาชีพ
                        </label>
                        <select id="profession_group" name="profession_group" class="form-select" required>
                            <option value="">โปรดเลือกกลุ่มอาชีพ</option>
                            <option value="กลุ่มอาชีพเกษตรกรรม">กลุ่มอาชีพเกษตรกรรม</option>
                            <option value="กลุ่มอาชีพอุตสาหกรรม">กลุ่มอาชีพอุตสาหกรรม</option>
                            <option value="กลุ่มอาชีพพาณิชยกรรม">กลุ่มอาชีพพาณิชยกรรม</option>
                            <option value="กลุ่มอาชีพสร้างสรรค์">กลุ่มอาชีพสร้างสรรค์</option>
                            <option value="กลุ่มอาชีพเฉพาะทาง">กลุ่มอาชีพเฉพาะทาง</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="budget" class="form-label">
                            <i class="bi bi-cash icon-color-green"></i> งบประมาณที่ใช้
                        </label>
                        <input type="number" id="budget" name="budget" class="form-control" placeholder="ระบุงบประมาณ" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">
                            <i class="bi bi-person-plus icon-color-blue"></i> จำนวนผู้ลงทะเบียน
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">ชาย</span>
                            <input type="number" id="male_registration" name="male_registration" class="form-control" placeholder="0" oninput="calculateTotalRegistration()">
                            <span class="input-group-text">หญิง</span>
                            <input type="number" id="female_registration" name="female_registration" class="form-control" placeholder="0" oninput="calculateTotalRegistration()">
                            <span class="input-group-text">รวม</span>
                            <input type="number" id="total_registration" name="total_registration" class="form-control" placeholder="0" readonly>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label">
                            <i class="bi bi-calendar-event icon-color-red"></i> วันที่เริ่มกิจกรรม
                        </label>
                        <input type="date" id="start_date" name="start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label">
                            <i class="bi bi-calendar-check icon-color-green"></i> วันที่สิ้นสุดกิจกรรม
                        </label>
                        <input type="date" id="end_date" name="end_date" class="form-control" required>
                    </div>
                </div>
            </div>

            <!-- จำนวนผู้ผ่านการฝึกอบรม -->
            <div class="form-section">
                <h5 class="form-title d-flex align-items-center">
                    <i class="bi bi-people-fill icon-color-blue me-3"></i> จำนวนผู้ผ่านการฝึกอบรม

                    <div class="d-flex align-items-center">
                        <div class="input-group me-1">
                            <span class="input-group-text">ชาย</span>
                            <input type="number" id="total_male_trainees" name="total_male_trainees" class="form-control" placeholder="0" readonly style="max-width: 80px;">
                        </div>
                        <div class="input-group me-1">
                            <span class="input-group-text">หญิง</span>
                            <input type="number" id="total_female_trainees" name="total_female_trainees" class="form-control" placeholder="0" readonly style="max-width: 80px;">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">รวม</span>
                            <input type="number" id="total_trainees" name="total_trainees" class="form-control" placeholder="0" readonly style="max-width: 80px;">
                        </div>
                    </div>
                </h5>

                <!-- การจัดกลุ่มอายุ -->
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-gender-male icon-color-green"></i> อายุ ต่ำกว่า 15 ปี</label>
                        <input type="number" id="male_under_15" name="male_under_15" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_under_15" name="female_under_15" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-person icon-color-yellow"></i> อายุ 15-29 ปี</label>
                        <input type="number" id="male_15_29" name="male_15_29" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_15_29" name="female_15_29" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-person icon-color-blue"></i> อายุ 30-39 ปี</label>
                        <input type="number" id="male_30_39" name="male_30_39" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_30_39" name="female_30_39" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-person icon-color-red"></i> อายุ 40-49 ปี</label>
                        <input type="number" id="male_40_49" name="male_40_49" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_40_49" name="female_40_49" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-person icon-color-green"></i> อายุ 50-59 ปี</label>
                        <input type="number" id="male_50_59" name="male_50_59" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_50_59" name="female_50_59" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-person icon-color-yellow"></i> อายุ 60 ปีขึ้นไป</label>
                        <input type="number" id="male_60_above" name="male_60_above" class="form-control" placeholder="ชาย" oninput="calculateTotals()">
                        <input type="number" id="female_60_above" name="female_60_above" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotals()">
                    </div>
                </div>
            </div>

            <!-- ผู้ผ่านการฝึกอบรมได้รับความรู้นำไปใช้ -->
            <div class="form-section">
                <h5 class="form-title text-center">
                    <i class="bi bi-lightbulb-fill icon-color-red"></i> ผู้ผ่านการฝึกอบรมได้รับความรู้นำไปใช้

                    <div class="d-flex align-items-center">
                        <div class="input-group me-1">
                            <span class="input-group-text">ชาย</span>
                            <input type="number" id="total_male_trainees_knowledge" name="total_male_trainees_knowledge" class="form-control" placeholder="0" readonly style="max-width: 48px;">
                        </div>
                        <div class="input-group me-1">
                            <span class="input-group-text">หญิง</span>
                            <input type="number" id="total_female_trainees_knowledge" name="total_female_trainees_knowledge" class="form-control" placeholder="0" readonly style="max-width: 48px;">
                        </div>
                        <div class="input-group">
                            <span class="input-group-text">รวม</span>
                            <input type="number" id="total_trainees_knowledge" name="total_trainees_knowledge" class="form-control" placeholder="0" readonly style="max-width: 67px;">
                        </div>
                    </div>

                </h5>
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-hammer icon-color-blue"></i> สร้างอาชีพ</label>
                        <input type="number" id="male_create_job" name="male_create_job" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_create_job" name="female_create_job" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-currency-dollar icon-color-green"></i> เพิ่มรายได้</label>
                        <input type="number" id="male_increase_income" name="male_increase_income" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_increase_income" name="female_increase_income" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-heart-fill icon-color-red"></i> คุณภาพชีวิตดีขึ้น</label>
                        <input type="number" id="male_quality_life" name="male_quality_life" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_quality_life" name="female_quality_life" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-bar-chart icon-color-blue"></i> ต่อยอดภูมิปัญญา</label>
                        <input type="number" id="male_local_wisdom" name="male_local_wisdom" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_local_wisdom" name="female_local_wisdom" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-people-fill icon-color-green"></i> วิสาหกิจชุมชน</label>
                        <input type="number" id="male_community_enterprise" name="male_community_enterprise" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_community_enterprise" name="female_community_enterprise" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label"><i class="bi bi-diagram-3-fill icon-color-yellow"></i> สร้างมูลค่าเพิ่ม</label>
                        <input type="number" id="male_value_addition" name="male_value_addition" class="form-control" placeholder="ชาย" oninput="calculateTotalKnowledge()">
                        <input type="number" id="female_value_addition" name="female_value_addition" class="form-control mt-2" placeholder="หญิง" oninput="calculateTotalKnowledge()">
                    </div>
                </div>
            </div>

            <!-- รายได้ที่เพิ่มขึ้น -->
            <div class="form-section">
                <h5 class="form-title">
                    <i class="bi bi-graph-up-arrow icon-color-blue"></i> รายได้ที่เพิ่มขึ้น
                    <div class="d-flex ms-4 flex-grow-1 align-items-center mb-3">

                        <div class="d-flex align-items-center">
                            <div class="input-group me-1">
                                <span class="input-group-text">ชาย</span>
                                <input type="number" id="total_male_income" name="total_male_income" class="form-control" placeholder="0" readonly style="max-width: 60px;">
                            </div>
                            <div class="input-group me-1">
                                <span class="input-group-text">หญิง</span>
                                <input type="number" id="total_female_income" name="total_female_income" class="form-control" placeholder="0" readonly style="max-width: 60px;">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">รวม</span>
                                <input type="number" id="total_income" name="total_income" class="form-control" placeholder="0" readonly style="max-width: 60px;">
                            </div>
                        </div>
                    </div>

                </h5>
                <p class="small text-muted">(โดยเฉลี่ยต่อเดือน ใช้เป็นข้อมูลในการติดตามและประเมินผลเท่านั้น)</p>

                <div class="row mb-3">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="male_income_below_3000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-green"></i> ต่ำกว่า 3,000 บาท
                            </label>
                            <input type="number" id="male_income_below_3000" name="male_income_below_3000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_below_3000" name="female_income_below_3000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                        <div class="col-md-4">
                            <label for="male_income_3001_5000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-yellow"></i> 3,001 - 5,000 บาท
                            </label>
                            <input type="number" id="male_income_3001_5000" name="male_income_3001_5000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_3001_5000" name="female_income_3001_5000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                        <div class="col-md-4">
                            <label for="male_income_5001_10000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-red"></i> 5,001 - 10,000 บาท
                            </label>
                            <input type="number" id="male_income_5001_10000" name="male_income_5001_10000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_5001_10000" name="female_income_5001_10000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="male_income_10001_15000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-blue"></i> 10,001 - 15,000 บาท
                            </label>
                            <input type="number" id="male_income_10001_15000" name="male_income_10001_15000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_10001_15000" name="female_income_10001_15000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                        <div class="col-md-4">
                            <label for="male_income_15001_30000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-green"></i> 15,001 - 30,000 บาท
                            </label>
                            <input type="number" id="male_income_15001_30000" name="male_income_15001_30000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_15001_30000" name="female_income_15001_30000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                        <div class="col-md-4">
                            <label for="male_income_30001_50000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-yellow"></i> 30,001 - 50,000 บาท
                            </label>
                            <input type="number" id="male_income_30001_50000" name="male_income_30001_50000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_30001_50000" name="female_income_30001_50000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="male_income_50001_100000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-red"></i> 50,001 - 100,000 บาท
                            </label>
                            <input type="number" id="male_income_50001_100000" name="male_income_50001_100000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_50001_100000" name="female_income_50001_100000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                        <div class="col-md-4">
                            <label for="male_income_above_100000" class="form-label">
                                <i class="bi bi-cash-coin icon-color-blue"></i> มากกว่า 100,000 บาท
                            </label>
                            <input type="number" id="male_income_above_100000" name="male_income_above_100000" class="form-control" placeholder="ชาย" oninput="calculateIncomeTotals()">
                            <input type="number" id="female_income_above_100000" name="female_income_above_100000" class="form-control mt-2" placeholder="หญิง" oninput="calculateIncomeTotals()">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ส่วนของสภาพปัญหา/อุปสรรคและข้อเสนอแนะ -->
            <div class="form-section">
                <h5 class="form-title text-center">
                    <i class="bi bi-exclamation-triangle-fill icon-color-red"></i> สภาพปัญหา/อุปสรรคและข้อเสนอแนะ
                </h5>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-x-circle-fill icon-color-red"></i> สภาพปัญหา/อุปสรรค</label>
                        <textarea id="problem_issues" name="problem_issues" required class="form-control textarea-error" rows="4" placeholder="ระบุปัญหา/อุปสรรคที่พบ..." required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label"><i class="bi bi-lightbulb-fill icon-color-green"></i> ข้อเสนอแนะ</label>
                        <textarea id="suggestions" name="suggestions" class="form-control textarea-suggestion" rows="4" placeholder="ระบุข้อเสนอแนะ..." required></textarea>
                    </div>
                </div>
            </div>

            <!-- อัพโหลดภาพ -->
            <div class="form-section">
                <h5 class="form-title text-center"><i class="bi bi-image icon-color-blue"></i> อัพโหลดภาพกิจกรรม</h5>
                <div class="row mb-3">
                    <div class="col-md-3">
                        <div class="upload-box">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('image1').click()">อัพโหลดภาพกิจกรรม 1</button>
                            <input type="file" id="image1" name="image1" class="form-control" accept="image/*" style="display:none;" onchange="previewImage(event, 'preview1')" required>
                            <img id="preview1" src="#" alt="Preview Image 1" class="img-preview mt-2">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="upload-box">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('image2').click()">อัพโหลดภาพกิจกรรม 2</button>
                            <input type="file" id="image2" name="image2" class="form-control" accept="image/*" style="display:none;" onchange="previewImage(event, 'preview2')" required>
                            <img id="preview2" src="#" alt="Preview Image 2" class="img-preview mt-2">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="upload-box">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('image3').click()">อัพโหลดภาพกิจกรรม 3</button>
                            <input type="file" id="image3" name="image3" class="form-control" accept="image/*" style="display:none;" onchange="previewImage(event, 'preview3')" required>
                            <img id="preview3" src="#" alt="Preview Image 3" class="img-preview mt-2">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="upload-box">
                            <button type="button" class="btn btn-secondary" onclick="document.getElementById('image4').click()">อัพโหลดภาพกิจกรรม 4</button>
                            <input type="file" id="image4" name="image4" class="form-control" accept="image/*" style="display:none;" onchange="previewImage(event, 'preview4')" required>
                            <img id="preview4" src="#" alt="Preview Image 4" class="img-preview mt-2">
                        </div>
                    </div>
                </div>
            </div>

            <!-- ปุ่มส่งรายงาน -->
            <div class="submit-button">
                <button type="submit" id="submit-button" name="submit-button" class="btn btn-primary">
                    <i class="bi bi-send"></i> ส่งรายงาน
                </button>
            </div>
        </div>
    </form>

    <!-- เพิ่ม JavaScript ของ Bootstrap 5 -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="/saraban/nfecrud/script.js"></script>

</body>

</html>