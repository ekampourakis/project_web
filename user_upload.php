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
		<title>Upload Data</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
		<!-- Load leaflet.js -->
		<link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css"
				integrity="sha512-xwE/Az9zrjBIphAcBb3F6JVqxf46+CDLwfLMHloNu6KEQCAWi6HcDUbeOfBIptF7tcCzusKFjFw2yuvEpDL9wQ=="
				crossorigin=""/>
		<script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"
				integrity="sha512-gZwIG9x3wUXg2hdXF6+rVkLF/0Vi9U8D2Ntg4Ga5I5BZpVkVxlJWbSQtXPSiUTtC0TjtGOmxa1AJPuV0CPthew=="
				crossorigin=""></script>
		<!-- Load the draw plugin -->
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.css"/>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/0.4.2/leaflet.draw.js"></script>

	</head>
	<body class="loggedin">
		<nav class="navtop">
			<div>
				<h1>CrowdScope</h1>

				<!-- Navigation Bar -->
				<a href="user.php"><i class="fas fa-user"></i>Dashboard</a>
				<a href="user_stats.php"><i class="fas fa-chart-line"></i>Stats</a>
				<a href="user_upload.php"><i class="selected fas fa-cloud-upload-alt"></i>Upload</a>
				<a href="logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>

			</div>
		</nav>

		<div class="content">
			<h2>Upload Data</h2>
			<p>Select the location history file you downloaded from Google in JSON format.
			Note that before uploading, you can exclude sensitive regions by highlighting them on the map using the toolbar located at the top right.
			</p>

			<div id="mapid"></div>
			
			<form action="upload.php" method="post" enctype="multipart/form-data">
				Filename: 
				<input type="file" name="file" id="file">
				<input type="hidden" name="areas" id="areas" value="[]">
				<input type="submit" name="submit" value="Submit">
			</form>

			<p>When uploading big JSON files it will take a while to process all the locations. Please be patient.</p>

		</div>

		<!-- Load the map -->
		<script src="upload_map.js"></script>
	</body>
</html>