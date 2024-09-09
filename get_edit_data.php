<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reference_number'])) {
    $referenceNumber = $_POST['reference_number'];

    // คำสั่ง SQL เพื่อเลือกแถวที่มีค่า reference_number ที่ตรงกัน โดยเรียงลำดับ id จากมากไปน้อย และจำกัดผลลัพธ์เพียง 1 แถว
    $sql = "SELECT * FROM data_edit WHERE reference_number = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $referenceNumber);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $editData = $result->fetch_assoc();
        echo json_encode($editData);
    } else {
        echo json_encode(['error' => 'ไม่พบข้อมูล']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['error' => 'คำขอไม่ถูกต้อง']);
}
?>
