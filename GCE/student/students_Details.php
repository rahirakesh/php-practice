<?php
include '../Includes/dbcon.php';
include '../Includes/session.php';

$classCode = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students Details</title>
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
                <div class="table-container">
                    <div class="title" id="addClass">
                        <h2 class="section--title">Students</h2>
                        <a href="add_student.php?id=<?php echo $classCode; ?>" class="add"><i class="ri-add-line"></i>Add Student</a>
                    </div>
                    <div class="table">
                        <table id="StudentTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Students Name</th>                                    
                                    <th>Fathers Name</th>
                                    <th>Contact</th>
                                    <th>E-Mail</th>
                                    <th>Settings</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT ID, studentName, fathersName, contact, email FROM students WHERE course = $classCode";
                                $result = $conn->query($sql);
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr data-id='" . $row['ID'] . "'>";
                                        echo "<td>" . $row["ID"] . "</td>";
                                        echo "<td>" . $row["studentName"] . "</td>";                                        
                                        echo "<td>" . $row["fathersName"] . "</td>";
                                        echo "<td>" . $row["contact"] . "</td>";
                                        echo "<td>" . $row["email"] . "</td>";
                                        echo "<td>
                                                <i class='ri-edit-line edit'></i>
                                                <i class='ri-delete-bin-line delete'></i>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='8'>No records found</td></tr>";
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
            $('.delete').click(function() {
                var row = $(this).closest('tr');
                var studentId = row.data('id');
                
                if (confirm('Are you sure you want to delete this student?')) {
                    $.ajax({
                        url: 'delete_student.php',
                        type: 'POST',
                        data: { id: studentId },
                        dataType: 'json',
                        success: function(response) {
                            if (response.status === 'success') {
                                row.remove();
                                alert('Student deleted successfully.');
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('AJAX error: ' + status + ' - ' + error);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
