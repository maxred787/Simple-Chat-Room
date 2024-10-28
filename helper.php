<?php
    define("DB_HOST", "mydb");
    define("USERNAME", "dummy");
    define("PASSWORD", "c3322b");
    define("DB_NAME", "db3322");

    function authenticate() {
        if (isset($_SESSION['user'])) { //if already authenticated
            return true;
        }
        if (isset($_POST['user']) && isset($_POST['password'])) {
            $user = $_POST['user'];
            $password = $_POST['password'];
            #Check username & password
            $result = validate_login($user, $password);
            if ($result['isValid']) {
                #Matched username & password
                $_SESSION['user'] = $user; //Store authenticated variable
                // session_write_close(); //free session lock
                return true;
            } else { //Wrong credential
                return false;
            }
        }

        return false;
    }

    function validate_login($useremail, $password) {
        $db_conn = mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME)
                    or die("Connection Error!".mysqli_connect_error());
        
        $sql = "SELECT * FROM account WHERE useremail = '$useremail' AND password = '$password'";

        $result = mysqli_query($db_conn, $sql) or die("<p>Query Error!<br>".mysqli_error($db_conn)."</p>");
        
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                return ['isValid' => true, 'msg' => ''];
            } else {
                $result = validate_email($useremail);
                
                if ($result) {
                    return ['isValid' => false, 'msg' => 'Failed to login. Incorrect password!!'];
                } else {
                    return ['isValid' => false, 'msg' => 'Failed to login. Unknown user!!'];
                }
            }
        }
    }

    function validate_email($useremail) {
        $db_conn=mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME) or die("Connection Error!".mysqli_connect_error());

        $sql = "SELECT * FROM account WHERE useremail='$useremail'";

        $result = mysqli_query($db_conn, $sql);

        if ($result) {
            return (mysqli_num_rows($result) > 0);
        }
    }

    function get_session_var() {
        if (isset($_SESSION['user'])) {
            $user = $_SESSION['user'];
            echo json_encode(array('user' => $user));
        }
    }

    if (isset($_GET['session'])) {
        session_start();
        get_session_var();
        // $ses = $_SESSION['session'];
        // echo '{"session": "$ses"}';
    }
?>