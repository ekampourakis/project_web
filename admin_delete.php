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
		<title>Admin Dashboard</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>CrowdScope</h1>

				<!-- Navigation Bar -->
				<a href="admin.php"><i class="selected fas fa-user"></i>Dashboard</a>
				<a href="admin_stats.php"><i class="fas fa-chart-line"></i>Stats</a>
				<a href="admin_delete.php"><i class="fas fa-cloud-upload-alt"></i>Delete</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
			</div>
		</nav>
		<div class="content">
			<div>
				<form action="delete.php">
					<input type="submit" value="Delete all user data"/>
				</form>
			</div>
			
		</div>

	</body>
</html>
