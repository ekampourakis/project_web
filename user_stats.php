<?php

// Resume the previous session
session_start();

// If the user is not logged in redirect to the login page
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit();
}

require_once("functions.php");

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>User Stats</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<!-- Load leaflet.js -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
				integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
				crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
				integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
				crossorigin=""></script>
		<script src="heatmap.js"></script>
		<script src="leaflet-heatmap.js"></script>

	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>CrowdScope</h1>

                <!-- Navigation Bar -->
                <a href="user.php"><i class="fas fa-user"></i>Dashboard</a>
                <a href="user_stats.php"><i class="selected fas fa-chart-line"></i>Stats</a>
                <a href="user_upload.php"><i class="fas fa-cloud-upload-alt"></i>Upload</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>

			</div>
		</nav>
		<div class="content">
			<h2>User Stats</h2>

			<!-- Range Picker -->
			<div>
				<form action="user_stats.php" method="post">
					<!-- Month Range -->
					<label for="start-month">Start Month:</label>
					<select id="start-month" name="start-month">
						<?php echo monthOption(); ?>
					</select>
					-
					<label for="end-month">End Month:</label>
					<select id="end-month" name="end-month">
						<?php echo monthOption(); ?>
					</select>
					<br/><br/>
					<!-- Year Range -->
					<label for="start-year">Start Year:</label>
					<select id="start-year" name="start-year">
						<?php echo yearOption(); ?>
					</select>
					-
					<label for="end-year">End Year:</label>
					<select id="end-year" name="end-year">
						<?php echo yearOption(); ?>
					</select>
					<br/><br/>
					<input type="submit" name="submit" value="Show">
				</form>
			</div>

			<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

				<?php
					if (isset($_POST["submit"])) {

						ini_set('display_errors', 1);
						ini_set('display_startup_errors', 1);
						error_reporting(E_ALL);

						// Load the range
						$start_month = (int)($_POST["start-month"]);
						$end_month = (int)($_POST["end-month"]);
						$start_year = (int)($_POST["start-year"]);
						$end_year = (int)($_POST["end-year"]);
						// echo "$start_month - $end_month : $start_year - $end_year";
						// echo "<br/>";

						echo "<div>";
						$body = 0;
						$vehicle = 0;
						$neutral = 0;
						$total = 0;
						// $results = [];
						// For each year
						for ($y = $start_year; $y <= $end_year; $y++) {
							// For each month
							for ($m = $start_month; $m <= $end_month; $m++) {
								$activities = getmonthactivity($_SESSION['id'], $m, $y);
								// Detailed information
								// $act = getmonthactivity_detailed($_SESSION['id'], $m, $y);
								// foreach ($act as $key => $value) {
								// 	$results[$key] += $value;
								// }
								$body += $activities[0];
								$vehicle += $activities[1];
								$neutral += $activities[2];
								$total += $activities[3];
							}
						}
						// $_ENV["ACTIVITIES"] = $results;
						if ($total > 0) {
							$body = (int)($body * 100 / $total);
							$vehicle = (int)($vehicle * 100 / $total);
							$neutral = (int)($neutral * 100 / $total);
							$activities_percentage = json_encode([$body, $vehicle, $neutral]);
							echo "<div class='chart-container'><canvas id='activityChart'></canvas></div>";

							$hour_activities = getHourActivity($_SESSION["id"], $start_month, $end_month, $start_year, $end_year);
							echo "<div class='chart-container'><canvas id='hourlyChart'></canvas></div>";

							$day_activities = getDayActivity($_SESSION["id"], $start_month, $end_month, $start_year, $end_year);
							echo "<div class='chart-container'><canvas id='dailyChart'></canvas></div>";

							$heats = getHeat($_SESSION["id"], $start_month, $end_month, $start_year, $end_year);
							echo "<div id='mapid'></div>";

						} else {
							echo "<div>No recorded activity for the selected period.</div>";
						}

						echo "</div>";

					}
				?>

				<!-- Chart Scripts -->
				<script>
					var ctx = document.getElementById('activityChart').getContext('2d');
					var myChart = new Chart(ctx, {
						type: 'doughnut',
						data: {
							labels: ['Body Activities', 'Vehicle Activities', 'Neutral Activities'],
							datasets: [{
								data: <?php echo $activities_percentage; ?>,
								backgroundColor: [
									'rgb(75, 192, 32)',
									'rgb(255, 159, 64)',
									'rgb(201, 203, 207)'
								],
							}]
						},
						options: {
							title: {
								display: true,
								text: 'Activity Type (%)'
							},
							maintainAspectRatio: false,
							animation: {
								animateScale: true,
								animateRotate: true
							}
						}
					});
				</script>

				<script>
					var ctx = document.getElementById('hourlyChart').getContext('2d');
					var myChart = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: <?php echo hourLabels(); ?>,
							datasets: [
							{
								label: 'Body Activity',
								data: <?php echo json_encode($hour_activities["BODY"]); ?>,
								backgroundColor: 'rgb(75, 192, 32)',
								borderColor: 'rgb(75, 192, 32)',
								borderWidth: 1
							},{
								label: 'Vehicle Activity',
								data: <?php echo json_encode($hour_activities["VEHICLE"]); ?>,
								backgroundColor: 'rgb(255, 159, 64)',
								borderColor: 'rgb(255, 159, 64)',
								borderWidth: 1
							},{
								label: 'Neutral Activity',
								data: <?php echo json_encode($hour_activities["NEUTRAL"]); ?>,
								backgroundColor: 'rgb(201, 203, 207)',
								borderColor: 'rgb(201, 203, 207)',
								borderWidth: 1,
								hidden: true
							}
							]
						},
						options: {
							title: {
								display: true,
								text: 'Hourly Histogram'
							},
							maintainAspectRatio: false,
							scales: {
								yAxes: [{
									gridLines: { display: false },
									ticks: { display: false, beginAtZero: true }
								}],
								xAxes: [{ gridLines: { display: false } }]
							}
						}
					});
				</script>

				<script>
					var ctx = document.getElementById('dailyChart').getContext('2d');
					var myChart = new Chart(ctx, {
						type: 'bar',
						data: {
							labels: <?php echo dayLabels(); ?>,
							datasets: [
							{
								label: 'Body Activity',
								data: <?php echo json_encode($day_activities["BODY"]); ?>,
								backgroundColor: 'rgb(75, 192, 32)',
								borderColor: 'rgb(75, 192, 32)',
								borderWidth: 1
							},{
								label: 'Vehicle Activity',
								data: <?php echo json_encode($day_activities["VEHICLE"]); ?>,
								backgroundColor: 'rgb(255, 159, 64)',
								borderColor: 'rgb(255, 159, 64)',
								borderWidth: 1
							},{
								label: 'Neutral Activity',
								data: <?php echo json_encode($day_activities["NEUTRAL"]); ?>,
								backgroundColor: 'rgb(201, 203, 207)',
								borderColor: 'rgb(201, 203, 207)',
								borderWidth: 1,
								hidden: true
							}
							]
						},
						options: {
							title: {
								display: true,
								text: 'Daily Histogram'
							},
							maintainAspectRatio: false,
							scales: {
								yAxes: [{
									gridLines: { display: false },
									ticks: { display: false, beginAtZero: true }
								}],
								xAxes: [{ gridLines: { display: false } }]
							}
						}
					});
				</script>

				<!-- Load the heatmap -->
				<script src="load_heatmap.js"></script>
				<script>
					var Data = {
						data:  <?php echo json_encode($heats); ?>
					};
					heatmapLayer.setData(Data);
				</script>
		</div>
	</body>
</html>