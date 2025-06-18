<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $name = $_POST['courseName'];
    $fee = $_POST['fee'];
    $duration = $_POST['duration'];

    if (!empty($id) && !empty($name) && !empty($fee) && !empty($duration)) {
        $sql = "UPDATE coursedata SET Name = ?, Fees = ?, Duration = ? WHERE Id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $response = ["message" => "Error preparing statement: " . $conn->error];
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("siii", $name, $fee, $duration, $id);

        if ($stmt->execute()) {
            $response = [
                "Id" => $id,
                "name" => $name,
                "fee" => $fee,
                "duration" => $duration,
                "message" => "Course updated successfully!"
            ];
            echo json_encode($response);
        } else {
            $response = ["message" => "Error updating course: " . $stmt->error];
            echo json_encode($response);
        }

        $stmt->close();
    } else {
        $response = ["message" => "All fields are required!"];
        echo json_encode($response);
    }
}
?>
