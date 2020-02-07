<?php

date_default_timezone_set('UTC');

/**
 * Calculates the great-circle distance between two points using the Haversine formula.
 * @param float $latitudeFrom Latitude of start point in [deg decimal]
 * @param float $longitudeFrom Longitude of start point in [deg decimal]
 * @param float $latitudeTo Latitude of target point in [deg decimal]
 * @param float $longitudeTo Longitude of target point in [deg decimal]
 * @param float $earthRadius Mean earth radius in [m]
 * @return float Distance between points in [m] (same as earthRadius)
 */
function haversine($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
	$latFrom = deg2rad($latitudeFrom);
	$lonFrom = deg2rad($longitudeFrom);
	$latTo = deg2rad($latitudeTo);
	$lonTo = deg2rad($longitudeTo);
	$latDelta = $latTo - $latFrom;
	$lonDelta = $lonTo - $lonFrom;
	$angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
	return $angle * $earthRadius;
}

/**
 * Calculates if a coordinate pair is inside a rectangle
 * @param float[] $xs The longitudes of the defining edges of the rectangle
 * @param float[] $ys The latitudes of the defining edges of the rectangle
 * @return bool If the coordinate pair is inside the rectangle
 */
function inside($xs, $ys, $x, $y) {
    if ($x > min($xs) && $x < max($xs) && $y > min($ys) && $y < max($ys)) { return true; }
    return false;
}

function getmonthstamps(int $month, int $year=2019) {
	$query_date = "$year-$month-01";
	$first = date("Y-m-01 00:00:00", strtotime($query_date));
	$last = date("Y-m-t 23:59:59", strtotime($query_date));
	$first_stamp = strtotime($first) * 1000;
	$last_stamp = strtotime($last) * 1000;
	return [$first_stamp, $last_stamp];
}

function eco_score($activity) {
	if ($activity[0] <= 0 and $activity[1] <= 0) {
		return 0;
	}
	return (int)(100* $activity[0] / ($activity[0] + $activity[1]));
}

function getmonthactivity($id, int $month, int $year=2019) {
	require('config.php');
	$stamps = getmonthstamps($month, $year);
	if ($result = $con->query("SELECT * FROM data WHERE userid='$id' AND timestampMs>$stamps[0] AND timestampMs<$stamps[1]")) {
		// Classifications of activites
		$body = ["ON_BICYCLE", "ON_FOOT", "RUNNING", "WALKING"];
		$neutral = ["STILL", "TILTING", "UNKNOWN", "NULL"];
		$vehicle = ["IN_VEHICLE"];
		// All other activities are considered as extra vehicle classifications
		$results = 0;
		$body_activities = 0;
		$vehicle_activities = 0;
		$neutral_activities = 0;
		while($row=mysqli_fetch_assoc($result)) {
			$results++;
			$activity = $row["activity_type"];
			if (in_array($activity, $body)) {
				$body_activities++;
			} else if (in_array($activity, $neutral)) {
				$neutral_activities++;
			} else {
				$vehicle_activities++;
			}
		}
		// Free result set
		$result->close();
		return [$body_activities, $vehicle_activities, $neutral_activities, $results];
	}
}

function getmonthactivity_detailed($id, int $month, int $year=2019) {
	require('config.php');
	$stamps = getmonthstamps($month, $year);
	if ($result = $con->query("SELECT * FROM data WHERE userid='$id' AND timestampMs>$stamps[0] AND timestampMs<$stamps[1]")) {
		$results = [];
		while($row=mysqli_fetch_assoc($result)) {
			$results[$row["activity_type"]]++;
		}
		// Free result set
		$result->close();
		return $results;
	}
}

function dictKeys($dict) {
	$result = [];
	foreach ($dict as $key => $value) {
		array_push($result, $key);
	}
	return json_encode($result);
}

function dictValues($dict) {
	$result = [];
	foreach ($dict as $key => $value) {
		array_push($result, $value);
	}
	return json_encode($result);
}

function activityToCategory($activity) {
	$categories = ["BODY" => ["ON_BICYCLE", "ON_FOOT", "RUNNING", "WALKING"],
				"NEUTRAL" => ["STILL", "TILTING", "UNKNOWN", "NULL"],
				"VEHICLE" => ["IN_VEHICLE", "EXITING_VEHICLE"]];
	$ok = false;
	$result = "";
	foreach ($categories as $key => $value) {
		if (in_array($activity, $categories[$key])) {
			$result = $key;
			$ok = true;
		break;
		}
	}
	// Categorize unknown activies as vehicle types
	return $ok ? $result :"VEHICLE";
}

function dayToIndex($day) {
	$labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
	return array_search($day, $labels);
}

function getHourActivity($id, $start_month, $end_month, $start_year, $end_year) {
	require('config.php');

	// The resulting array
	$results = ["BODY" => [], "NEUTRAL" => [], "VEHICLE" => []];
	foreach ($results as $key => $value) {
		for ($j = 0; $j < 24; $j++) {
			array_push($results[$key], 0);
		}
	}

	// For each year
	for ($y = $start_year; $y <= $end_year; $y++) {
		// For each month
		for ($m = $start_month; $m <= $end_month; $m++) {
			$stamps = getmonthstamps($m, $y);
			// Query all entries

			if ($result = $con->query("SELECT * FROM data WHERE userid='$id' AND timestampMs>$stamps[0] AND timestampMs<$stamps[1]")) {
				// For each entry of the month
				while($row=mysqli_fetch_assoc($result)) {
					// Use entry timestamp if activity timestamp is not available
					$timestamp = $row["activity_timestampMs"] > 0 ? $row["activity_timestampMs"] / 1000.0 : $row["timestampMs"];
					// Load hour based on timestamp of entry
					$entry_hour = (int)(date("H", $timestamp));
					// Sum activity type to the hour dictionary
					$results[activityToCategory($row["activity_type"])][$entry_hour]++;
				}
			}
		}
	}
	// Return a 3x24 array containing per hour information for the 3 activity categories
	return $results;
}

function getDayActivity($id, $start_month, $end_month, $start_year, $end_year) {
	require('config.php');

	// The resulting array
	$results = ["BODY" => [], "NEUTRAL" => [], "VEHICLE" => []];
	foreach ($results as $key => $value) {
		for ($j = 0; $j < 7; $j++) {
			array_push($results[$key], 0);
		}
	}

	// For each year
	for ($y = $start_year; $y <= $end_year; $y++) {
		// For each month
		for ($m = $start_month; $m <= $end_month; $m++) {
			$stamps = getmonthstamps($m, $y);
			// Query all entries

			if ($result = $con->query("SELECT * FROM data WHERE userid='$id' AND timestampMs>$stamps[0] AND timestampMs<$stamps[1]")) {
				// For each entry of the month
				while($row=mysqli_fetch_assoc($result)) {
					// Use entry timestamp if activity timestamp is not available
					$timestamp = $row["activity_timestampMs"] > 0 ? $row["activity_timestampMs"] / 1000.0 : $row["timestampMs"];
					// Load hour based on timestamp of entry
					$entry_day = dayToIndex(date("l", $timestamp));
					// Sum activity type to the hour dictionary
					$results[activityToCategory($row["activity_type"])][$entry_day]++;
				}
			}
		}
	}
	// Return a 3x24 array containing per hour information for the 3 activity categories
	return $results;
}

function getHeat($id, $start_month, $end_month, $start_year, $end_year) {
	require('config.php');
	// The resulting array
	$results = [];
	// For each year
	for ($y = $start_year; $y <= $end_year; $y++) {
		// For each month
		for ($m = $start_month; $m <= $end_month; $m++) {
			$stamps = getmonthstamps($m, $y);
			// Query all entries

			if ($result = $con->query("SELECT * FROM data WHERE userid='$id' AND timestampMs>$stamps[0] AND timestampMs<$stamps[1]")) {
				// For each entry of the month
				while($row=mysqli_fetch_assoc($result)) {
					array_push($results, ["lat" => $row["latitudeE7"] / 10000000.0, "lng" => $row["longitudeE7"] / 10000000.0, "count" => 1]);
				}
			}
		}
	}
	return $results;
}

function scoreSort($a,$b) { return ($a[0] >= $b[0]) ? -1 : 1; }

function toPlace($i) {
	if ($i > 2) { $iplus = ($i + 1) . "th"; return $iplus; }
	return ($i === 0 ? "1st" : ($i === 1 ? "2nd" : "3rd"));
}

function toMonth($m) {
	return date("F", mktime(0, 0, 0, $m, 10));
}

function monthOption() {
	$options = "";
	for ($m = 1; $m <= 12; $m++) {
		$currentMonth = ($m === date('m') ? "selected='$m'" : "");
		$options = $options . "<option value='$m' $currentMonth>";
		$options = $options . toMonth($m) . "</option>";
	}
	return $options;
}

function yearOption() {
	$options = "";
	for ($y = 2017; $y <= 2020; $y++) {
		$currentYear = ($y == date('Y') ? "selected='$y'" : "");
		$options = $options . "<option value='$y' $currentYear>$y</option>";
	}
	return $options;
}

function hourLabels() {
	$labels = [];
	for ($i = 0; $i < 24; $i++) {
		array_push($labels, str_pad($i, 2, "0", STR_PAD_LEFT) . ":00");
	}
	return json_encode($labels);
}

function dayLabels() {
	$labels = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
	return json_encode($labels);
}

?>