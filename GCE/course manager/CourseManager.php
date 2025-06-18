<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genuise Computer</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    <?php include 'includes/topbar.php'; ?>
    <section class="main">
        <?php include 'includes/sidebar.php'; ?>
        <div class="main--content">
            <div class="overview">
                <div class="formDiv--" id="addClassForm" style="display:none;">
                    <form id="CourseForm">
                        <div style="display:flex; justify-content:space-between;">
                            <div class="form-title">
                                <p id="formTitle"></p>
                            </div>
                            <div>
                                <span class="close">&times;</span>
                            </div>
                        </div>
                        <div>                            
                            <input type="text" id="courseName" name="courseName" placeholder="Course Name" required>                            
                            <input type="text" id="duration" name="duration" placeholder="Duration (In months)" required>
                            <input type="text" id="fee" name="fee" placeholder="Fees" required>                          
                        </div>
                        <button type="submit" id="formSubmitButton" name="addCourse" class="btn-submit">Add Course</button>
                    </form>
                </div>

                <div class="table-container">
                    <div class="title" id="addClass">
                        <h2 class="section--title">Course Details</h2>
                        <button class="add"><i class="ri-add-line"></i>Launch New Course</button>
                    </div>
                    <div class="table">
                        <table id="CourseTable">
                            <thead>
                                <tr>                                   
                                    <th>Course Name</th>
                                    <th>Duration</th>
                                    <th>Fees</th>
                                    <th>Settings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM coursedata";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr data-id='" . $row['Id'] . "'>";
                                        echo "<td>" . $row["Name"] . "</td>";
                                        echo "<td>" . $row["Duration"] . "</td>";
                                        echo "<td>" . $row["Fees"] . "</td>";                                        
                                        echo "<td>
                                                <i class='ri-edit-line edit'></i>
                                                <i class='ri-delete-bin-line delete'></i>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4'>No records found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'includes/footer.php'; ?>

    <script>
       $(document).ready(function() {
    // Handle click event for the "X" button to hide the form
    $(".close").click(function() {
        $("#addClassForm").hide();
        $("#overlay").hide();
        $("body").css("overflow", "auto");
    });

    // Delete Course
    $(document).on("click", ".delete", function() {
        if (confirm("Are you sure you want to delete this Course?")) {
            var row = $(this).closest("tr");
            var id = row.attr("data-id");
            $.ajax({
                url: "delete_course.php",
                method: "POST",
                data: { deleteId: id },
                success: function(response) {
                    row.remove();
                    alert(response); // Display success message
                },
                error: function(xhr, status, error) {
                    alert("An error occurred while deleting the Course.");
                    console.error(error);
                }
            });
        }
    });

    // Add Course
    $(".add").click(function() {
        $("#addClassForm").show();
        $("#overlay").show();
        $("body").css("overflow", "hidden");
        $("#formTitle").text("Add Course");
        $("#formSubmitButton").attr("name", "addCourse").text("Add Course");
        $("#CourseForm")[0].reset();
    });

    // Edit Course
    $(document).on("click", ".edit", function() {
        $("#addClassForm").show();
        $("#overlay").show();
        $("body").css("overflow", "hidden");
        $("#formTitle").text("Edit Course");
        $("#formSubmitButton").attr("name", "updateCourse").text("Update Course");

        var row = $(this).closest("tr");
        var id = row.attr("data-id");
        var name = row.find("td:eq(0)").text();
        var duration = row.find("td:eq(1)").text();
        var fee = row.find("td:eq(2)").text();

        $("#CourseForm").data("id", id); // Store the id in the form
        $("#courseName").val(name);
        $("#duration").val(duration);
        $("#fee").val(fee);
    });

    // Form submission for adding/updating Course
    $("#CourseForm").submit(function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        var submitButtonName = $(this).find("button[type=submit]").attr("name");

        var url = submitButtonName === "addCourse" ? "add_course.php" : "update_course.php";

        if (submitButtonName === "updateCourse") {
            formData += '&id=' + $(this).data("id"); // Append the id for update
        }

        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            dataType: 'json',
            success: function(response) {
                alert(response.message); // Show success message or handle redirection

                if (submitButtonName === "addCourse" && response.message === "Course added successfully!") {
                    var newRow = "<tr data-id='" + response.Id + "'>";
                    newRow += "<td>" + response.name + "</td>";
                    newRow += "<td>" + response.duration + "</td>";
                    newRow += "<td>" + response.fee + "</td>";                   
                    newRow += "<td><i class='ri-edit-line edit'></i><i class='ri-delete-bin-line delete'></i></td>";
                    $("#CourseTable tbody").append(newRow);
                } else if (submitButtonName === "updateCourse" && response.message === "Course updated successfully!") {
                    var row = $("#CourseTable tr[data-id='" + response.Id + "']");
                    row.find("td:eq(0)").text(response.name);
                    row.find("td:eq(1)").text(response.duration);
                    row.find("td:eq(2)").text(response.fee);                    
                }

                $("#addClassForm").hide();
                $("#overlay").hide();
                $("body").css("overflow", "auto");
            },
            error: function(xhr, status, error) {
                alert("An error occurred while processing the form.");
                console.error(error);
            }
        });
    });
});

    </script>
</body>
</html>
