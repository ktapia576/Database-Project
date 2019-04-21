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
            if($_SERVER['REQUEST_METHOD'] == 'POST'){
                // Check if amount empty
                if(empty($_POST["transaction-amount"])){
                    die("You must enter an amount!");
                }

                if(empty($_POST["transaction-source"])){
                    die("You must select a Source!");
                }

                if(empty($_POST["transaction-type"])){
                    die("You must select a transaction type!");
                }

                $amount=$_POST["transaction-amount"];
                $type=$_POST["transaction-type"];
                $code=$_POST["transaction-code"];
                $source=$_POST["transaction-source"];
                $note=null;

                // Check if note was entered
                if(!empty($_POST["transaction-note"])){
                    $note=$_POST["transaction-note"];
                }

                // Check if number
                if(!is_numeric($amount)){   // TRUE if var_name is a number or a numeric string, FALSE otherwise.
                   die("You can only enter numerical values for amount!");
                }
                
                if($amount <= 0){
                    die("You have entered the amount: ".$amount." | That is less than 0.");
                }

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

                $sql = "SELECT SUM(amount) as balance FROM CPS3740_2019S.Money_tapiake WHERE cid='$id'";    // Get balance of User based on ID

                // Get result or show error and die
                $result = $conn->query($sql) or die($conn->error);
                $row = $result->fetch_assoc();
                
                $balance=$row['balance'];

                if($type == "W"){
                    if($amount <= $balance){  // Check balance before withdrawal  
                        $amount="-".$amount;
                        $sql="INSERT INTO CPS3740_2019S.Money_tapiake (code, cid, type, amount, mydatetime, note, sid)
                        VALUES ('$code', '$id', '$type', '$amount', NOW(), '$note', '$source')";

                        // Get result or show error and die
                        $result = $conn->query($sql) or die($conn->error);

                        print("<h1>Your Withdrawal of <b>".$amount."</b> was successful!");
                    }
                    else{
                        die("Your balance is lower than the withrawal amount!");
                    }
                }
                elseif($type == "D"){
                    $sql="INSERT INTO CPS3740_2019S.Money_tapiake (code, cid, type, amount, mydatetime, note, sid)
                    VALUES ('$code', '$id', '$type', '$amount', NOW(), '$note', '$source')";
                    
                    // Get result or show error and die
                    $result = $conn->query($sql) or die($conn->error);

                    print("<p>Your Deposit of <b>".$amount."</b> was successful!</p>");
                }
            }
            else {
                die("<p>Must use add transaction form!</p>");
            }
        ?>
    </body>
</html>