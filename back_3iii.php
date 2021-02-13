<?php

require_once 'config/db.php';

$sqlQuery1 = "SELECT COUNT(cache_private) FROM files ";
$sqlQuery2 = "SELECT COUNT(cache_public) FROM files ";
$sqlQuery3 = "SELECT COUNT(cache_no_cache) FROM files ";
$sqlQuery4 = "SELECT COUNT(cache_no_store) FROM files ";

$sum = $sqlQuery1 + $sqlQuery2 + $sqlQuery3 +$sqlQuery4 
$public = $sqlQuery2/$sum;
$private = $sqlQuery1/$sum;
$no_cache = $sqlQuery3/$sum;
$no_store = $sqlQuery4/$sum;

mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
<title>Headers Analysis</title>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.css"></script>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css">
</head>
<body>
    <div id="chart-container">
        <canvas id="myPie"></canvas>
    </div>

    <script>
        new Chart(document.getElementById("myPie"),{

            type: 'pie',
            data: {
            labels: ['public', 'private','no-store','no-cache'],
            datasets: [{
                label: 'Cachability Directives',
                backgroundColor: ['#49e2ff','##ff7d49','#3dd12a','#c9d12a'],
                data: [<?$public, $private, $no_store, $no_cache?>]
            }]
        },
        options: {

            title: {
                display: true,
                text: 'Ποσοστό cacheability directives.'
             }
        }
        });
    
    </script>

</body>
</html>