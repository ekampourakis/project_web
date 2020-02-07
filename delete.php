<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Delete Data</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>CrowdScope</h1>

                <!-- Navigation Bar -->
                <a href="index.php"><i class="fas fa-user"></i>Dashboard</a>
                <a href="user_stats.php"><i class="fas fa-chart-line"></i>Stats</a>
                <a href="delete.php"><i class="selected fas fa-cloud-upload-alt"></i>Delete</a>
                <a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>

			</div>
		</nav>
		<div class="content">

			<?php
			
				// Include the database
				require('config.php');
				// Resume the previous session
				session_start();
				// If the user is not logged in redirect to the login page
				if (!isset($_SESSION['loggedin'])) {
					header('Location: index.html');
					exit();
				}
				
				// Delete data from all the users
				$con->query("DELETE FROM data");
				$con->close();

				echo "<p>Data deleted successfully!</p>";
			?>
			
		</div>
	</body>
</html>