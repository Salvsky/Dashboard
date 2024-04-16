<?php 
    include('assets/partials/sidebar.php');
?>

    <!-- ============== START OF CONTENT ================= -->
    
<section id="content">
    <?php include ('assets/partials/nav.php'); ?>
    <!-- ================ START OF MAIN CONTENT ================= -->
        <main>
            <?php 
                $openCount = 0;
                $onGoingCount = 0;
                $closedCount = 0;
                $totalCount = 0;
                
                // Query to get total counts from the database
                $queryTotalCounts = "SELECT 
                                        SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) AS OpenCount,
                                        SUM(CASE WHEN status = 'On-going' THEN 1 ELSE 0 END) AS OnGoingCount,
                                        SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS ClosedCount,
                                        COUNT(*) AS TotalCount
                                    FROM tickets";
                
                $resultTotalCounts = mysqli_query($conn, $queryTotalCounts);
                if($resultTotalCounts){
                    $rowTotalCounts = mysqli_fetch_assoc($resultTotalCounts);
                    $openCount = $rowTotalCounts['OpenCount'];
                    $onGoingCount = $rowTotalCounts['OnGoingCount'];
                    $closedCount = $rowTotalCounts['ClosedCount'];
                    $totalCount = $rowTotalCounts['TotalCount'];
                } else {
                    echo "Error: " . mysqli_error($conn);
                }
                
                if(isset($_POST["FORMFilter"])){
                    $start_date = $_POST["FORMStartDate"];
                    $end_date = $_POST["FORMEndDate"];
                    $division = $_POST["FORMDivision"];
                
                    if(empty($start_date) || empty($end_date) || $end_date < $start_date){
                        echo "<script>alert('Invalid date range')</script>";
                    } else {
                        // Query to count tickets by status
                        $queryCount = "SELECT 
                                            SUM(CASE WHEN status = 'Open' THEN 1 ELSE 0 END) AS OpenCount,
                                            SUM(CASE WHEN status = 'On-going' THEN 1 ELSE 0 END) AS OnGoingCount,
                                            SUM(CASE WHEN status = 'Closed' THEN 1 ELSE 0 END) AS ClosedCount,
                                            COUNT(*) AS TotalCount
                                        FROM tickets 
                                        WHERE date_time BETWEEN '$start_date' AND '$end_date' 
                                            AND division = '$division'";
                
                        $result = mysqli_query($conn, $queryCount);
                        if($result){
                            $row = mysqli_fetch_assoc($result);
                            $openCount = $row['OpenCount'];
                            $onGoingCount = $row['OnGoingCount'];
                            $closedCount = $row['ClosedCount'];
                            $totalCount = $row['TotalCount'];

                        } else {
                            echo "Error: " . mysqli_error($conn);
                        }
                        
                    }
                }
            ?>

            <div class="head-title">
                <div class="left">
                    <h1>Ticketing Dashboard</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a href="#">Dashboard </a>
                        </li>
                        <li>
                            <i class="fa-solid fa-chevron-right"></i>
                        </li>

                        <li>
                            <a class="active" href="#">Home</a>
                        </li>
                    </ul>
                </div>
                <a href="#" class="btn-download">
                    <i class="fa-solid fa-cloud-arrow-down"></i>
                    <span class="text">Print Ticket</span>
                </a>
            </div>

            <ul class="box-info">
                <li>
                    <i class="fa-solid fa-folder-open"></i>
                    <span class="text">
                        <h3><?php echo $openCount; ?></h3>
                        <p>OPEN</p>
                    </span>
                </li>
                <li>
                    <i class="fa-solid fa-gears"></i>
                    <span class="text">
                        <h3><?php echo $onGoingCount; ?></h3>
                        <p>ON-GOING</p>
                    </span>
                </li>
                <li>
                    <i class="fa-solid fa-box-archive"></i>
                    <span class="text"> 
                        <h3><?php echo $closedCount; ?></h3>
                        <p>CLOSED</p>
                    </span>
                </li>
                <li>
                    <i class="fa-solid fa-list-check"></i>
                    <span class="text">
                        <h3><?php echo $totalCount; ?></h3>
                        <p>TOTAL TICKET</p>
                    </span>
                </li>
            </ul>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Ticket Overview</h3>
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <hr>
                    <br>
                    <div class="bar-chart">
                        <canvas id="pieChart" class="pieChart"></canvas>
                    </div>
                </div>

                <div class="todo">
                    <div class="head">
                        <h3>Filter Tickets</h3>
                        <i class="fa-solid fa-filter"></i>
                    </div>
                    <hr>
                    <br>
                    <div class="form-filter">
                        <form action="" method="POST">
                            <label for="FORMStartDate">Start Date</label>
                            <input type="date" name="FORMStartDate" id="FORMStartDate">
                            <label for="FORMEndDate">End Date</label>
                            <input type="date" name="FORMEndDate" id="FORMEndDate">
                            <label for="FORMDivision">Division</label>
                            <select name="FORMDivision" id="FORMDivision">
                                <?php 
                                include('connection/connection.php');

                                $querySelect = "SELECT DISTINCT division FROM tickets";
                                $categories = mysqli_query($conn, $querySelect);
                                while($result = mysqli_fetch_array($categories)) { 
                                    echo "<option value='".$result["division"]."'>".$result["division"]."</option>";
                                } 
                                
                                ?>
                            </select>
                            <div class="filter">
                            <button type="submit" name="FORMFilter">Filter</button>
                            </div>
                        </form>
                    </div>
                    <div class="table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Division</th>
                                    <th>Employee Name</th>
                                    <th>Issue Type</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                    $start = 0;
                                    $rows_per_page = 8;

                                    $record = "SELECT * FROM tickets";
                                    $result = $conn->query($record);

                                    $num_rows = $result->num_rows;

                                    $pages = ceil($num_rows / $rows_per_page);

                                    
                                    if(isset($_GET['page-nr'])){
                                        $page = $_GET['page-nr'] - 1;
                                        $start = $page * $rows_per_page;
                                    }


                                    $queryDisplay = "SELECT * FROM tickets LIMIT $start, $rows_per_page";
                                    $sqlDisplay = $conn->query($queryDisplay);

                                    if($sqlDisplay == true) {
                                        $count = mysqli_num_rows($sqlDisplay);

                                        if($count > 0){

                                            if(!isset($_POST['FORMFilter'])){

                                            while($row = mysqli_fetch_assoc($sqlDisplay)) {
                                                $division = $row['division'];
                                                $tixID = $row['ticket_id'];
                                                $name = $row['name'];
                                                $issue = $row['issue_type'];
                                                $status = $row['status'];
                                                $date = $row['date_time'];

                                                ?>
                                                <tr>
                                                    <td><?php echo $tixID;?></td>
                                                    <td><?php echo $division;?></td>
                                                    <td><?php echo $name;?></td>
                                                    <td><?php echo $issue;?></td>
                                                    <td><?php echo $status;?></td>
                                                    <td><?php echo $date;?></td>
                                                </tr>
                                                <?php
                                            }
                                            }else{
                                                $filtered_start_date = $_POST["FORMStartDate"];
                                                $filtered_end_date = $_POST["FORMEndDate"];
                                                $filtered_division = $_POST["FORMDivision"];
                                            
                                                if(empty($filtered_start_date) || empty($filtered_end_date) || $filtered_end_date < $filtered_start_date){
                                                    echo "<script>alert('Invalid date range')</script>";
                                                } else {
                                                    $queryFilter = "SELECT * FROM tickets WHERE date_time BETWEEN '$filtered_start_date' AND '$filtered_end_date' 
                                                    AND division = '$filtered_division'";

                                                    $sqlFilter = $conn->query($queryFilter);
                                                    if($sqlFilter) {
                                                        while($row = mysqli_fetch_assoc($sqlFilter)){

                                                        $filteredTicketId = $row['ticket_id'];
                                                        $filteredDivision = $row['division'];
                                                        $filteredName = $row['name'];
                                                        $filteredIssue = $row['issue_type'];
                                                        $filteredStatus = $row['status'];
                                                        $filteredDate = $row['date_time'];

                                                        ?>
                                                        <tr>
                                                            <td><?php echo $filteredTicketId;?></td>
                                                            <td><?php echo $filteredDivision;?></td>
                                                            <td><?php echo $filteredName;?></td>
                                                            <td><?php echo $filteredIssue;?></td>
                                                            <td><?php echo $filteredStatus;?></td>
                                                            <td><?php echo $filteredDate ;?></td>
                                                        </tr>
                                                        <?php
                                                        }
                                                    }else{
                                                        echo "Error: " . mysqli_error($conn);
                                                    }
                                                }
                                            }
                                        }else{
                                            echo "<td> There is no data to be Displayed </td>";
                                        }
                                        
                                    }else {
                                        echo "<td>Error Displaying Data</td>";
                                    }
    
                                ?>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <hr>
                        <br>
                        
                        <div id="<?php echo $id; ?>" class="page-container">
                        <div class="page-info">

                        <?php 
                            if(!isset($_GET['page-nr'])){
                                $page = 1;
                            }else{
                                $page = $_GET['page-nr'];
                            }
                        ?>
                            Showing <strong><?php echo $page;?></strong> of <?php echo $pages;?> pages
                        </div>

                        <div class="pagination">
                            <a href="?page-nr=1">First</a>

                            <?php 
                                if(isset($_GET['page-nr']) && $_GET['page-nr'] > 1) {
                                    ?>
                                    <a href="?page-nr=<?php echo $_GET['page-nr'] - 1; ?>">Previous</a>
                                <?php
                                }else{
                                    ?>
                                    <a> Previous</a>
                                    <?php
                                }
                            ?>

                            <div class="page-numbers">

                                <?php 
                                    for($counter = 1; $counter <= $pages; $counter++){
                                        ?>
                                            <a href="?page-nr=<?php echo $counter?>"><?php echo $counter; ?></a>
                                        <?php
                                    }
                                ?>
                            </div>

                            <?php
                                if(!isset($_GET['page-nr'])){
                                    ?>
                                        <a href="?page-nr=2">Next</a>
                                    <?php
                                }else{
                                    if($_GET['page-nr'] >= $pages){
                                        ?>
                                        <a>Next</a>
                                        <?php
                                    }else{
                                        ?>
                                        <a href="?page-nr=<?php echo $_GET['page-nr'] + 1; ?>">Next</a>
                                        <?php
                                    }
                                }
                            ?>
                            <!-- <a href="">Next</a> -->
                            <a href="?page-nr=<?php echo $pages;?>">Last</a>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    
    <!-- ================ END OF MAIN CONTENT ================== -->
</section>


    <!-- ============== END OF CONTENT ================= -->

    <script src="assets/js/main.js"></script>

    <script>
        // Prepare data for Pie Chart
        var pieData = {
            labels: ["Open", "On-going", "Closed"],
            datasets: [{
                data: [<?php echo $openCount; ?>, <?php echo $onGoingCount; ?>, <?php echo $closedCount; ?>],
                backgroundColor: [
                    'rgba(0, 128, 0, 1)',
                    'rgba(249, 202, 36, 1)',
                    'rgba(255, 0, 0, 1)'
                ],
                borderColor: [
                    'rgba(0, 128, 0, 1)',
                    'rgba(249, 202, 36, 1)',
                    'rgba(255, 0, 0, 1)',
                ],
                borderWidth: 1
            }]
        };

        // Get canvas element
        var ctx = document.getElementById('pieChart').getContext('2d');

        // Initialize Pie Chart
        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: pieData,
            options: {
                // Add options here if needed
            }
        });

        const links = document.querySelectorAll('.page-numbers > a');
        const bodyId = parseInt(document.body.id) - 1;
        links[bodyId].classList.add("active");

    </script>
    
</body>
</html>