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

                
                    // -------------------- Get Sources from Sources Table -------------------------
                    $sources= array();

                    $sql = "SELECT * FROM CPS3740.Sources";

                    // Get result or show error and die
                    $result = $conn->query($sql) or die($conn->error);

                    // Print out options dynamically from Sources Table
                    while($row=$result->fetch_assoc()){
                        $sources[$row["id"]]=$row["name"];
                    }

                    // ------------------------------------------------------------------------------

                    $id = $_COOKIE['customerID'];
                    $keyword = $_GET['keyword'];
                    $sql = "SELECT * FROM Customers WHERE id='$id'";    // Fetch User Info
                
                    $result = $conn->query($sql) or die($conn->error);

                    // Store User info
                    $row = $result->fetch_assoc();  //fetch data from database
                    $name = $row["name"];
                    
                    // print('<p><button type="button" onclick="logout()">Logout</button></p>');  // logout button
                    print ('<p><a href="javascript:logout()">Logout</a></p>'); // Logout Button 
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
                        print("<table class='highlight' style='width: 50%;'><tr><th>ID</th><th>Code</th><th>Type</th><th>Amount</th>
                        <th>Date Time</th><th>Note</th><th>Source</th></tr>");

                        // Print rows with data
                        while($row = $result->fetch_assoc()) {
                            if($row["type"] === 'D'){
                                $type='<td>Deposit</td><td style="color: blue;">';
                            }
                            else {
                                $type='<td>Withdraw</td><td style="color: red;">';
                            }
                            print("<tr><td>".$row["mid"]."</td><td>".$row["code"]."</td>".$type.
                            $row["amount"]."</td><td>".$row["mydatetime"]."</td><td>".$row["note"]."</td><td>".$sources[$row["sid"]]."</td></tr>"); 
                        }

                        echo"</table><br>";
                    }
                    else {
                        print("<p>No records found with the search keyword: <b>".$keyword."</b></p>");
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