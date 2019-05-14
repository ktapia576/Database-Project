<!DOCTYPE html>
<html>
    <head>
        <title>CPS3740 Project Bonus</title>
        
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
        <link href="styles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    </head>

    <body>
        <?php 
        // ------------------- Connect to Database ---------------------------
        function db_connect() {
            $conn;
            
            if(!isset($conn)) {
                $config = parse_ini_file('config.ini'); // Load database config
                $conn = new mysqli($config['servername'],$config['username'],$config['password'],$config['dbname']);    // Create connection to database using config.ini
            }

            if($conn === false) {
                return mysqli_connect_error(); 
            }
            return $conn;
        }

        // Connect to the database
        $conn = db_connect();

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        // --------------------------------------------------------------------


        // --------------------------------- Create table --------------------------------
        $sql = "SELECT * FROM CPS3740.Stores WHERE latitude IS NOT NULL AND longitude IS NOT NULL";

        // Get result or show error and die
        $result = $conn->query($sql) or die($conn->error);

        if($result->num_rows > 0) {   // Check if there are records found
            // Print Header columns of table
            print("<table class='highlight' style='width: 50%;'>
            <tr><th>ID</th><th>Name</th><th>Address</th><th>City</th><th>State</th><th>Zipcode</th><th>Location(Latitude,Longitude)</th></tr>");

            // Print rows with data
            while($row = $result->fetch_assoc()) {
                print("<tr><td>".$row["sid"]."</td><td>".$row["Name"]."</td><td>".
                $row["address"]."</td><td>".$row["city"]."</td>
                <td>".$row["State"]."</td><td>".$row["Zipcode"]."</td><td>".$row["latitude"].",".$row["longitude"]."</td></tr>"); 

                $storeInfo[] = (object) array( 
                    'Id' =>  $row['sid'],
                    'Name' => $row['Name'],
                    'Address'=> $row['address'],
                    'City' => $row['city'],
                    'State' => $row['State'],
                    'Zipcode' => $row['Zipcode'],
                    'Latitude'=> $row['latitude'],
                    'Longitude'=> $row['longitude']
                );
            }

            echo"</table><br>";
        }
        else {
            print("There are no records found.<br>");
        }

        // ---------------------------------------------------------------------------------------
        
        ?>

        <script>
            var locationMarkers = [];
            var i = 0;
            var storeInfo = JSON.parse('<?php echo json_encode($storeInfo); ?>');  // where store info will be stored

            var arrayLength = storeInfo.length;

            //put store information into map marker location array
            while ( i < arrayLength ){ 
                locationMarkers.push([storeInfo[i].Id , storeInfo[i].Name , storeInfo[i].Latitude, 
                storeInfo[i].Longitude , storeInfo[i].Address , storeInfo[i].City,
                storeInfo[i].State , storeInfo[i].Zipcode] );

                i++;
            }

            function initialize() {
                var bounds  = new google.maps.LatLngBounds(); // var to get the markers locations and set bounds/zoom
                var options = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };

                var map = new google.maps.Map(document.getElementById('map'), options);
                var infowindow = new google.maps.InfoWindow();
                
                var markerIcon = {
                    scaledSize: new google.maps.Size(80, 80),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(32,65),
                    labelOrigin: new google.maps.Point(40,33)
                };
                var location;
                var mySymbol;
                var marker, m;

                

                for (m = 0; m < locationMarkers.length; m++) {
                    //console.log("Length of markerlocation " + locationMarkers.length);
                    location = new google.maps.LatLng(locationMarkers[m][2], locationMarkers[m][3]),
                    marker = new google.maps.Marker({
                        map: map,
                        position: location,
                        icon: markerIcon,
                        label: {
                            text: locationMarkers[m][0] ,
                            color: "black",
                            fontSize: "16px",
                            fontWeight: "bold"
                        }

                    });

                    bounds.extend(location);
                    map.fitBounds(bounds);       // zoom based on all markers
                    map.panToBounds(bounds);     // center based on all markers

                    google.maps.event.addListener(marker, 'click', (function(marker, m) {
                        return function() {
                            infowindow.setContent("Store Name: " + locationMarkers[m][1] + 
                                "<br>" + locationMarkers[m][4] + ", " + locationMarkers[m][5] + 
                                ", " + locationMarkers[m][6] + " " + locationMarkers[m][7]);
                            infowindow.open(map, marker);
                        }
                    })(marker, m));
                }

            }
            google.maps.event.addDomListener(window, 'load', initialize);;
        </script>

        <div id="map" style="height: 500px; width: 50%;"></div>

    </body>
</html>