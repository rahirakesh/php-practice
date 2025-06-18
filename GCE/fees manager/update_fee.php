<?php
include '../Includes/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $studentId = intval($_POST['studentId']);
    $field = $_POST['field'];
    $value = floatval($_POST['value']);

    // Validate input data
    if (empty($studentId) || empty($field) || !isset($value)) {
        echo "Invalid input data.";
        exit;
    }

    // Sanitize the field to prevent SQL injection
    $allowed_fields = ['admissionFee', 'instalment1', 'instalment2', 'instalment3', 'instalment4', 'instalment5'];
    if (!in_array($field, $allowed_fields)) {
        echo "Invalid field.";
        exit;
    }

    // Check if the record exists
    $check_sql = "SELECT * FROM feescollection WHERE studentId = $studentId";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update the existing record
        $update_sql = "UPDATE feescollection SET $field = ? WHERE studentId = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("di", $value, $studentId);
    } else {
        // Insert a new record
        $insert_sql = "INSERT INTO feescollection (studentId, $field) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        $stmt->bind_param("id", $studentId, $value);
    }

    if ($stmt->execute()) {
        echo "Data updated successfully";
    } else {
        echo "Failed to update data: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
