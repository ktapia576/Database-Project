<!DOCTYPE html>
<html>
    <head>
        <title>CPS3740 Project</title>
        <style>
            table, th, td {
                border: 1px solid black;
            }
        </style>
    </head>

    <body>
        <p>The following customers are in the bank system:</p>
        <table>  
        <?php
            function db_connect() {
                $conn;
                    // Try and connect to the database, if a connection has not been established yet
                if(!isset($conn)) {
                    // Load database configuration as an array. Using the location of the configuration file
                    $config = parse_ini_file('config.ini'); 
                    $conn = new mysqli($config['servername'],$config['username'],$config['password'],$config['dbname']);    // Create connection to database using config.ini
                }
                    // If connection was not successful, handle the error
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

            $sql = "SELECT * FROM Customers";

            // Get result or show error and die
            $result = $conn->query($sql) or die($conn->error);

            // Print Header columns of table
            echo "<tr><th>ID</th><th>login</th><th>password</th><th>Name</th><th>Gender</th><th>DOB</th><th>Street</th><th>City</th><th>State</th><th>Zipcode</th></tr>";

            // Print rows with data
            while($row = $result->fetch_assoc()) {
               print("<tr><td>".$row["id"]."</td><td>".$row["login"]."</td><td>".$row["password"]."</td><td>".
               $row["name"]."</td><td>".$row["gender"]."</td><td>".$row["DOB"]."</td><td>".$row["street"]."
               </td><td>".$row["city"]."</td><td>".$row["state"]."</td><td>".$row["zipcode"]."</td></tr>"); 
            }
        ?>
        </table>
    </body>
</html>