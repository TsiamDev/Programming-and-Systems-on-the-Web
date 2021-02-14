<?php

	require_once 'config/db.php';

	//Query - duh!
	$sql = "SELECT COUNT(cache_private), COUNT(cache_public), COUNT(cache_no_cache), COUNT(cache_no_store) FROM files ";

	$stmt = $conn->prepare($sql);
	$stmt->execute();
	// "out variables"
	$stmt->bind_result($private, $public, $no_cache, $no_store);

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
	$public = ($public / $sum) * 100;
	$private = ($private / $sum) * 100;
	$no_cache = ($no_cache / $sum) * 100;
	$no_store = ($no_store / $sum) * 100;

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
            labels: ['public', 'private','no-store','no-cache'],
            datasets: [{
                label: 'Cachability Directives',
                backgroundColor: ['#49e2ff','##ff7d49','#3dd12a','#c9d12a'],
                data: [<?php echo $public ?>, <?php echo $private ?>, <?php echo $no_store ?>, <?php echo $no_cache ?>]
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