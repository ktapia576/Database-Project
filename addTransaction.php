<!DOCTYPE html>
<html>
    <head>
        <title>CPS3740 Project</title>

        <link href="styles.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet">

        <script defer src="utility.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    </head>

    <body>
        <?php
            if(isset($_COOKIE['customerID'])){  // Check if User is logged in
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

                print("<p>You can you use this feature!</p>");
            }
            else{
                print("<p>You must log in to use this feature!</p>");
            }    
        }
        ?>

    </body>
</html>