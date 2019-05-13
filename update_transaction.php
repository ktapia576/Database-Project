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
                $transCodes = $_POST['transCode'];
                
                $count = count($transCodes);

                for($i=0; $i<$count;$i++){
                    echo $transCodes[$i] . " ";
                }

            }
            else {
                die("<p>Must use update transaction form!</p>");
            }
        ?>
    </body>
</html>