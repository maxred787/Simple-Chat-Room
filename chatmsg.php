<?php
    session_start();
    include 'helper.php';

    if (authenticate()) {
        $sql = "CREATE TABLE IF NOT EXISTS `message` (
            `msgid` int NOT NULL AUTO_INCREMENT,
            `time` bigint NOT NULL,
            `message` varchar(250) NOT NULL,
            `person` varchar(20) NOT NULL,
            PRIMARY KEY (`msgid`)
        );";

        $db_conn = mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME)
                    or die("Connection Error!".mysqli_connect_error());

        if (!mysqli_query($db_conn, $sql)){
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
        }

        if (isset($_POST['message'])) {
            $msg = $_POST['message'];
            $time = time();
            $user = explode('@', $_SESSION['user'])[0];
            
            $sql = "INSERT INTO message VALUES (NULL, '$time', '$msg', '$user');";

            if (!mysqli_query($db_conn, $sql)){
                echo '{"result": false}';
            } else {
                $_SESSION['lastActionTime'] = $time;
                echo '{"result": true}';
            }
        } else {
            $user = explode('@', $_SESSION['user'])[0];
            $time = time()-3600;
            
            $res = array();
            $res['user'] = $user;

            $sql = "DELETE FROM message WHERE time < '$time';";
            $result = mysqli_query($db_conn, $sql);
            
            $sql = "SELECT * FROM message;";
            $result = mysqli_query($db_conn, $sql);
            
            $msgs = array();
            if ($result) {
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
                        $msgs[] = $row;
                    }
                }
            }
            $res['msgs'] = $msgs;

            if (isset($_SESSION['lastActionTime'])) {
                if (time() - $_SESSION['lastActionTime'] >= 119) {
                    #set SESSION cookie to expire ==> delete cookie
                    if (isset($_COOKIE[session_name()])) {
                        setcookie(session_name(),'',time()-3600, '/');
                    }
                    session_unset();
                    session_destroy();
                }
            } else {
                $_SESSION['lastActionTime'] = time();
            }

            echo json_encode($res);
        }
    } else {
        http_response_code(401);
    }

?>