<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "construction_pms_db";

// Make sure $id is defined
$id = isset($_GET['id']) ? $_GET['id'] : 1; // Set a default or get it from the request

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize an array for storing chart data
$array = [];

// Query the database for project progress data
$prog = $conn->query("SELECT * FROM project_partition NATURAL JOIN project_division WHERE project_id = '$id' ");
while ($progress = $prog->fetch_assoc()) {
    $name = $progress['division'];
    $pid = $progress['pp_id'];

    $prog3 = $conn->query("SELECT SUM(progress) as total_prog FROM project_progress WHERE pp_id = '$pid'");
    $row_prog = $prog3->fetch_assoc();

    if ($prog && $prog->num_rows > 0) {
        // Assign a color based on total progress
        if ($row_prog['total_prog'] <= 50) {
            $color = 'rgba(251, 159, 118, 0.53)';
        } else {
            $color = 'rgba(120, 151, 239, 0.53)';
        }

        // Prepare data for chart
        $array[$id][] = '{"progress":"' . $row_prog['total_prog'] . '","name":"' . ucfirst($name) . '","color":"' . $color . '"}';
    } else {
        $array[$id][] = '{"progress":"0","name":"No Data","color":"rgba(255, 0, 0, 0.53)"}';
    }
}

// Query for total project progress
$prog2 = $conn->query("SELECT SUM(progress) as total FROM project_progress NATURAL JOIN project_partition WHERE project_id = '$id' ");
$progress2 = $prog2->fetch_assoc();
$total = ($progress2['total'] / ($prog->num_rows ?: 1)); // Prevent division by zero
$tots = number_format($total, 0);

// Define total color
$colors = 'rgba(0, 241, 5, 0.39)';

// Prepare the final chart data
$data2 = ',{"progress":"' . $tots . '","name":"Total","color":"' . $colors . '"}';
$data = implode(',', $array[$id]);

$conn->close();
?>

<!-- Render the chart -->
<div class="chartdiv" id="chartdiv<?php echo $id ?>" style="width:100%; height:300px;"></div>

<script>
jQuery(document).ready(function(){
    chart.exportConfig = {
        menuItems: [{
            icon: '../am_chart/images/export.png',
            format: 'png',
            onclick: function(a) {
                var output = a.output({
                    format: 'png',
                    output: 'datastring'
                }, function(data) {
                    console.log(data);
                });
            }
        }]
    };
});

var chart = AmCharts.makeChart("chartdiv<?php echo $id ?>", {
    "type": "serial",
    "theme": "none",
    "pathToImages": "http://localhost/new_admin/am_chart/images/export.png",
    "dataProvider": [<?php echo $data . $data2 ?>],
    "valueAxes": [{
        "axisAlpha": 0,
        "position": "left",
        "title": "Project Progress (%)",
    }],
    "startDuration": 1,
    "graphs": [{
        "balloonText": "<b>[[category]]: [[value]]</b>",
        "colorField": "color",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "type": "column",
        "valueField": "progress",
        "labelText": "[[progress]]%",
        "labelPosition": "inside",
    }],
    "chartCursor": {
        "categoryBalloonEnabled": false,
        "cursorAlpha": 0,
        "zoomable": false
    },
    "categoryField": "name",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation": 50,
        "title": "Divisions"
    },
});
</script>
