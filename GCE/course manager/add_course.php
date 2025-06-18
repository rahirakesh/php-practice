<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['courseName'];
    $fee = $_POST['fee'];
    $duration = $_POST['duration'];

    if (!empty($name) && !empty($fee) && !empty($duration)) {
        $sql = "INSERT INTO coursedata (Name, Fees, Duration) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            $response = ["message" => "Error preparing statement: " . $conn->error];
            echo json_encode($response);
            exit;
        }

        $stmt->bind_param("sii", $name, $fee, $duration);

        if ($stmt->execute()) {
            $newCourseId = $stmt->insert_id;

            // Create a directory inside ../uploads named after the new course ID
            $uploadDir = "uploads/" . $newCourseId;
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $response = [
                "Id" => $newCourseId,
                "name" => $name,
                "fee" => $fee,
                "duration" => $duration,
                "message" => "Course added successfully!"
            ];
            echo json_encode($response);
        } else {
            $response = ["message" => "Error adding course: " . $stmt->error];
            echo json_encode($response);
        }

        $stmt->close();
    } else {
        $response = ["message" => "All fields are required!"];
        echo json_encode($response);
    }
}
?>
