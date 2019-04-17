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
                if(!empty($_GET['keyword'])){
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
                    $keyword = $_GET['keyword'];
                    $sql = "SELECT * FROM Customers WHERE id='$id'";    // Fetch User Info
                
                    $result = $conn->query($sql) or die($conn->error);

                    // Store User info
                    $row = $result->fetch_assoc();  //fetch data from database
                    $name = $row["name"];
                    
                    print('<p><button type="button" onclick="logout()">Logout</button></p>');  // logout button
                    print('<h2>Search</h2><br>');
                    print('<p>The transactions in customer <b>'.$name.'</b> records matched keyword <b>'.$keyword.'</b> are: </p>');
                    
                    // ------------------------- Search -----------------------------------------
                    if($keyword === '*'){   // Check if user enters * for all transactions
                        $sql = "SELECT mid, code, type, amount, mydatetime, note, sid FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";
                    }
                    else {
                        $sql = "SELECT mid, code, type, amount, mydatetime, note, sid FROM CPS3740_2019S.Money_tapiake WHERE cid='$id' AND note LIKE '%$keyword%'";
                    }
                    
                    // Get result or show error and die
                    $result = $conn->query($sql) or die($conn->error);

                    // Check if there are results from search
                    if($result->num_rows > 0) {
                        // Print Header columns of table
                        echo "<table><tr><th>ID</th><th>Code</th><th>Operation</th><th>Amount</th><th>Date Time</th><th>Note</th></tr>";

                        // Print rows with data
                        while($row = $result->fetch_assoc()) {
                            if($row["type"] === 'D'){
                                $type='<td>Deposit</td><td style="color: blue;">';
                            }
                            else {
                                $type='<td>Withdraw</td><td style="color: red;">';
                            }
                            print("<tr><td>".$row["mid"]."</td><td>".$row["code"]."</td>".$type.
                            $row["amount"]."</td><td>".$row["mydatetime"]."</td><td>".$row["note"]."</td></tr>"); 
                        }

                        echo"</table><br>";
                    }
                    else {
                        print("<p>No records found!</p>");
                    }      
                    // -------------------------------------------------------------------------------

                    $sql = "SELECT SUM(amount) as balance FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";    // Get balance of User based on ID

                    // Get result or show error and die
                    $result = $conn->query($sql) or die($conn->error);
                    $row = $result->fetch_assoc();

                }
                else {
                    print("<p>Keyword text box empty!</p>");
                }
            }
            else{
                print("<p>You must log in to use this feature!</p>");
            }    
        ?>

    </body>
</html>