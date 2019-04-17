<!DOCTYPE html>
<html>
    <head>
        <title>CPS3740 Project</title>

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
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

                $id = $_COOKIE['customerID'];
                $sql = "SELECT * FROM Customers WHERE id='$id'";    // Fetch User Info
            
                $result = $conn->query($sql) or die($conn->error);

                // Store User info
                $row = $result->fetch_assoc();  //fetch data from database
                $name = $row["name"];
                
                // print('<p><button type="button" onclick="logout()">Logout</button></p>');  // logout button
                print ('<p><a href="javascript:logout()">Logout</a></p>'); // Logout Button 
                print('<h3>Add Transaction</h3>');
                
                $sql = "SELECT SUM(amount) as balance FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";    // Get balance of User based on ID

                // Get result or show error and die
                $result = $conn->query($sql) or die($conn->error);
                $row = $result->fetch_assoc();
                
                $balance=$row['balance'];
                if($balance === NULL){
                    printf("<b>".$name."</b> current balance is <b>NULL</b>. May not have any transactional records");
                }
                else {
                    printf("<b>".$name."</b> current balance is <b>%.2f</b>.",$balance);
                }

                // ----------------------- Form for Add Transaction ---------------------------
                print ("");
            }
            else{
                print("<p>You must log in to use this feature!</p>");
            }    
        ?>

    </body>
</html>