<?php
// ฟังก์ชันสำหรับนับจำนวนรายงานตามเงื่อนไขที่กำหนด
function countReports($conn, $username, $activity_type, $profession_group)
{
    $sql = "SELECT COUNT(*) as count 
            FROM reports 
            WHERE username = ? 
            AND activity_type = ? 
            AND profession_group = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $activity_type, $profession_group);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function countReportsCombined($conn, $username, $profession_group)
{
    $sql = "SELECT COUNT(*) as count 
            FROM reports 
            WHERE username = ? 
            AND profession_group = ? 
            AND (activity_type = 'รูปแบบชั้นเรียนวิชาชีพ (31 ชั่วโมงขึ้นไป)' OR activity_type = 'รูปแบบ 1 อำเภอ 1 อาชีพ')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $profession_group);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

// ฟังก์ชันสำหรับรวมจำนวนฟิลด์ total_registration ตามเงื่อนไขที่กำหนด
function sumTotalRegistration($conn, $username, $profession_group, $activity_types)
{
    $placeholders = implode(',', array_fill(0, count($activity_types), '?'));
    $types = str_repeat('s', count($activity_types)) . 'ss';
    $sql = "SELECT SUM(total_registration) as total 
            FROM reports 
            WHERE username = ? 
            AND profession_group = ? 
            AND activity_type IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $params = array_merge([$username, $profession_group], $activity_types);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}

// ฟังก์ชันสำหรับรวมจำนวนฟิลด์ต่างๆ ตามเงื่อนไขที่กำหนด
function sumField($conn, $username, $profession_group, $activity_types, $field)
{
    $placeholders = implode(',', array_fill(0, count($activity_types), '?'));
    $types = str_repeat('s', count($activity_types)) . 'ss';
    $sql = "SELECT SUM($field) as total 
            FROM reports 
            WHERE username = ? 
            AND profession_group = ? 
            AND activity_type IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $params = array_merge([$username, $profession_group], $activity_types);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}
// ฟังก์ชันสำหรับรวมจำนวนฟิลด์ total_trainees_knowledge ตามเงื่อนไขที่กำหนด
function sumTotalTraineesKnowledge($conn, $username, $profession_group, $activity_types)
{
    $placeholders = implode(',', array_fill(0, count($activity_types), '?'));
    $types = str_repeat('s', count($activity_types)) . 'ss';
    $sql = "SELECT SUM(total_trainees_knowledge) as total 
            FROM reports 
            WHERE username = ? 
            AND profession_group = ? 
            AND activity_type IN ($placeholders)";
    $stmt = $conn->prepare($sql);
    $params = array_merge([$username, $profession_group], $activity_types);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total'] ?? 0;
}
function getTotalIncomeByGender($conn, $username)
{
    $sql = "SELECT 
                SUM(male_income_below_3000 + female_income_below_3000) as income_below_3000,
                SUM(male_income_3001_5000 + female_income_3001_5000) as income_3001_5000,
                SUM(male_income_5001_10000 + female_income_5001_10000) as income_5001_10000,
                SUM(male_income_10001_15000 + female_income_10001_15000) as income_10001_15000,
                SUM(male_income_15001_30000 + female_income_15001_30000) as income_15001_30000,
                SUM(male_income_30001_50000 + female_income_30001_50000) as income_30001_50000,
                SUM(male_income_50001_100000 + female_income_50001_100000) as income_50001_100000,
                SUM(male_income_above_100000 + female_income_above_100000) as income_above_100000
            FROM reports 
            WHERE username = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username); // ผูกค่า username กับคำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        $data = [
            'income_below_3000' => 0,
            'income_3001_5000' => 0,
            'income_5001_10000' => 0,
            'income_10001_15000' => 0,
            'income_15001_30000' => 0,
            'income_30001_50000' => 0,
            'income_50001_100000' => 0,
            'income_above_100000' => 0
        ];
    }

    $stmt->close(); // ปิดคำสั่ง SQL
    return $data;
}

$total_income = getTotalIncomeByGender($conn, $username);

// รวมค่า total income ทั้งหมด
$total_income_sum = array_sum($total_income);
// กำหนดประเภทกิจกรรมที่ต้องการ
$activity_types_interest = ["รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)"];
$activity_types_professional = ["รูปแบบชั้นเรียนวิชาชีพ (31 ชั่วโมงขึ้นไป)", "รูปแบบ 1 อำเภอ 1 อาชีพ"];

function getTotalTraineesByActivityType($conn, $username, $activity_type)
{
    $sql = "SELECT 
                SUM(male_under_15) as total_male_under_15,
                SUM(female_under_15) as total_female_under_15,
                SUM(male_15_29) as total_male_15_29,
                SUM(female_15_29) as total_female_15_29,
                SUM(male_30_39) as total_male_30_39,
                SUM(female_30_39) as total_female_30_39,
                SUM(male_40_49) as total_male_40_49,
                SUM(female_40_49) as total_female_40_49,
                SUM(male_50_59) as total_male_50_59,
                SUM(female_50_59) as total_female_50_59,
                SUM(male_60_above) as total_male_60_above,
                SUM(female_60_above) as total_female_60_above,
                SUM(total_male_trainees) as total_male_trainees,
                SUM(total_female_trainees) as total_female_trainees,
                SUM(total_trainees) as total_trainees
            FROM reports 
            WHERE username = ? AND activity_type = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $activity_type); // ผูกค่า username และ activity_type กับคำสั่ง SQL
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    } else {
        return [
            'total_male_under_15' => 0,
            'total_female_under_15' => 0,
            'total_male_15_29' => 0,
            'total_female_15_29' => 0,
            'total_male_30_39' => 0,
            'total_female_30_39' => 0,
            'total_male_40_49' => 0,
            'total_female_40_49' => 0,
            'total_male_50_59' => 0,
            'total_female_50_59' => 0,
            'total_male_60_above' => 0,
            'total_female_60_above' => 0,
            'total_male_trainees' => 0,
            'total_female_trainees' => 0,
            'total_trainees' => 0
        ];
    }
}


// ดึงข้อมูลจากตาราง reports ที่ตรงกับ username
$query = "SELECT profession_group, activity_name, course_hours 
          FROM reports 
          WHERE username = ?
          ORDER BY total_registration DESC 
          LIMIT 5";

$stmt = $conn->prepare($query);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// ปิดการใช้งาน stmt หลังจากที่ใช้งานเสร็จแล้ว
$stmt->close();

// ดึงข้อมูลจากตาราง reports ที่ตรงกับ username โดยเรียงตาม total_income
$query2 = "SELECT profession_group, activity_name, total_trainees_knowledge, total_income 
          FROM reports 
          WHERE username = ?
          ORDER BY total_income DESC 
          LIMIT 5";

$stmt2 = $conn->prepare($query2);
if ($stmt2 === false) {
    die('Prepare failed: ' . $conn->error);
}
$stmt2->bind_param("s", $username);
$stmt2->execute();
$result2 = $stmt2->get_result();

$data_income = [];
if ($result2->num_rows > 0) {
    while ($row2 = $result2->fetch_assoc()) {
        $data_income[] = $row2;
    }
}

// ปิดการใช้งาน stmt2 และการเชื่อมต่อฐานข้อมูลหลังจากที่ใช้งานเสร็จแล้ว
$stmt2->close();
// ดึงข้อมูลสำหรับแต่ละประเภทของ activity_type
$group_interest = getTotalTraineesByActivityType($conn, $username, 'รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)');
$professional_class = getTotalTraineesByActivityType($conn, $username, 'รูปแบบชั้นเรียนวิชาชีพ (31 ชั่วโมงขึ้นไป)');
$one_district_one_job = getTotalTraineesByActivityType($conn, $username, 'รูปแบบ 1 อำเภอ 1 อาชีพ');

// นับจำนวนและรวมค่าสำหรับกลุ่มอาชีพเกษตรกรรม
$count_agriculture_1_30 = countReports($conn, $username, "รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)", "กลุ่มอาชีพเกษตรกรรม");
$count_agriculture_combined = countReportsCombined($conn, $username, "กลุ่มอาชีพเกษตรกรรม");
$total_registration_agriculture_interest = sumTotalRegistration($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest);
$total_registration_agriculture_professional = sumTotalRegistration($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional);
$total_trainees_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'total_trainees');
$total_trainees_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'total_trainees');
$total_create_job_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_create_job + female_create_job');
$total_create_job_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_create_job + female_create_job');
$total_income_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_increase_income + female_increase_income');
$total_income_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_increase_income + female_increase_income');
$total_quality_life_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_quality_life + female_quality_life');
$total_quality_life_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_quality_life + female_quality_life');
$total_local_wisdom_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_local_wisdom + female_local_wisdom');
$total_local_wisdom_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_local_wisdom + female_local_wisdom');
$total_community_enterprise_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_community_enterprise + female_community_enterprise');
$total_community_enterprise_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_community_enterprise + female_community_enterprise');
$total_value_addition_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'male_value_addition + female_value_addition');
$total_value_addition_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'male_value_addition + female_value_addition');
$total_trainees_knowledge_agriculture_interest = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest);
$total_trainees_knowledge_agriculture_professional = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional);
$total_income_agriculture_interest = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_interest, 'total_income');
$total_income_agriculture_professional = sumField($conn, $username, "กลุ่มอาชีพเกษตรกรรม", $activity_types_professional, 'total_income');

// นับจำนวนและรวมค่าสำหรับกลุ่มอาชีพอุตสาหกรรม
$count_industry_1_30 = countReports($conn, $username, "รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)", "กลุ่มอาชีพอุตสาหกรรม");
$count_industry_combined = countReportsCombined($conn, $username, "กลุ่มอาชีพอุตสาหกรรม");
$total_registration_industry_interest = sumTotalRegistration($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest);
$total_registration_industry_professional = sumTotalRegistration($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional);
$total_trainees_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'total_trainees');
$total_trainees_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'total_trainees');
$total_create_job_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_create_job + female_create_job');
$total_create_job_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_create_job + female_create_job');
$total_income_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_increase_income + female_increase_income');
$total_income_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_increase_income + female_increase_income');
$total_quality_life_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_quality_life + female_quality_life');
$total_quality_life_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_quality_life + female_quality_life');
$total_local_wisdom_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_local_wisdom + female_local_wisdom');
$total_local_wisdom_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_local_wisdom + female_local_wisdom');
$total_community_enterprise_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_community_enterprise + female_community_enterprise');
$total_community_enterprise_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_community_enterprise + female_community_enterprise');
$total_value_addition_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'male_value_addition + female_value_addition');
$total_value_addition_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'male_value_addition + female_value_addition');
$total_trainees_knowledge_industry_interest = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest);
$total_trainees_knowledge_industry_professional = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional);
$total_income_industry_interest = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_interest, 'total_income');
$total_income_industry_professional = sumField($conn, $username, "กลุ่มอาชีพอุตสาหกรรม", $activity_types_professional, 'total_income');

// นับจำนวนและรวมค่าสำหรับกลุ่มอาชีพพาณิชยกรรม
$count_commerce_1_30 = countReports($conn, $username, "รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)", "กลุ่มอาชีพพาณิชยกรรม");
$count_commerce_combined = countReportsCombined($conn, $username, "กลุ่มอาชีพพาณิชยกรรม");
$total_registration_commerce_interest = sumTotalRegistration($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest);
$total_registration_commerce_professional = sumTotalRegistration($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional);
$total_trainees_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'total_trainees');
$total_trainees_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'total_trainees');
$total_create_job_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_create_job + female_create_job');
$total_create_job_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_create_job + female_create_job');
$total_income_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_increase_income + female_increase_income');
$total_income_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_increase_income + female_increase_income');
$total_quality_life_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_quality_life + female_quality_life');
$total_quality_life_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_quality_life + female_quality_life');
$total_local_wisdom_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_local_wisdom + female_local_wisdom');
$total_local_wisdom_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_local_wisdom + female_local_wisdom');
$total_community_enterprise_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_community_enterprise + female_community_enterprise');
$total_community_enterprise_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_community_enterprise + female_community_enterprise');
$total_value_addition_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'male_value_addition + female_value_addition');
$total_value_addition_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'male_value_addition + female_value_addition');
$total_trainees_knowledge_commerce_interest = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest);
$total_trainees_knowledge_commerce_professional = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional);
$total_income_commerce_interest = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_interest, 'total_income');
$total_income_commerce_professional = sumField($conn, $username, "กลุ่มอาชีพพาณิชยกรรม", $activity_types_professional, 'total_income');

// นับจำนวนและรวมค่าสำหรับกลุ่มอาชีพสร้างสรรค์
$count_creative_1_30 = countReports($conn, $username, "รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)", "กลุ่มอาชีพสร้างสรรค์");
$count_creative_combined = countReportsCombined($conn, $username, "กลุ่มอาชีพสร้างสรรค์");
$total_registration_creative_interest = sumTotalRegistration($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest);
$total_registration_creative_professional = sumTotalRegistration($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional);
$total_trainees_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'total_trainees');
$total_trainees_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'total_trainees');
$total_create_job_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_create_job + female_create_job');
$total_create_job_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_create_job + female_create_job');
$total_income_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_increase_income + female_increase_income');
$total_income_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_increase_income + female_increase_income');
$total_quality_life_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_quality_life + female_quality_life');
$total_quality_life_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_quality_life + female_quality_life');
$total_local_wisdom_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_local_wisdom + female_local_wisdom');
$total_local_wisdom_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_local_wisdom + female_local_wisdom');
$total_community_enterprise_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_community_enterprise + female_community_enterprise');
$total_community_enterprise_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_community_enterprise + female_community_enterprise');
$total_value_addition_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'male_value_addition + female_value_addition');
$total_value_addition_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'male_value_addition + female_value_addition');
$total_trainees_knowledge_creative_interest = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest);
$total_trainees_knowledge_creative_professional = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional);
$total_income_creative_interest = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_interest, 'total_income');
$total_income_creative_professional = sumField($conn, $username, "กลุ่มอาชีพสร้างสรรค์", $activity_types_professional, 'total_income');

// นับจำนวนและรวมค่าสำหรับกลุ่มอาชีพเฉพาะทาง
$count_specialized_1_30 = countReports($conn, $username, "รูปแบบกลุ่มสนใจหลักสูตร (ไม่เกิน 30ชั่วโมง)", "กลุ่มอาชีพเฉพาะทาง");
$count_specialized_combined = countReportsCombined($conn, $username, "กลุ่มอาชีพเฉพาะทาง");
$total_registration_specialized_interest = sumTotalRegistration($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest);
$total_registration_specialized_professional = sumTotalRegistration($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional);
$total_trainees_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'total_trainees');
$total_trainees_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'total_trainees');
$total_create_job_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_create_job + female_create_job');
$total_create_job_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_create_job + female_create_job');
$total_income_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_increase_income + female_increase_income');
$total_income_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_increase_income + female_increase_income');
$total_quality_life_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_quality_life + female_quality_life');
$total_quality_life_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_quality_life + female_quality_life');
$total_local_wisdom_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_local_wisdom + female_local_wisdom');
$total_local_wisdom_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_local_wisdom + female_local_wisdom');
$total_community_enterprise_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_community_enterprise + female_community_enterprise');
$total_community_enterprise_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_community_enterprise + female_community_enterprise');
$total_value_addition_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'male_value_addition + female_value_addition');
$total_value_addition_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'male_value_addition + female_value_addition');
$total_trainees_knowledge_specialized_interest = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest);
$total_trainees_knowledge_specialized_professional = sumTotalTraineesKnowledge($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional);
$total_income_specialized_interest = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_interest, 'total_income');
$total_income_specialized_professional = sumField($conn, $username, "กลุ่มอาชีพเฉพาะทาง", $activity_types_professional, 'total_income');
?>