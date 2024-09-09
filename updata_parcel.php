<?php
require_once('connection.php');

if (isset($_POST['reference_number']) && isset($_POST['action'])) {
    $referenceNumber = $_POST['reference_number'];
    $action = $_POST['action'];
    $currentDate = date('Y-m-d');

    switch ($action) {
        case 'receive':
            updateDocumentStatus($conn, $referenceNumber, 'รับแล้ว', $currentDate);
            break;

        case 'edit':
            updateDocumentStatus($conn, $referenceNumber, 'แก้ไขงานพัสดุ', $currentDate);
            break;

                default:
            echo "Invalid action";
    }
} else {
    echo "Invalid request";
}

$conn->close();

function updateDocumentStatus($conn, $referenceNumber, $status, $date) {
    $statusValue = ($status === 'รับแล้ว') ? 3 : 4; // กำหนดค่า statusValue เป็น 3 หรือ 4 ตามสถานะ

    $sql = "UPDATE form_submissions SET parcel_status = '$status', parcel_data = '$date' WHERE reference_number = '$referenceNumber'";
    if ($conn->query($sql) === TRUE) {
        // อัปเดตค่า status ให้เป็น 1 หรือ 2 ตามสถานะ
        $sql_status = "UPDATE form_submissions SET status = '$statusValue' WHERE reference_number = '$referenceNumber'";
        if ($conn->query($sql_status) === TRUE) {
            echo "success";
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        echo "error: " . $conn->error;
    }
}

?>
