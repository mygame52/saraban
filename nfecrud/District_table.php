<?php
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


// ดึงข้อมูลจากตาราง form_submissions ที่มีฟิลด์ district ตรงกับ username ของผู้ใช้ที่ล็อกอิน
$sql = "SELECT * FROM form_submissions WHERE district = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// ดึงข้อมูลจาก budget34_2567 และคำนวณงบประมาณ
$query_budget = "SELECT province, budget FROM budget34_2567 WHERE school_code = ?";
$stmt_budget = $conn->prepare($query_budget);
$stmt_budget->bind_param("s", $username);
$stmt_budget->execute();
$result_budget = $stmt_budget->get_result();
$budget_amount = 0;
$total_budget = 0;
$percentage = 0;
$province = '';
$class_31hrs_budget = 0;
$district_occupation_budget = 0;

if ($row_budget = $result_budget->fetch_assoc()) {
    $budget_amount = $row_budget['budget'];
    $province = $row_budget['province'];

    $query_submissions = "SELECT SUM(budget) AS total_budget FROM form_submissions WHERE district = ?";
    $stmt_submissions = $conn->prepare($query_submissions);
    $stmt_submissions->bind_param("s", $username);
    $stmt_submissions->execute();
    $result_submissions = $stmt_submissions->get_result();
    $row_submissions = $result_submissions->fetch_assoc();
    $total_budget = $row_submissions['total_budget'];

    $percentage = ($total_budget / $budget_amount) * 100;
    $remaining_budget = $budget_amount - $total_budget;

    // คำนวณงบประมาณสำหรับชั้นเรียน 31 ชม.ขึ้นไป
    $query_class_31hrs = "SELECT SUM(budget) AS class_31hrs_budget FROM form_submissions WHERE district = ? AND book_type = 'ชั้นเรียน 31 ชม.ขึ้นไป'";
    $stmt_class_31hrs = $conn->prepare($query_class_31hrs);
    $stmt_class_31hrs->bind_param("s", $username);
    $stmt_class_31hrs->execute();
    $result_class_31hrs = $stmt_class_31hrs->get_result();
    $row_class_31hrs = $result_class_31hrs->fetch_assoc();
    $class_31hrs_budget = $row_class_31hrs['class_31hrs_budget'];

    // คำนวณงบประมาณสำหรับ 1 อำเภอ 1 อาชีพ
    $query_district_occupation = "SELECT SUM(budget) AS district_occupation_budget FROM form_submissions WHERE district = ? AND book_type = '1 อำเภอ 1 อาชีพ'";
    $stmt_district_occupation = $conn->prepare($query_district_occupation);
    $stmt_district_occupation->bind_param("s", $username);
    $stmt_district_occupation->execute();
    $result_district_occupation = $stmt_district_occupation->get_result();
    $row_district_occupation = $result_district_occupation->fetch_assoc();
    $district_occupation_budget = $row_district_occupation['district_occupation_budget'];
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>District Table</title>
    <!-- เพิ่ม CSS ของ Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.8.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        .blinking-icon {
            color: yellow;
            animation: blinking 1.5s infinite;
        }

        @keyframes blinking {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }

        .budget-summary {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .status-btn {
            border: none;
            background: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <h4 class="mb-4 text-center">เอกสารวางเบิกศูนย์ฝึกอาชีพชุมชน <?php echo htmlspecialchars($amphoe_name); ?></h4>

        <table id="formTable" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>ผู้ส่ง</th>
                    <th>สกร.ระดับอำเภอ</th>
                    <th>ประเภทหนังสือ</th>
                    <th>เลขที่หนังสือ</th>
                    <th>ชื่อเรื่อง</th>
                    <th>งบประมาณ</th>
                    <th>ผู้รับ</th>
                    <th>วันที่รับ</th>
                    <th>ID</th>
                    <th>สถานะ</th>
                </tr>
            </thead>

            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['sender']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['district']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_type']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['book_number']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['title']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['budget']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['receiver']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['receive_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['reference_number']) . "</td>";
                        echo "<td>";
                        switch ($row['status']) {
                            case 1:
                                echo "<button class='status-btn continuous'>งานต่อเนื่อง</button>";
                                break;
                            case 2:
                                echo "<button class='status-btn continuous-edit'><i class='bi bi-lightbulb blinking-icon'></i> แก้ไขงานต่อเนื่อง</button>";
                                break;
                            case 3:
                                echo "<button class='status-btn logistics'>งานพัสดุ</button>";
                                break;
                            case 4:
                                echo "<button class='status-btn logistics-edit'><i class='bi bi-lightbulb blinking-icon'></i> แก้ไขงานพัสดุ</button>";
                                break;
                            case 5:
                                echo "<button class='status-btn finance'>งานการเงิน</button>";
                                break;
                            case 6:
                                echo "<button class='status-btn finance-edit'><i class='bi bi-lightbulb blinking-icon'></i> แก้ไขงานการเงิน</button>";
                                break;
                            default:
                                echo "<button class='status-btn'>ไม่ระบุ</button>";
                                break;
                        }
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='10' class='text-center'>ไม่มีข้อมูล</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <div class="budget-summary mt-5">
            <h3>สรุปเอกสารชุดเบิกที่ส่งแล้ว</h3>
            <div class="row mt-4">
                <div class="col-md-6">
                    <h4><?php echo $province; ?></h4>
                    <h5>งบประมาณที่รับจัดสรร: <?php echo number_format($budget_amount); ?> บาท</h5>
                    <h5>เอกสารชุดเบิกที่ส่งแล้ว: <?php echo number_format($total_budget); ?> บาท ดังนี้ </h5>
                    <h5>กลุ่มสนใจ: <?php echo number_format($total_budget - $class_31hrs_budget - $district_occupation_budget); ?> บาท</h5>
                    <h5>ชั้นเรียน 31 ชม.ขึ้นไป: <?php echo number_format($class_31hrs_budget); ?> บาท</h5>
                    <h5>1 อำเภอ 1 อาชีพ: <?php echo number_format($district_occupation_budget); ?> บาท</h5>
                    <h5>งบประมาณที่รับจัดสรรคงเหลือ: <?php echo number_format($remaining_budget); ?> บาท</h5>
                    <h5>คิดเป็น: <?php echo number_format($percentage, 2); ?>%</h5>
                </div>
                <div class="col-md-6">
                    <canvas id="budgetChart" width="450" height="450"></canvas> <!-- ลดขนาด canvas ลงอีก -->
                </div>


            </div>
        </div>
    </div>

   

    <!-- เพิ่ม JavaScript ของ Bootstrap 5 และ DataTable -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.0/dist/chart.min.js"></script>

    <!-- เพิ่ม JavaScript สำหรับจัดการ DataTable และแผนภูมิ -->
    <script>
        $(document).ready(function() {
            $('#formTable').DataTable({
                dom: 'lBfrtip',
                buttons: [{
                    extend: 'print',
                    text: 'พิมพ์',
                    title: 'เอกสารลงรับ งานต่อเนื่อง',
                    exportOptions: {
                        columns: ':not(:last-child)'
                    }
                }],
                language: {
                    lengthMenu: "แสดง _MENU_ รายการต่อหน้า",
                    zeroRecords: "ไม่พบข้อมูล",
                    info: "แสดงหน้า _PAGE_ จาก _PAGES_",
                    infoEmpty: "ไม่มีข้อมูล",
                    infoFiltered: "(กรองข้อมูลจากทั้งหมด _MAX_ รายการ)",
                    search: "ค้นหา:",
                    paginate: {
                        first: "หน้าแรก",
                        last: "หน้าสุดท้าย",
                        next: "ถัดไป",
                        previous: "ก่อนหน้า"
                    }
                }
            });

            var ctx = document.getElementById('budgetChart').getContext('2d');
            var budgetData = {
                labels: ['งบประมาณคงเหลือ', 'ชั้นเรียน 31 ชม.ขึ้นไป', '1 อำเภอ 1 อาชีพ', 'กลุ่มสนใจ'],
                datasets: [{
                    label: 'Budget Allocation',
                    data: [
                        <?php echo $remaining_budget; ?>,
                        <?php echo $class_31hrs_budget; ?>,
                        <?php echo $district_occupation_budget; ?>,
                        <?php echo $total_budget - $class_31hrs_budget - $district_occupation_budget; ?>
                    ],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 159, 64, 0.8)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 159, 64, 1)'
                    ],
                    borderWidth: 1.5, // ลดขนาดเส้นขอบของแต่ละส่วน
                    hoverOffset: 8, // ลดการขยายเมื่อ hover
                    hoverBackgroundColor: [
                        'rgba(255, 99, 132, 0.9)',
                        'rgba(54, 162, 235, 0.9)',
                        'rgba(75, 192, 192, 0.9)',
                        'rgba(255, 159, 64, 0.9)'
                    ]
                }]
            };

            var myChart = new Chart(ctx, {
                type: 'doughnut',
                data: budgetData,
                options: {
                    responsive: false, // ปิดการปรับขนาดอัตโนมัติ
        maintainAspectRatio: false, // ปิดการรักษาอัตราส่วน
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                boxWidth: 15, // ขนาดของกล่องสีใน legend
                                padding: 10, // ระยะห่างระหว่างกล่องสีและข้อความ
                                font: {
                                    size: 12 // ขนาดฟอนต์ของ legend
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.7)',
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12 // ลดขนาดฟอนต์ของ tooltip
                            },
                            callbacks: {
                                label: function(tooltipItem) {
                                    var dataset = tooltipItem.dataset;
                                    var total = dataset.data.reduce(function(previousValue, currentValue) {
                                        return previousValue + currentValue;
                                    }, 0);
                                    var currentValue = dataset.data[tooltipItem.dataIndex];
                                    var percentage = Math.floor(((currentValue / total) * 100) + 0.5);
                                    return tooltipItem.label + ': ' + currentValue.toLocaleString() + ' บาท (' + percentage + '%)';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
</body>

</html>