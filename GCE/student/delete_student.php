<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentId = intval($_POST['id']);
    
    // Delete student record
    $sql = "DELETE FROM students WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $studentId);

    if ($stmt->execute()) {
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Failed to delete student."]);
    }

    $stmt->close();
}
$conn->close();
?>
