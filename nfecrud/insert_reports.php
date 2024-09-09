<?php
session_start();
include('../connection.php');

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];

// รับข้อมูลจากฟอร์ม
$subdistrict = $_POST['subdistrict'];
$quarter = $_POST['quarter'];
$fiscal_year = $_POST['fiscal_year'];
$activity_type = $_POST['activity_type'];
$activity_name = $_POST['activity_name'];
$course_hours = $_POST['course_hours'];
$profession_group = $_POST['profession_group'];
$budget = $_POST['budget'];
$male_registration = $_POST['male_registration'];
$female_registration = $_POST['female_registration'];
$total_registration = $_POST['total_registration'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];
$total_male_trainees = $_POST['total_male_trainees'];
$total_female_trainees = $_POST['total_female_trainees'];
$total_trainees = $_POST['total_trainees'];
$male_under_15 = $_POST['male_under_15'];
$female_under_15 = $_POST['female_under_15'];
$male_15_29 = $_POST['male_15_29'];
$female_15_29 = $_POST['female_15_29'];
$male_30_39 = $_POST['male_30_39'];
$female_30_39 = $_POST['female_30_39'];
$male_40_49 = $_POST['male_40_49'];
$female_40_49 = $_POST['female_40_49'];
$male_50_59 = $_POST['male_50_59'];
$female_50_59 = $_POST['female_50_59'];
$male_60_above = $_POST['male_60_above'];
$female_60_above = $_POST['female_60_above'];

$total_male_trainees_knowledge = $_POST['total_male_trainees_knowledge'];
$total_female_trainees_knowledge = $_POST['total_female_trainees_knowledge'];
$total_trainees_knowledge = $_POST['total_trainees_knowledge'];

$male_create_job = $_POST['male_create_job'];
$female_create_job = $_POST['female_create_job'];
$male_increase_income = $_POST['male_increase_income'];
$female_increase_income = $_POST['female_increase_income'];
$male_quality_life = $_POST['male_quality_life'];
$female_quality_life = $_POST['female_quality_life'];
$male_local_wisdom = $_POST['male_local_wisdom'];
$female_local_wisdom = $_POST['female_local_wisdom'];
$male_community_enterprise = $_POST['male_community_enterprise'];
$female_community_enterprise = $_POST['female_community_enterprise'];
$male_value_addition = $_POST['male_value_addition'];
$female_value_addition = $_POST['female_value_addition'];

$total_male_income = $_POST['total_male_income'];
$total_female_income = $_POST['total_female_income'];
$total_income = $_POST['total_income'];

$male_income_below_3000 = $_POST['male_income_below_3000'];
$female_income_below_3000 = $_POST['female_income_below_3000'];
$male_income_3001_5000 = $_POST['male_income_3001_5000'];
$female_income_3001_5000 = $_POST['female_income_3001_5000'];
$male_income_5001_10000 = $_POST['male_income_5001_10000'];
$female_income_5001_10000 = $_POST['female_income_5001_10000'];
$male_income_10001_15000 = $_POST['male_income_10001_15000'];
$female_income_10001_15000 = $_POST['female_income_10001_15000'];
$male_income_15001_30000 = $_POST['male_income_15001_30000'];
$female_income_15001_30000 = $_POST['female_income_15001_30000'];
$male_income_30001_50000 = $_POST['male_income_30001_50000'];
$female_income_30001_50000 = $_POST['female_income_30001_50000'];
$male_income_50001_100000 = $_POST['male_income_50001_100000'];
$female_income_50001_100000 = $_POST['female_income_50001_100000'];
$male_income_above_100000 = $_POST['male_income_above_100000'];
$female_income_above_100000 = $_POST['female_income_above_100000'];

$problem_issues = $_POST['problem_issues'];
$suggestions = $_POST['suggestions'];



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

// ฟังก์ชันสำหรับอัพโหลดไฟล์
function uploadImage($imageInputName, $uploadDir, $filenameBase, $index) {
    $imageFileType = strtolower(pathinfo($_FILES[$imageInputName]["name"], PATHINFO_EXTENSION));
    $newFileName = $filenameBase . $index . "." . $imageFileType;  // สร้างชื่อไฟล์ใหม่
    $targetFile = $uploadDir . $newFileName;
    $uploadOk = 1;

    // ตรวจสอบว่าเป็นไฟล์รูปภาพจริงหรือไม่
    $check = getimagesize($_FILES[$imageInputName]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        echo "ไฟล์ที่เลือกไม่ใช่รูปภาพ.";
        $uploadOk = 0;
    }

    // ตรวจสอบว่ามีการอัพโหลดไฟล์หรือไม่
    if (file_exists($targetFile)) {
        echo "ไฟล์นี้มีอยู่แล้ว.";
        $uploadOk = 0;
    }

    // ตรวจสอบขนาดไฟล์
    if ($_FILES[$imageInputName]["size"] > 500000) {
        echo "ไฟล์มีขนาดใหญ่เกินไป.";
        $uploadOk = 0;
    }

    // อนุญาตเฉพาะไฟล์บางประเภท
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
        echo "อนุญาตเฉพาะไฟล์ JPG, JPEG, PNG & GIF เท่านั้น.";
        $uploadOk = 0;
    }

    // ตรวจสอบว่า uploadOk = 0 หรือไม่
    if ($uploadOk == 0) {
        echo "ขออภัย, ไฟล์ของคุณไม่ได้รับการอัพโหลด.";
        return null;
    } else {
        // ใช้เส้นทางแบบสัมบูรณ์
        $absolutePath = realpath($uploadDir) . DIRECTORY_SEPARATOR . $newFileName;

        if (move_uploaded_file($_FILES[$imageInputName]["tmp_name"], $absolutePath)) {
            return $newFileName;  // ส่งคืนชื่อไฟล์ใหม่
        } else {
            echo "เกิดข้อผิดพลาดในการอัพโหลดไฟล์.";
            return null;
        }
    }
}

$uploadDir = "uploads/";  
$filenameBase = $amphoe_name . "_" . $subdistrict . "_" . $activity_name . "_";

// เก็บผลลัพธ์การอัพโหลดแต่ละไฟล์
$uploadedImages = [];
for ($i = 1; $i <= 4; $i++) {
    $imageInputName = 'image' . $i;
    if (isset($_FILES[$imageInputName]) && $_FILES[$imageInputName]['error'] == UPLOAD_ERR_OK) {
        $uploadedImages[] = uploadImage($imageInputName, $uploadDir, $filenameBase, $i);
    }
}

// เตรียมคำสั่ง SQL สำหรับการเพิ่มข้อมูลลงในตาราง reports
$query = "INSERT INTO reports (username, amphoe_name, subdistrict, quarter, fiscal_year, activity_type, activity_name, course_hours, profession_group, budget, male_registration, female_registration, total_registration, start_date, end_date, total_male_trainees, total_female_trainees, total_trainees, male_under_15, female_under_15, male_15_29, female_15_29, male_30_39, female_30_39, male_40_49, female_40_49, male_50_59, female_50_59, male_60_above, female_60_above,
 total_male_trainees_knowledge, total_female_trainees_knowledge, total_trainees_knowledge, male_create_job, female_create_job, male_increase_income, female_increase_income, male_quality_life, female_quality_life, male_local_wisdom, female_local_wisdom, male_community_enterprise, female_community_enterprise, male_value_addition, female_value_addition,
  total_male_income, total_female_income, total_income,  male_income_below_3000, female_income_below_3000, male_income_3001_5000, female_income_3001_5000, male_income_5001_10000, female_income_5001_10000, male_income_10001_15000, female_income_10001_15000, male_income_15001_30000, female_income_15001_30000, male_income_30001_50000, female_income_30001_50000, male_income_50001_100000, female_income_50001_100000, male_income_above_100000, female_income_above_100000, 
  problem_issues, suggestions, image1, image2, image3, image4) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
             
$stmt = $conn->prepare($query);

$stmt->bind_param("sssssssisiissssiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiiissssss",



    $username,
    $amphoe_name,
    $subdistrict,
    $quarter,
    $fiscal_year,
    $activity_type,
    $activity_name,
    $course_hours,
    $profession_group,
    $budget, 
    $male_registration,
    $female_registration,
    $total_registration,
    $start_date,
    $end_date,
    $total_male_trainees,
    $total_female_trainees,
    $total_trainees,
    $male_under_15,
    $female_under_15,
    $male_15_29,
    $female_15_29,
    $male_30_39,
    $female_30_39,
    $male_40_49,
    $female_40_49,
    $male_50_59,
    $female_50_59,
    $male_60_above,
    $female_60_above,
    $total_male_trainees_knowledge,
    $total_female_trainees_knowledge,
    $total_trainees_knowledge,
    $male_create_job,
    $female_create_job,
    $male_increase_income,
    $female_increase_income,
    $male_quality_life,
    $female_quality_life,
    $male_local_wisdom,
    $female_local_wisdom,
    $male_community_enterprise,
    $female_community_enterprise,
    $male_value_addition,
    $female_value_addition,
    $total_male_income,
    $total_female_income,
    $total_income,
    $male_income_below_3000, 
    $female_income_below_3000, 
    $male_income_3001_5000, 
    $female_income_3001_5000, 
    $male_income_5001_10000, 
    $female_income_5001_10000, 
    $male_income_10001_15000, 
    $female_income_10001_15000, 
    $male_income_15001_30000, 
    $female_income_15001_30000, 
    $male_income_30001_50000, 
    $female_income_30001_50000, 
    $male_income_50001_100000, 
    $female_income_50001_100000, 
    $male_income_above_100000, 
    $female_income_above_100000, 
    $problem_issues, 
    $suggestions, 
    $uploadedImages[0], 
    $uploadedImages[1], 
    $uploadedImages[2], 
    $uploadedImages[3]

);

if ($stmt->execute()) {
    // เก็บสถานะการแจ้งเตือนในเซสชัน
    $_SESSION['alert'] = [
        'type' => 'success',
        'message' => 'บันทึกข้อมูลเรียบร้อยแล้ว'
    ];
} else {
    // เก็บสถานะการแจ้งเตือนในเซสชัน
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'เกิดข้อผิดพลาดในการบันทึกข้อมูล: ' . $stmt->error
    ];
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$conn->close();

// เปลี่ยนเส้นทางไปยังหน้า reports.php
header("Location: /saraban/welcome.php?page=รายงานผล");
exit;
