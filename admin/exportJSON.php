<?php
$sql="select *,users.userid from data left join users on users.userid = data.userid;";

$response = array();
$posts = array();
$result=mysql_query($sql);
while($row=mysql_fetch_array($result)) {
  $userid=$row['users.userid'];
  $heading=$row['heading'];
  $type=$row['activity_type'];
  $confidence=$row['activity_confidence'];
  $atimestamp=$row['activity_timestampMs'];
  $vaccuracy=$row['verticalAccuracy'];
  $velocity=$row['velocity'];
  $accuracy=$row['accuracy'];
  $longitude=$row['longitudeE7'];
  $latitude=$row['latitudeE7'];
  $altitude=$row['altitude'];
  $timestampMs=$row['timestampMs'];


  $posts[] = array('heading'=> $heading, 'activity_type'=> $type, 'confidence'=> $confidence, 'activity_timestampMs'=> $atimestamp,'verticalAccuracy'=> $vaccuracy, 'velocity'=> $velocity,
   'accuracy'=> $accuracy, 'longitude'=> $longitude, 'latitude'=> $latitude, 'altitude'=> $altitude, 'timestampMs'=> $timestampMs, 'userid'=> $userid);
}

$response['posts'] = $posts;

$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($response));
fclose($fp);
