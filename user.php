<?php

// Resume the previous session
session_start();

// If the user is not logged in redirect to the login page
if (!isset($_SESSION['loggedin'])) {
	header('Location: index.html');
	exit();
}

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>User Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>CrowdScope</h1>

				<!-- Navigation Bar -->
				<a href="user.php"><i class="selected fas fa-user"></i>Dashboard</a>
				<a href="user_stats.php"><i class="fas fa-chart-line"></i>Stats</a>
				<a href="user_upload.php"><i class="fas fa-cloud-upload-alt"></i>Upload</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<h2>User Dashboard</h2>
			<p>Welcome back, <?=$_SESSION['name']?>!</p>

			<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js"></script>

			<?php

				// Include the database
				require('config.php');
				require('functions.php');
				// Resume the previous session
				session_start();
				// If the user is not logged in redirect to the login page
				if (!isset($_SESSION['loggedin'])) {
					header('Location: index.html');
					exit();
				}
				// ini_set('display_errors', 1);
				// ini_set('display_startup_errors', 1);
				// error_reporting(E_ALL);

				// The logged in user's ID
				$id = $_SESSION["id"];

				// Calculate the month's score
				$eco_score = eco_score(getmonthactivity($id, date("m"), date("Y")));
				$month = date("(F)", date("m"));
				echo "<p> <i class='far fa-star'></i> Your eco score for this month $month is $eco_score%.</p>";
				// Display in a circular progress bar

				// Calculate the year's score
				$months_activity = [];
				for ($i = 1; $i <= 12; $i++) { array_push($months_activity, eco_score(getmonthactivity($id, $i, date("Y")))); }
				$months_activity = json_encode($months_activity);
				// Display in a chart
				echo "<div class='chart-container'><canvas id='annualChart'></canvas></div>";

				// Calculate the extreme dates of the locations
				$min_q = $con->query("SELECT MIN(timestampMs) FROM data WHERE userid='$id'");
				$max_q = $con->query("SELECT MAX(timestampMs) FROM data WHERE userid='$id'");
				$cnt_q = $con->query("SELECT COUNT(timestampMs) FROM data WHERE userid='$id'");
				if ($min_q and $max_q and $cnt_q) {
					$cnt = mysqli_fetch_assoc($cnt_q)["COUNT(timestampMs)"];
					echo "<p> <i class='far fa-calendar-alt'></i> You have $cnt entries";
					if ($cnt > 0) {
						$min = date("j F Y", mysqli_fetch_assoc($min_q)["MIN(timestampMs)"] / 1000.0);
						$max = date("j F Y", mysqli_fetch_assoc($max_q)["MAX(timestampMs)"] / 1000.0);
						" ranging from $min to $max.</p>";
					} else {
						echo ".</p>";
					}

				}

				// Calculate the latest entry date
				$max_u = $con->query("SELECT MAX(timestampUpload) FROM data WHERE userid='$id'");
				if ($max_u) {
					$latest = date("j F Y, H:m:s", mysqli_fetch_assoc($max_u)["MAX(timestampUpload)"] / 1000.0);
					echo "<p> <i class='far fa-clock'></i> Your last upload was";
					if ($cnt > 0) {
						echo " on $latest.</p>";
					} else {
						echo " never.</p>";
					}
				}

				// Fetch all user IDs
				$users_q = $con->query("SELECT DISTINCT userid, firstname, lastname FROM users");
				$users = [];
				if ($users_q) {
					while ($row = mysqli_fetch_assoc($users_q)) {
						array_push($users, [$row["userid"], $row["firstname"], $row["lastname"]]);
					}
				}
				// Calculate the scores for all users
				$scores = [];
				foreach ($users as &$value) {
					array_push($scores, [eco_score(getmonthactivity($value[0], date("m"), date("Y"))), $value[1], $value[2], $value[0]]);
				}
				usort($scores, "scoreSort");
				$userplace = array_search($id, array_column($scores, 3), TRUE);
				$place = toPlace($userplace);
				echo "<p> <i class='fas fa-medal'></i> You ranked $place in the global eco rank this month.</p>";
				$podium = [];
				for ($i = 0; $i < min(3, count($scores)); $i++) {
					$userid = $scores[$i][3];
					$name = $scores[$i][1];
					$surname = mb_substr($scores[$i][2], 0, 1,'UTF8');
					$score = $scores[$i][0];
					$place = toPlace($i);
					array_push($podium, "$place: $name $surname. $score%");
				}
				// Fill missing places (if any)
				while (count($podium) < 3) {
					$missingPlace = count(length) + 1;
					$missingPlace = toPlace($missingPlace);
					array_push($podium, "$missingPlace: Empty");
				}
				// Swap the first two places for better chart visualization
				$swap_tmp = $podium[0];
				$podium[0] = $podium[1];
				$podium[1] = $swap_tmp;
				unset($swap);
				$podium = json_encode($podium);
				echo "<div class='chart-container'><canvas id='podiumChart'></canvas></div>";

			?>

			<!-- Chart Scripts -->
			<script>
				var ctx = document.getElementById('annualChart').getContext('2d');
				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
						datasets: [{
							label: 'Eco Score',
							data: <?php echo $months_activity; ?>,
							backgroundColor: 'rgba(255, 99, 132, 0.2)',
							borderColor: 'rgba(255, 99, 132, 1)',
							borderWidth: 1
						}]
					},
					options: {
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
				var ctx = document.getElementById('podiumChart').getContext('2d');
				var myChart = new Chart(ctx, {
					type: 'bar',
					data: {
						labels: <?php echo $podium; ?>,
						datasets: [{
							label: 'Eco Podium',
							data: [2, 3, 1],
							backgroundColor: ['rgba(167, 167, 173, 0.8)', 'rgba(214, 175, 54, 0.8)', 'rgba(106, 56, 5, 0.8)'],
						}]
					},
					options: {
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

		</div>
	</body>
</html>