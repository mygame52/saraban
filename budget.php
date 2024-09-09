<?php
session_start();

// ตรวจสอบว่ามีการ login หรือไม่
if (!isset($_SESSION['username'])) {
    // ถ้ายังไม่ได้ login ให้เปลี่ยนไปยังหน้า login.php
    header("Location: login.php");
    exit();
}

// รับค่า username จาก session
$username = $_SESSION['username'];

// ตรวจสอบสิทธิ์การเข้าถึง form.php
if ($username != 'admin' && $username != '0000' && $username != '3014' && $username != '4949') {
    // ถ้าไม่มีสิทธิ์ ให้แสดงข้อความว่าไม่อนุญาตให้เข้าถึง
    echo "Access Denied";
    exit();
}

require_once('connection.php');

// ตรวจสอบว่ามีการส่งค่า school_code มาหรือไม่
$selected_school_code = isset($_POST['school_code']) ? $_POST['school_code'] : '';

// ดึงข้อมูลจากฐานข้อมูลสำหรับ dropdown
$query_schools = "SELECT DISTINCT school_code, province FROM budget34_2567";
$result_schools = mysqli_query($conn, $query_schools);

// กำหนดค่าเริ่มต้น
$budget_amount = 0;
$total_budget = 0;
$remaining_budget = 0;
$percentage = 0;
$class_31hrs_budget = 0;
$district_occupation_budget = 0;
$province = '';

if ($selected_school_code) {
    if ($selected_school_code == 'all') {
        // ดึงข้อมูลทั้งหมด
        $query_all = "SELECT province, budget, school_code FROM budget34_2567";
        $result_all = mysqli_query($conn, $query_all);
    } else if ($selected_school_code == '1280200000') {
        // กำหนดการคำนวณเฉพาะเมื่อเลือก school_code 1280200000 (สกร.จังหวัดนครศรีธรรมราช)
        $query_budget = "SELECT budget FROM budget34_2567 WHERE school_code = '$selected_school_code'";
        $result_budget = mysqli_query($conn, $query_budget);
        if ($row_budget = mysqli_fetch_assoc($result_budget)) {
            $budget_amount = $row_budget['budget'];

            // ดึงข้อมูลจาก form_submissions สำหรับ "ชั้นเรียน 31 ชม.ขึ้นไป"
            $query_class_31hrs = "SELECT SUM(budget) AS class_31hrs_budget FROM form_submissions WHERE book_type = 'ชั้นเรียน 31 ชม.ขึ้นไป'";
            $result_class_31hrs = mysqli_query($conn, $query_class_31hrs);
            $row_class_31hrs = mysqli_fetch_assoc($result_class_31hrs);
            $class_31hrs_budget = $row_class_31hrs['class_31hrs_budget'];

            // ดึงข้อมูลจาก form_submissions สำหรับ "1 อำเภอ 1 อาชีพ"
            $query_district_occupation = "SELECT SUM(budget) AS district_occupation_budget FROM form_submissions WHERE book_type = '1 อำเภอ 1 อาชีพ'";
            $result_district_occupation = mysqli_query($conn, $query_district_occupation);
            $row_district_occupation = mysqli_fetch_assoc($result_district_occupation);
            $district_occupation_budget = $row_district_occupation['district_occupation_budget'];

            // ดึงข้อมูลจาก form_submissions สำหรับเอกสารชุดเบิกที่ส่งแล้ว
            $query_submissions = "SELECT SUM(budget) AS total_budget FROM form_submissions";
            $result_submissions = mysqli_query($conn, $query_submissions);
            $row_submissions = mysqli_fetch_assoc($result_submissions);
            $total_budget = $row_submissions['total_budget'];

            // คำนวณเปอร์เซ็นต์
            $remaining_budget = $budget_amount - $total_budget;
            $percentage = ($total_budget / $budget_amount) * 100;
        }
    } else {
        // ดึงข้อมูลจาก budget34_2567 สำหรับสถานศึกษาอื่น ๆ
        $query_budget = "SELECT province, budget FROM budget34_2567 WHERE school_code = '$selected_school_code'";
        $result_budget = mysqli_query($conn, $query_budget);
        if ($row_budget = mysqli_fetch_assoc($result_budget)) {
            $budget_amount = $row_budget['budget'];
            $province = $row_budget['province'];

            // ดึงข้อมูลจาก form_submissions
            $query_submissions = "SELECT SUM(budget) AS total_budget FROM form_submissions WHERE district = '$selected_school_code'";
            $result_submissions = mysqli_query($conn, $query_submissions);
            $row_submissions = mysqli_fetch_assoc($result_submissions);
            $total_budget = $row_submissions['total_budget'];

            // ดึงข้อมูลสำหรับ "ชั้นเรียน 31 ชม.ขึ้นไป"
            $query_class_31hrs = "SELECT SUM(budget) AS class_31hrs_budget FROM form_submissions WHERE district = '$selected_school_code' AND book_type = 'ชั้นเรียน 31 ชม.ขึ้นไป'";
            $result_class_31hrs = mysqli_query($conn, $query_class_31hrs);
            $row_class_31hrs = mysqli_fetch_assoc($result_class_31hrs);
            $class_31hrs_budget = $row_class_31hrs['class_31hrs_budget'];

            // ดึงข้อมูลสำหรับ "1 อำเภอ 1 อาชีพ"
            $query_district_occupation = "SELECT SUM(budget) AS district_occupation_budget FROM form_submissions WHERE district = '$selected_school_code' AND book_type = '1 อำเภอ 1 อาชีพ'";
            $result_district_occupation = mysqli_query($conn, $query_district_occupation);
            $row_district_occupation = mysqli_fetch_assoc($result_district_occupation);
            $district_occupation_budget = $row_district_occupation['district_occupation_budget'];

            // คำนวณเปอร์เซ็นต์
            $remaining_budget = $budget_amount - $total_budget;
            $percentage = ($total_budget / $budget_amount) * 100;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>งบประมาณศูนย์ฝึกอาชีพ</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 20px;
            background-color: #f8f9fa;
        }
        h1 {
            margin-bottom: 30px;
        }
        .card {
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 15px;
        }
        .form-select, .btn {
            border-radius: 20px;
        }
        .chart-container {
            width: 100%;
            max-width: 500px;
            margin: auto;
        }
        table {
            border-collapse: collapse;
        }
        table th, table td {
            text-align: center;
            vertical-align: middle;
        }
        .table thead th {
            background-color: #007bff;
            color: white;
        }
        .table tbody tr:nth-child(odd) {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">เอกสารชุดเบิกงานการศึกษาต่อเนื่อง</h1>
        <div class="card shadow-sm">
            <form method="post" action="">
                <div class="mb-3">
                    <label for="school_code" class="form-label">สถานศึกษา</label>
                    <select class="form-select" id="school_code" name="school_code" required>
                        <option value="">เลือก สกร.ระดับอำเภอ</option>
                        <option value="all" <?php echo ($selected_school_code == 'all') ? 'selected' : ''; ?>>แสดงทั้งหมด</option>
                        <?php while ($row = mysqli_fetch_assoc($result_schools)): ?>
                            <option value="<?php echo $row['school_code']; ?>" <?php echo ($selected_school_code == $row['school_code']) ? 'selected' : ''; ?>>
                                <?php echo $row['province']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Submit</button>
            </form>
        </div>

        <?php if ($selected_school_code): ?>
            <?php if ($selected_school_code == 'all'): ?>
                <div class="mt-5">
                    <h2 class="text-center">ข้อมูลทั้งหมด</h2>
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>สถานศึกษา</th>
                                <th>งบประมาณที่ได้รับจัดสรร</th>
                                <th>ชั้นเรียน 31 ชม.ขึ้นไป</th>
                                <th>1 อำเภอ 1 อาชีพ</th>
                                <th>เอกสารชุดเบิกที่ส่งแล้ว</th>
                                <th>เหลืองบประมาณที่จัดสรร</th>
                                <th>คิดเป็น %</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row_all = mysqli_fetch_assoc($result_all)): ?>
                                <?php
                                    $province = $row_all['province'];
                                    $budget_amount = $row_all['budget'];

                                    // ตรวจสอบว่าจังหวัดเป็นนครศรีธรรมราชหรือไม่
                                    if ($row_all['school_code'] == '1280200000') {
                                        // ดึงข้อมูลสำหรับ "ชั้นเรียน 31 ชม.ขึ้นไป"
                                        $query_class_31hrs = "SELECT SUM(budget) AS class_31hrs_budget FROM form_submissions WHERE book_type = 'ชั้นเรียน 31 ชม.ขึ้นไป'";
                                        $result_class_31hrs = mysqli_query($conn, $query_class_31hrs);
                                        $row_class_31hrs = mysqli_fetch_assoc($result_class_31hrs);
                                        $class_31hrs_budget = $row_class_31hrs['class_31hrs_budget'];

                                        // ดึงข้อมูลสำหรับ "1 อำเภอ 1 อาชีพ"
                                        $query_district_occupation = "SELECT SUM(budget) AS district_occupation_budget FROM form_submissions WHERE book_type = '1 อำเภอ 1 อาชีพ'";
                                        $result_district_occupation = mysqli_query($conn, $query_district_occupation);
                                        $row_district_occupation = mysqli_fetch_assoc($result_district_occupation);
                                        $district_occupation_budget = $row_district_occupation['district_occupation_budget'];

                                        // ดึงข้อมูลจาก form_submissions
                                        $query_submissions = "SELECT SUM(budget) AS total_budget FROM form_submissions";
                                        $result_submissions = mysqli_query($conn, $query_submissions);
                                        $row_submissions = mysqli_fetch_assoc($result_submissions);
                                        $total_budget = $row_submissions['total_budget'];
                                    } else {
                                        // ดึงข้อมูลสำหรับ "ชั้นเรียน 31 ชม.ขึ้นไป"
                                        $query_class_31hrs = "SELECT SUM(budget) AS class_31hrs_budget FROM form_submissions WHERE district = '{$row_all['school_code']}' AND book_type = 'ชั้นเรียน 31 ชม.ขึ้นไป'";
                                        $result_class_31hrs = mysqli_query($conn, $query_class_31hrs);
                                        $row_class_31hrs = mysqli_fetch_assoc($result_class_31hrs);
                                        $class_31hrs_budget = $row_class_31hrs['class_31hrs_budget'];

                                        // ดึงข้อมูลสำหรับ "1 อำเภอ 1 อาชีพ"
                                        $query_district_occupation = "SELECT SUM(budget) AS district_occupation_budget FROM form_submissions WHERE district = '{$row_all['school_code']}' AND book_type = '1 อำเภอ 1 อาชีพ'";
                                        $result_district_occupation = mysqli_query($conn, $query_district_occupation);
                                        $row_district_occupation = mysqli_fetch_assoc($result_district_occupation);
                                        $district_occupation_budget = $row_district_occupation['district_occupation_budget'];

                                        // ดึงข้อมูลจาก form_submissions
                                        $query_submissions = "SELECT SUM(budget) AS total_budget FROM form_submissions WHERE district = '{$row_all['school_code']}'";
                                        $result_submissions = mysqli_query($conn, $query_submissions);
                                        $row_submissions = mysqli_fetch_assoc($result_submissions);
                                        $total_budget = $row_submissions['total_budget'];
                                    }

                                    $remaining_budget = $budget_amount - $total_budget;
                                    $percentage = ($total_budget / $budget_amount) * 100;
                                ?>
                                <tr>
                                    <td><?php echo $province; ?></td>
                                    <td><?php echo number_format($budget_amount); ?> บาท</td>
                                    <td><?php echo number_format($class_31hrs_budget); ?> บาท</td>
                                    <td><?php echo number_format($district_occupation_budget); ?> บาท</td>
                                    <td><?php echo number_format($total_budget); ?> บาท</td>
                                    <td><?php echo number_format($remaining_budget); ?> บาท</td>
                                    <td><?php echo number_format($percentage, 2); ?>%</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="row mt-5 justify-content-center">
                    <div class="col-md-6">
                        <div class="card shadow-sm text-center">
                            <h4><?php echo $province; ?></h4>
                            <h4>งบประมาณที่ได้รับจัดสรร: <?php echo number_format($budget_amount); ?> บาท</h4>
                            <h4>ชั้นเรียน 31 ชม.ขึ้นไป: <?php echo number_format($class_31hrs_budget); ?> บาท</h4>
                            <h4>1 อำเภอ 1 อาชีพ: <?php echo number_format($district_occupation_budget); ?> บาท</h4>
                            <h4>เอกสารชุดเบิกที่ส่งแล้ว: <?php echo number_format($total_budget); ?> บาท</h4>
                            <h4>เหลืองบประมาณที่จัดสรร: <?php echo number_format($remaining_budget); ?> บาท</h4>
                            <h4>คิดเป็น: <?php echo number_format($percentage, 2); ?>%</h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="chart-container">
                            <canvas id="budgetChart"></canvas>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <?php if ($selected_school_code && $selected_school_code != 'all'): ?>
        <script>
            var ctx = document.getElementById('budgetChart').getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['เหลืองบประมาณที่จัดสรร', 'เอกสารชุดเบิกที่ส่งแล้ว', 'ชั้นเรียน 31 ชม.ขึ้นไป', '1 อำเภอ 1 อาชีพ'],
                    datasets: [{
                        label: 'Budget Allocation',
                        data: [<?php echo $remaining_budget; ?>, <?php echo $total_budget; ?>, <?php echo $class_31hrs_budget; ?>, <?php echo $district_occupation_budget; ?>],
                        backgroundColor: [
                            'rgba(75, 192, 192, 0.6)',
                            'rgba(255, 99, 132, 0.6)',
                            'rgba(54, 162, 235, 0.6)',
                            'rgba(153, 102, 255, 0.6)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.raw !== null) {
                                        label += new Intl.NumberFormat('en-US', {
                                            style: 'currency',
                                            currency: 'THB'
                                        }).format(context.raw);
                                    }
                                    let percentage = ((context.raw / <?php echo $budget_amount; ?>) * 100).toFixed(2);
                                    label += ` (${percentage}%)`;
                                    return label;
                                }
                            }
                        }
                    }
                }
            });
        </script>
    <?php endif; ?>
</body>
</html>
