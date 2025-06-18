<?php 
include '../Includes/dbcon.php';
include '../Includes/session.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link href="img/logo/jce.jpg" rel="icon">
  <title>GenuiseComputer</title>
  <link rel="stylesheet" href="css/styles.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/remixicon/4.2.0/remixicon.css" rel="stylesheet">
  <style>   

    .cards {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        justify-content: center;
    }

    .card {
        position: relative;
        width: 80%;
        max-width: 50px;
        overflow: hidden;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
        background-color: #fff;
        text-align: center;
    }

    .card:hover {
        transform: scale(1.05);
    }

    .card--link {
        display: block;
        text-decoration: none;
        color: inherit; /* Ensure the text color is inherited */
        height: 100%;
    }

    .card--data {
        padding: 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .card--title {
        font-size: 18px;
        margin-bottom: 5px;
    }

    .card--icon--lg {
        font-size: 48px;
        margin-top: 20px;
        color: #555; /* Adjust icon color as needed */
    }

</style>

  </style>
</head>
<body>
<?php include 'includes/topbar.php';?>
<section class="main">
    <?php include 'includes/sidebar.php';?>
    <div class="main--content">
        <div class="overview">
            <div class="title">
                <h2 class="section--title">Select Course</h2>
            </div>
            <div class="cards">
                <?php
                $sql = "SELECT Id, Name FROM coursedata;";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <div class="card card-1">
                            <a href="feesDetails.php?id=<?php echo $row['Id']; ?>" class="card--link">
                                <div class="card--data">
                                    <div class="card--content">
                                        <h5 class="card--title"><?php echo htmlspecialchars($row['Name']); ?></h5>
                                    </div>
                                    <i class="ri-book-read-line card--icon--lg"></i>
                                </div> 
                            </a>
                        </div>
                        <?php
                    }
                } else {
                    echo "<p>No classes found</p>";
                }
                ?>
            </div>
        </div>
    </div>
</section>

<script src="javascript/main.js"></script>
<?php include 'includes/footer.php';?>  
</body>
</html>
