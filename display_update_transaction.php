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
                $name =$_POST['customerName'];
                
                // print('<p><button type="button" onclick="logout()">Logout</button></p>');  // logout button
                print ('<p><a href="javascript:logout()">Logout</a></p>'); // Logout Button 
                print('<h3>Update Transactions</h3>');
                print('You can only update <b>Note</b> column.');
                
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

                // --------------------------------- Create table --------------------------------
                $sql = "SELECT mid, code, type, amount, mydatetime, note FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";

                // Get result or show error and die
                $result = $conn->query($sql) or die($conn->error);

                if($result->num_rows > 0) {   // Check if there are records found
                    // Print Header columns of table
                    echo "<table class='highlight' style='width: 50%;'><tr><th>ID</th><th>Code</th><th>Operation</th><th>Amount</th><th>Date Time</th><th>Note</th></tr>";

                    // Print rows with data
                    while($row = $result->fetch_assoc()) {
                        if($row["type"] === 'D'){
                            $type='<td>Deposit</td><td style="color: blue;">';
                        }
                        else {
                            $type='<td>Withdraw</td><td style="color: red;">';
                        }
                        print("<tr><td>".$row["mid"]."</td><td>".$row["code"]."</td>".$type.
                        $row["amount"]."</td><td>".$row["mydatetime"]."</td><td style='background-color: yellow;'>".$row["note"]."</td></tr>"); 
                    }

                    echo"</table><br>";
                }
                else {
                    print("There are no records found.<br>");
                }
                // -------------------------------------------------------------------------------
            }
            else{
                print("<p>You must log in to use this feature!</p>");
            }    
        ?>

    </body>
</html>