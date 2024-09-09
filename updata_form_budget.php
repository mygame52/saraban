<?php
require_once('connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $budgets = $_POST['budget'];

    foreach ($budgets as $province => $budget) {
        $sql = "UPDATE budget34_2567 SET budget='$budget' WHERE province='$province'";
        if (!$conn->query($sql)) {
            echo "Error updating record: " . $conn->error;
        }
    }
    $conn->close();
    header("Location: form_budget.php?success=1");
    exit();
}
?>
