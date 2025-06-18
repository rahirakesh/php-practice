<?php
// Include database connection and session management
include '../Includes/dbcon.php';
include '../Includes/session.php';

// Initialize variables
$classCode = isset($_GET['id']) ? intval($_GET['id']) : 0;
$errors = [];

// Check if form is submitted via POST method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $student_name = mysqli_real_escape_string($conn, $_POST['student_name']);
    $dob = $_POST['dob'];
    $fathersName = mysqli_real_escape_string($conn, $_POST['fathers_name']);
    $motherName = mysqli_real_escape_string($conn, $_POST['mothers_name']);
    $contact = $_POST['contact'];
    $email = $_POST['mail'];
    $cast = mysqli_real_escape_string($conn, $_POST['cast']);
    $category = mysqli_real_escape_string($conn, $_POST['grade']);
    $university = mysqli_real_escape_string($conn, $_POST['university']);
    $adharNumber = $_POST['aadharNumber'];
    $enrolmentDate = $_POST['enrolmentDate'];
    $completedDate = $_POST['completedDate'];
    $course = $classCode;

    // Check for unique contact, email, and aadhar
    $checkUniqueQuery = "SELECT id FROM students WHERE contact = ? OR email = ? OR adharNumber = ?";
    $stmtUnique = $conn->prepare($checkUniqueQuery);
    $stmtUnique->bind_param("sss", $contact, $email, $adharNumber);
    $stmtUnique->execute();
    $stmtUnique->store_result();

    if ($stmtUnique->num_rows > 0) {
        $errors[] = "A student with this contact, email, or Aadhar number already exists.";
    } else {
        // Prepare SQL statement to insert student details (excluding files)
        $sql = "INSERT INTO students (studentName, dob, fathersName, motherName, contact, email, cast, category, university, adharNumber, enrolmentDate, completedDate, course)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssssssssss", $student_name, $dob, $fathersName, $motherName, $contact, $email, $cast, $category, $university, $adharNumber, $enrolmentDate, $completedDate, $course);

        // Execute SQL statement
        if ($stmt->execute()) {
            // Get the newly assigned student ID
            $studentId = $stmt->insert_id;

            // Create target directory if it does not exist
            $targetDir = "uploads/$classCode/$studentId/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            // File upload handling
            $photo = $_FILES['student_image']['name'];
            $adhar = $_FILES['aadhar_image']['name'];
            $tc = $_FILES['tc']['name'];
            $cc = $_FILES['cc']['name'];

            // Rename files based on student ID
            $photoName = "student_image_" . $studentId . "." . pathinfo($photo, PATHINFO_EXTENSION);
            $adharName = "aadhar_image_" . $studentId . "." . pathinfo($adhar, PATHINFO_EXTENSION);
            $tcName = "tc_" . $studentId . "." . pathinfo($tc, PATHINFO_EXTENSION);
            $ccName = "cc_" . $studentId . "." . pathinfo($cc, PATHINFO_EXTENSION);

            // Move uploaded files to target directory
            move_uploaded_file($_FILES['student_image']['tmp_name'], $targetDir . $photoName);
            move_uploaded_file($_FILES['aadhar_image']['tmp_name'], $targetDir . $adharName);
            move_uploaded_file($_FILES['tc']['tmp_name'], $targetDir . $tcName);
            move_uploaded_file($_FILES['cc']['tmp_name'], $targetDir . $ccName);

            // Update student record with file names
            $sqlUpdate = "UPDATE students SET photo=?, adhar=?, TC=?, CC=? WHERE id=?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("ssssi", $photoName, $adharName, $tcName, $ccName, $studentId);

            if ($stmtUpdate->execute()) {
                echo "<script>alert('New student added successfully');window.location.href='add_student.php?id=$classCode';</script>";
            } else {
                $errors[] = "Error updating student record: " . $stmtUpdate->error;
            }

            $stmtUpdate->close();
        } else {
            $errors[] = "Error: " . $stmt->error;
        }
    }

    // Display errors if any
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<script>alert('$error');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <style>
        .form-label {
            color: #007bff;
            font-weight: bold;
        }
        .form-control {
            border-color: #007bff;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .image-show img {
            width: 68%;
            height: auto;
            margin-left: 40px;
        }
        .image-upload {
            text-align: center;
            margin-left: 30px;
            margin-top: 190px;
        }
        .btn-primary {
            float: right;
            margin-top: 10px;
        }  
    </style>
</head>
<body>
    <?php include 'includes/topbar.php'; ?>
    <section class="main">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main--content">
            <div class="formDiv--">
                <div class="container mt-5">
                    <form method="post" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="student_name" class="form-label">Student Name:</label>
                                    <input type="text" id="student_name" name="student_name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="dob" class="form-label">Date of Birth:</label>
                                    <input type="date" id="dob" name="dob" class="form-control" required>
                                </div>
                                <div class="form-row mb-3">
                                    <div class="col">
                                        <label for="contact" class="form-label">Contact:</label>
                                        <input type="text" id="contact" name="contact" class="form-control" required>
                                    </div>
                                    <div class="col">
                                        <label for="mail" class="form-label">e-Mail:</label>
                                        <input type="email" id="mail" name="mail" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="image-show mb-3">
                                    <img src="img/i.png" alt="Student Image" height="50px" class="img-fluid">
                                </div>

                                    <div class="image-upload mb-3">
                                        <label for="student_image" class="form-label">Student Image:</label>
                                        <input type="file" accept="image/*" id="student_image" name="student_image" class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="fathers_name" class="form-label">Father's Name:</label>
                                    <input type="text" id="fathers_name" name="fathers_name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="cast" class="form-label">Caste:</label>
                                    <input type="text" id="cast" name="cast" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="aadharNumber" class="form-label">Aadhar Number:</label>
                                    <input type="text" id="aadharNumber" name="aadharNumber" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="tc" class="form-label">TC Image:</label>
                                    <input type="file" accept="image/*" id="tc" name="tc" class="form-control">
                                    <small>No file chosen</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="mothers_name" class="form-label">Mother's Name:</label>
                                    <input type="text" id="mothers_name" name="mothers_name" class="form-control" required>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="grade" class="form-label mt-4 mt-20">Select Category:</label>
                                    <select id="grade" name="grade" class="form-select" required>
                                        <option value="">Select Grade</option>
                                        <option value="OBC">OBC</option>
                                        <option value="SC">SC</option>
                                        <option value="ST">ST</option>
                                        <option value="General">General</option>
                                    </select>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="aadhar_image" class="form-label">Aadhar Image:</label>
                                    <input type="file" accept="image/*" id="aadhar_image" name="aadhar_image" class="form-control">
                                    <small>No file chosen</small>
                                </div>
                                <div class="form-group mb-3">
                                    <label for="cc" class="form-label">CC Image:</label>
                                    <input type="file" accept="image/*" id="cc" name="cc" class="form-control">
                                    <small>No file chosen</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address" class="form-label">Address:</label>
                            <textarea id="address" name="address" rows="4" class="form-control" required></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="university" class="form-label">University:</label>
                            <input type="text" id="university" name="university" class="form-control" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="enrolmentDate" class="form-label">Enrolment Date:</label>
                                    <input type="date" id="enrolmentDate" name="enrolmentDate" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="completedDate" class="form-label">Completed Date:</label>
                                    <input type="date" id="completedDate" name="completedDate" class="form-control">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>
   
</body>
</html>
