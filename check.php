<?php
    session_start();
    include 'helper.php';
    
    $db_conn=mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME) or die("Connection Error!".mysqli_connect_error());
    
    if (isset($_GET['user'])) {
        $user = $_GET['user'];

        $result = validate_email($user);
        if ($result) {
            echo '{"result": true}';
            exit();
        } else {
            echo '{"result": false}';
            exit();
        }
    }
?>