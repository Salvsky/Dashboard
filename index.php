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

                    <br>

                        <div class="hr-bar-graph">
                            <div class="hr-bar-graph">
                            <div class="progress-bars">
                                <span class="label">Open</span>

                                <div class="prog-container">
                                <div class="progress-bar open"></div>
                                </div>

                                <span class="value"><?php echo $openCount; ?> / <?php echo $totalCount;?></span>
                            </div>

                            <div class="progress-bars G">
                                <span class="label">On-going</span>

                                <div class="prog-container">
                                <div class="progress-bar onGoing"></div>
                                </div>
                                <span class="value"><?php echo $onGoingCount; ?> / <?php echo $totalCount;?></span>
                            </div>

                            <div class="progress-bars C">
                                <span class="label">Closed</span>

                                <div class="prog-container">
                                <div class="progress-bar closed"></div>
                                </div>
                                <span class="value"><?php echo $closedCount; ?> / <?php echo $totalCount;?></span>
                            </div>
                        </div>
                    
                    </div>
                </div>
            </div>

            <div class="priority-container">
                <div class="priority-ticket">
                    <div class="head">
                        <div>
                        <h3>Priority Tickets</h3>
                        </div>
                        <div>
                        <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                    </div>
                    <hr>
                    <br>
                    <div class="display-tickets">
                        <!-- =========== Mula Dito ============== -->

                        
                        <!-- =========== Gang Mula Dito ============== -->
                                

                    </div>
                </div>
            </div>

            

        </main>
    
    <!-- ================ END OF MAIN CONTENT ================== -->
</section>


    <!-- ============== END OF CONTENT ================= -->

    <script src="assets/js/main.js"></script>

    <script>
        var totalCount = <?php echo $totalCount; ?>;
        var openCount = <?php echo $openCount; ?>;
        var onGoingCount = <?php echo $onGoingCount; ?>;
        var closedCount = <?php echo $closedCount; ?>;

        var openProgressBar = document.querySelector('.progress-bar.open');
        var onGoingProgressBar = document.querySelector('.progress-bar.onGoing');
        var closedProgressBar = document.querySelector('.progress-bar.closed');

        openProgressBar.style.width = (openCount / totalCount * 100) + '%';
        onGoingProgressBar.style.width = (onGoingCount / totalCount * 100) + '%';
        closedProgressBar.style.width = (closedCount / totalCount * 100) + '%';
        
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

        var ctx = document.getElementById('pieChart').getContext('2d');

        var myPieChart = new Chart(ctx, {
            type: 'pie',
            data: pieData,
            options: {
            }
        });
        
    </script>
    
</body>
</html>