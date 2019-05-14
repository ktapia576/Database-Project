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
                $id = $_COOKIE['customerID'];
                $delete = $_POST['delete'];
                $note = $_POST['note'];
                $codes = $_POST['codes'];
                $numDeleted=0;
                $numUpdated=0;
                
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


                // ----------------- Delete transactions ---------------------------------------
                $count = count($delete);

                for($i=0; $i<$count;$i++){
                    // sql to delete a record
                    $sql = "DELETE FROM CPS3740_2019S.Money_tapiake WHERE code='$delete[$i]'";

                    if ($conn->query($sql) === TRUE) {
                      print("The Code <b>".$delete[$i]."</b> has been <b>deleted</b> from the database<br>");
                      $numDeleted++;
                    } else {
                     echo "Error deleting record " .$delete[$i]." : ". $conn->error;
                    }

                    //echo $delete[$i] . " ";
                }
                // ------------------------------------------------------------------------------

                // ------------------- Update transaction Note ----------------------------------

                $count = count($note);

                for($i=0; $i<$count;$i++){
                    $sql = "UPDATE CPS3740_2019S.Money_tapiake SET note='$note[$i]' WHERE code='$codes[$i]' AND note!='note[$i]'";

                    $conn->query($sql); // do query

                    // Check if update was successful
                    if ($conn->affected_rows > 0) {
                        print("The Note for code <b>".$codes[$i]."</b> has been <b>updated</b><br>");
                        $numUpdated++;
                    } elseif ($conn->affected_rows == 0) {
                        //do nothing, nothing updated
                    }
                    else {
                        echo "Error updating record " .$codes[$i]." : ". $conn->error;
                    }

                   // echo $codes[$i]." : ".$note[$i] . " ,";
                }
                // ------------------------------------------------------------------------------

                print("<br>-----------------------------------------------------");
                print("<br>Number of deleted records: <b>".$numDeleted."</b><br>");
                print("Number of updated records: <b>".$numUpdated."</b><br>");
            }
            else {
                die("<p>Must use update transaction form!</p>");
            }
        ?>
    </body>
</html>