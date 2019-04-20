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
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                if(!empty($_POST['login']) && !empty($_POST['password'])) {
                    $login = $_POST["login"];
                    $password = $_POST["password"];

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
            
                    $sql = "SELECT * FROM Customers WHERE login='$login' AND password='$password'";
            
                    $result = $conn->query($sql) or die($conn->error);
            
                    // If Successful login
                    if ($result->num_rows == 1) {
                        // print('<p><button type="button" onclick="logout()">Logout</button></p>');  // logout button
                        print ('<p><a href="javascript:logout()">Logout</a></p>'); // Logout Button 

                        // Fetch User info
                        $row = $result->fetch_assoc();  //fetch data from database
                        $id = $row["id"];
                        $name = $row["name"];
                        $ip = $_SERVER['REMOTE_ADDR'];  // Get ip address of client
                        $age = date_diff(date_create($row["DOB"]), date_create('now'))->y;

                        //Create Cookie
                        $cookie_name = "customerID";
                        $cookie_value = $id;
                        
                        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "./");

                        print("<b>Your IP:</b> ".$ip."<br>");

                        // Check if IP is from Kean
                        $arr = explode('.', $ip);

                        if($arr[0] == 10 || ($arr[0] == 131 && $arr[1] == 125)){
                            print("You <b>ARE</b> at Kean University.<br>");
                        }
                        else {
                            print("You are <b>NOT</b> from Kean University.<br>");
                        }               

                        print("<b>Welcome Customer:</b> ".$name."<br>");
                        print("<b>Age:</b> ".$age."<br>");
                        print("<b>Address:</b> ".$row["street"].", ".$row["city"].", ".$row["zipcode"]."<br>");
                        print("__________________________________________________________________________________<br>");

                        // --------------------------------- Create table --------------------------------
                        $sql = "SELECT mid, code, type, amount, mydatetime, note FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";

                        // Get result or show error and die
                        $result = $conn->query($sql) or die($conn->error);

                        if($result->num_rows > 0) {   // Check if there are records found
                            echo"<br>The transactions for customer ".$name." are:";
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
                                $row["amount"]."</td><td>".$row["mydatetime"]."</td><td>".$row["note"]."</td></tr>"); 
                            }

                            echo"</table><br>";
                        }
                        else {
                            print("There are no records found.<br>");
                        }
                        // -------------------------------------------------------------------------------

                        $sql = "SELECT SUM(amount) as balance FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";

                        // Get result or show error and die
                        $result = $conn->query($sql) or die($conn->error);
                        $row = $result->fetch_assoc();
                        
                        $balance=$row['balance'];
                        if($balance === NULL){
                            printf("<p>Total balance is <b>NULL</b>. May not have any transactional records</p>");
                        }
                        else {
                            printf("<b>Total balance:</b> %.2f",$balance);
                        }

                        // ------------------ 3 Functions (Add, Search, Update) ---------------------
                        print('<p><form method="POST" action="add_transaction.php">
                        <input type="hidden" name="customerName" value="$name">
                        <input type="submit" value="Add Transaction"></form></p>');
                        print('<a href="updateTranscation.php">Display and update transaction</a><br>');
                        print('<p><form method="GET" action="search.php">
                            Keyword: <input type="text" name="keyword" style="width: 20%"> <input type="submit" value="Search Transaction"> </form></p>');
                    }
                    else {
                        // Check if login exists or if password does not match
                        $sql = "SELECT * FROM Customers WHERE login='$login'";

                        $result = $conn->query($sql) or die($conn->error);

                        if($result->num_rows == 1){
                            die("The login ".strtolower($login)." exists, but password does not match");
                        }
                        else {
                            die("The login ".$login." doesn't exist in the database");
                        }
                    }
                }
                else {
                    die("<p>Login or Password Empty! Database connection not going to be established!</p>");
                }
            }
            else {
                die("<p>You must use login form!</p>");
            }
        ?>

    </body>
</html>