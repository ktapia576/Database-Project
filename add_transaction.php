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

                // Fetch User Info
                $sql = "SELECT * FROM Customers WHERE id='$_COOKIE['customerID']'";
            
                $result = $conn->query($sql) or die($conn->error);

                // Store User info
                $row = $result->fetch_assoc();  //fetch data from database
                $id = $row["id"];
                $name = $row["name"];
                
                print('<button type="button" onclick="logout()">Logout</button><br>');  // logout button
                print('<h2>Add Transaction</h2><br>');
                
                $sql = "SELECT SUM(amount) as balance FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";

                // Get result or show error and die
                $result = $conn->query($sql) or die($conn->error);
                $row = $result->fetch_assoc();
                
                $balance=$row['balance'];
                if($balance === NULL){
                    printf($name." current balance is NULL");
                }
                printf($name." current balance is %.2f",$balance);

            }
            else{
                print("<p>You must log in to use this feature!</p>");
            }    
        ?>

    </body>
</html>