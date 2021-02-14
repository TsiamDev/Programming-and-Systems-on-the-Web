<?php

	require_once 'config/db.php';

	//Query - duh!
	$sql = "SELECT COUNT(max_stale), COUNT(min_fresh), COUNT(public), COUNT(private), COUNT(no_cache), COUNT(no_store) FROM files ";

	$stmt = $conn->prepare($sql);
	$stmt->execute();
	// "out variables"
	$stmt->bind_result($max_stale, $min_fresh, $public, $private, $no_cache, $no_store);

	// fetch returns:
	// TRUE -> on success
	// FALSE -> on error
	// NULL -> no rows/data or truncation happened
	if ( $stmt->fetch() != TRUE )
	{
		die("error while fetching data");
	}
	
	//If you got here, data was fetched
	//so, use it
	
	//Compute sum & percentages
	$sum = $public + $private + $no_cache + $no_store;
	$max_stale = ($max_stale / $sum) * 100;
	$min_fresh = ($min_fresh / $sum) * 100;
	//terminate connection!
	mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
<title>Headers Analysis</title>
	<script src="..\front_end\dependencies\Chartjs\Chartjs.js"></script>
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
            labels: ['max-stale', 'min-fresh'],
            datasets: [{
                label: 'Cachability Directives',
                backgroundColor: ['#49e2ff','##ff7d49'],
                data: [<?php echo $max_stale ?>, <?php echo $min_fresh ?>]
            }]
        },
        options: {

            title: {
                display: true,
                text: 'Ποσοστό max-stale/min-fresh.'
             }
        }
        });
    
    </script>

</body>
</html>