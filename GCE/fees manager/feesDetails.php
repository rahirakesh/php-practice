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
    <title>Genuise Computers</title>
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
                        <h2 class="section--title">Student's Fee Details</h2>
                        <a href="add_student.php?id=<?php echo $classCode; ?>" class="add"><i class="ri-add-line"></i>Add Student</a>
                    </div>
                    <div class="table">
                        <table id="StudentTable">
                            <thead>
                                <tr>
                                    <th>Students Name</th>
                                    <th>Admission Fee</th>
                                    <th>1'st</th>
                                    <th>2'nd</th>
                                    <th>3'rd</th>
                                    <th>4'th</th>
                                    <th>5'th</th>
                                    <th>Total</th>
                                    <th>Remain</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                                $sql = "SELECT
                                    s.ID as studentId,
                                    s.studentName,
                                    COALESCE(fc.admissionFee, 0) as admissionFee,
                                    COALESCE(fc.instalment1, 0) as instalment1,
                                    COALESCE(fc.instalment2, 0) as instalment2,
                                    COALESCE(fc.instalment3, 0) as instalment3,
                                    COALESCE(fc.instalment4, 0) as instalment4,
                                    COALESCE(fc.instalment5, 0) as instalment5,
                                    (
                                        COALESCE(fc.admissionFee, 0) +
                                        COALESCE(fc.instalment1, 0) +
                                        COALESCE(fc.instalment2, 0) +
                                        COALESCE(fc.instalment3, 0) +
                                        COALESCE(fc.instalment4, 0) +
                                        COALESCE(fc.instalment5, 0)
                                    ) as Total,
                                    (
                                        cd.Fees -
                                        (
                                            COALESCE(fc.admissionFee, 0) +
                                            COALESCE(fc.instalment1, 0) +
                                            COALESCE(fc.instalment2, 0) +
                                            COALESCE(fc.instalment3, 0) +
                                            COALESCE(fc.instalment4, 0) +
                                            COALESCE(fc.instalment5, 0)
                                        )
                                    ) as Remain,
                                    cd.Fees as courseFees
                                FROM
                                    students s
                                LEFT JOIN
                                    feescollection fc ON s.ID = fc.studentId
                                JOIN
                                    coursedata cd ON s.course = cd.Id
                                WHERE
                                    s.course = $classCode";

                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $total = $row["admissionFee"] + $row["instalment1"] + $row["instalment2"] + $row["instalment3"] + $row["instalment4"] + $row["instalment5"];
                                        $remaining = $row["courseFees"] - $total;
                                        $rowClass = $remaining == 0 ? 'green' : ($remaining < 0 ? 'blue' : 'orange');

                                        echo "<tr data-id='" . $row['studentId'] . "'>";
                                        echo "<td>" . $row["studentName"] . "</td>";
                                        echo "<td class='editable' data-field='admissionFee' contenteditable='true'>" . $row["admissionFee"] . "</td>";
                                        echo "<td class='editable' data-field='instalment1' contenteditable='true'>" . $row["instalment1"] . "</td>";
                                        echo "<td class='editable' data-field='instalment2' contenteditable='true'>" . $row["instalment2"] . "</td>";
                                        echo "<td class='editable' data-field='instalment3' contenteditable='true'>" . $row["instalment3"] . "</td>";
                                        echo "<td class='editable' data-field='instalment4' contenteditable='true'>" . $row["instalment4"] . "</td>";
                                        echo "<td class='editable' data-field='instalment5' contenteditable='true'>" . $row["instalment5"] . "</td>";
                                        echo "<td class='total'>" . $total . "</td>";
                                        echo "<td class='remaining " . $rowClass . "'>" . $remaining . "</td>";
                                        echo "<td class='courseFees' style='display:none;'>" . $row["courseFees"] . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='9'>No records found</td></tr>";
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
        $('.editable').on('focusout', function() {
            var studentId = $(this).closest('tr').data('id');
            var field = $(this).data('field');
            var value = $(this).text();

            $.ajax({
                url: 'update_fee.php',
                method: 'POST',
                data: {
                    studentId: studentId,
                    field: field,
                    value: value
                },
                success: function(response) {
                    alert(response);

                    var total = 0;
                    var row = $('tr[data-id="' + studentId + '"]');
                    row.find('.editable').each(function() {
                        total += parseFloat($(this).text()) || 0;
                    });

                    var courseFees = parseFloat(row.find('.courseFees').text());
                    var remaining = courseFees - total;

                    row.find('.total').text(total);
                    row.find('.remaining').text(remaining);

                    if (remaining == 0) {
                        row.find('.remaining').removeClass('orange blue').addClass('green');
                    } else if (remaining < 0) {
                        row.find('.remaining').removeClass('green orange').addClass('blue');
                    } else {
                        row.find('.remaining').removeClass('green blue').addClass('orange');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Failed to update data: ' + error);
                }
            });
        });
    });
    </script>

    <style>
        .green {
            background-color: #ccfff5;
        }
        .blue {
            background-color: #ccddff;
        }
        .orange {
            background-color: #ffe6e6;
        }
    </style>
</body>
</html>
