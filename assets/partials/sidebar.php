<?php 
    include('assets/connection/connection.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OSHC</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
</head>
    <?php 
        if(isset($_GET['page-nr'])){
            $id = $_GET['page-nr'];
        }else{
            $id = 1;
        }
    ?>
<body id="<?php echo $id; ?>">

    <!-- =========== START OF SIDEBAR ============ -->
    <section id="sidebar">
        <a href="#" class="brand">
            <div class="left-section">
            <img src="assets/images/oshc_logo_new.png" class="brand-logo">
            </div>
            <span class="text">Occupational Safety and Health Center</span>
        </a>

        <ul class="side-menu top">

            <li class="active">
                <a href="#">
                    <i class="fa-solid fa-gauge"></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-plus"></i>
                    <span class="text">Create Ticket</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-ticket"></i>
                    <span class="text">View Tickets</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fa-solid fa-file-circle-plus"></i>
                    <span class="text">Add Ticket Item</span>
                </a>
            </li>
        </ul>        

        <ul class="side-menu">
            <li>
                <a href="#">
                    <i class="fa-solid fa-user-group"></i>
                    <span class="text">Employees</span>
                </a>
            </li>
            

        </ul>        
    </section>
    <!-- =========== END OF SIDEBAR ============ -->