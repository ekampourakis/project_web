
<html>
  <head>
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    <script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);
      //a
      function drawChart() {

        var data1 = google.visualization.arrayToDataTable([
        ['activity type', 'count']
        <?php
        foreach ($connections as $b1) {
          echo"['".b1['users.userid']."', ".b1['COUNT(data.foreignkey)']."],";
        }
         ?>
        ]);

        var options = {
          title: 'overall activities'
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart1'));

        chart.draw(data1, options);
      }
//b
        function drawChart() {

          var data2 = google.visualization.arrayToDataTable([
          ['month', 'count']
          <?php
          foreach ($month as $b2) {
            echo"['".b2['timestamp']."', ".b2['COUNT(userid)']."],";
          }
           ?>
          ]);

          var options = {
            title: 'overall activities'
          };
          var chart = new google.visualization.PieChart(document.getElementById('piechart2'));

          chart.draw(data2, options);
        }
        function drawChart() {

          var data3 = google.visualization.arrayToDataTable([
          ['day', 'count']
          <?php
          foreach ($day as $b3) {
            echo"['".b3['timestamp']."', ".b3['COUNT(userid)']."],";
          }
           ?>
          ]);

          var options = {
            title: 'overall activities'
          };
          var chart = new google.visualization.PieChart(document.getElementById('piechart3'));

          chart.draw(data3, options);
        }
        function drawChart() {

          var data4 = google.visualization.arrayToDataTable([
          ['hour', 'count']
          <?php
          foreach ($hour as $b4) {
            echo"['".b4['timestamp']."', ".b4['COUNT(userid)']."],";
          }
           ?>
          ]);

          var options = {
            title: 'overall activities'
          };
          var chart = new google.visualization.PieChart(document.getElementById('piechart4'));

          chart.draw(data4, options);
        }
        function drawChart() {

          var data5 = google.visualization.arrayToDataTable([
          ['year', 'count']
          <?php
          foreach ($year as $b5) {
            echo"['".b5['timestamp']."', ".b5['COUNT(userid)']."],";
          }
           ?>
          ]);

          var options = {
            title: 'overall activities'
          };
          var chart = new google.visualization.PieChart(document.getElementById('piechart5'));

          chart.draw(data5, options);
        }
    </script>
  </head>
  <body>
    <div id="piechart1" style="width: 900px; height: 500px;"></div>
    <div id="piechart2" style="width: 900px; height: 500px;"></div>
    <div id="piechart3" style="width: 900px; height: 500px;"></div>
    <div id="piechart4" style="width: 900px; height: 500px;"></div>
    <div id="piechart5" style="width: 900px; height: 500px;"></div>
  </body>
</html>
