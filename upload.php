<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Upload Data</title>
		<link href="style.css" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">
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
				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);
				require_once('vendor/autoload.php');
				require_once('functions.php');

				// Load the censored areas
				$rect = json_decode($_POST["areas"], true);

				if(isset($_POST["submit"]) && isset($_POST["areas"])) {

					$target_path = $_SERVER['DOCUMENT_ROOT']."/uploads/";
					if (file_exists($target_path.$_FILES["file"]["name"])) {
						// echo "<p>An error occured while uploading your data. Please try again.</p>";
						unlink($target_path.$_FILES["file"]["name"]);
					}
					move_uploaded_file($_FILES["file"]["tmp_name"], $target_path.$_FILES["file"]["name"]);
					// Load file process
					try {
						// Create the pointer for the JSON parser
						$jsonStream = \JsonMachine\JsonMachine::fromFile($target_path.$_FILES["file"]["name"], "/locations");
						// Counters to count the number of locations that were processed
						$cnt = 0;
						$id = $_SESSION["id"];
						// For each entry in the JSON
						foreach ($jsonStream as $name => $data) {
							// Extract the vital information
							$lat = $data["latitudeE7"];
							$long = $data["longitudeE7"];
							// Calculate the distance from Patras
							$dist = haversine($lat / 10000000.0, $long / 10000000.0, 38.230462, 21.753150);
							// Exclude all points that are censored by the user
							$censored = false;
							foreach ($rect as &$value) {
								$poly = [$value["p1"], $value["p2"], $value["p3"], $value["p4"]];
								$xs = [$poly[0][0], $poly[1][0], $poly[2][0], $poly[3][0]];
								$ys = [$poly[0][1], $poly[1][1], $poly[2][1], $poly[3][1]];
								if (inside($xs, $ys, $long / 10000000.0, $lat / 10000000.0)) { $censored = true; }
								if ($censored) { break; }
							}
							// If location fulfills the criteria
							if ($dist <= 10000 && !$censored) {
								// Default all information
								$timestamp = $data["timestampMs"];
								$accuracy = $data["accuracy"];
								$activity_timestamp = "NULL";
								$activity_type = "NULL";
								$activity_confidence = "NULL";
								$altitude = "NULL";
								$verticalAccuracy = "NULL";
								$velocity = "NULL";
								$heading = "NULL";
								$uploadTimestamp = time() * 1000;
								// Load activity information
								if (isset($data["activity"])) {
									// Default to the last recent activity (already sorted ascending by Google)
									$activity = $data["activity"][0];
									$activity_timestamp = $activity["timestampMs"];
									// Default to the most confident type (already sorted descending by Google)
									$activity_type = $activity["activity"][0]["type"];
									$activity_confidence = $activity["activity"][0]["confidence"];
								}
								// Load altitude information
								if (isset($data["altitude"])) { $altitude = $data["altitude"]; }
								if (isset($data["verticalAccuracy"])) { $verticalAccuracy = $data["verticalAccuracy"]; }
								// Load velocity vector information
								if (isset($data["velocity"])) { $velocity = $data["velocity"]; }
								if (isset($data["heading"])) { $heading = $data["heading"]; }
								// Create insert query
								$query = "INSERT INTO data (userid, heading, activity_type, activity_confidence, activity_timestampMs, verticalAccuracy, velocity, accuracy, longitudeE7, latitudeE7, altitude, timestampMs, timestampUpload) VALUES ('$id', $heading, '$activity_type', $activity_confidence, $activity_timestamp, $verticalAccuracy, $velocity, $accuracy, $long, $lat, $altitude, $timestamp, $uploadTimestamp)";
								$con->query($query);
								// Increment the counter
								$cnt = $cnt + 1;
							}
						}						
					} catch (Exception $e) {
						echo "<p>Something went wrong. Please try again.</p>";
					}
					unlink($target_path.$_FILES["file"]["name"]);
					echo "<p>Uploaded $cnt locations successfully!</p>";
				} else {
					echo "<p>Something went wrong. Please try again.</p>";
				}
				$con->close();
			?>
			
		</div>
	</body>
</html>