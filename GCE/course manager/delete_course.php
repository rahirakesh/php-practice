<?php
include '../Includes/dbcon.php';

if (isset($_POST['deleteId'])) {
    $id = $_POST['deleteId'];

    $sql = "DELETE FROM coursedata WHERE Id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "Course deleted successfully.";
    } else {
        echo "Error: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
}
?>
