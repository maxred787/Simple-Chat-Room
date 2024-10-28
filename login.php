<?php
    session_start();
    include 'helper.php';

    #Set the access counter
    if (isset($_SESSION['counter'])) {
        $_SESSION['counter'] += 1;
    } else {
        $_SESSION['counter'] = 1;
    }

    function start() {
        $db_conn = mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME)
                    or die("Connection Error!".mysqli_connect_error());

        $sql = "CREATE TABLE IF NOT EXISTS account(
            id SMALLINT NOT NULL PRIMARY KEY AUTO_INCREMENT,
            useremail VARCHAR(60) NOT NULL,
            password VARCHAR(50) NOT NULL
        )";

        if (!mysqli_query($db_conn, $sql)){
            echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
        }

        if (isset($_POST['login'])) {
            if (authenticate()) {
                header("location: chat.php");
            } else {
                $user = $_POST['user'];
                $password = $_POST['password'];
    
                $result = validate_login($user, $password);
                if (!$result['isValid']) {
                    display_login_form($result['msg']);
                }
            }
        } else if (isset($_POST['register'])) {
            register_account($_POST["user"], $_POST["password"]);
        } else if (isset($_GET['action']) && $_GET['action'] == 'signout') {
            logout();
        } else {
            if (authenticate()) {
                header("Location: chat.php");
            } else {
                display_login_form();
            }
        }
    }

    function register_account($userEmail, $password) {
        $db_conn = mysqli_connect(DB_HOST, USERNAME, PASSWORD, DB_NAME)
                    or die("Connection Error!".mysqli_connect_error());

        $result = validate_email($userEmail);

        if ($result) {
            display_login_form("Failed to register. Already registered before!!");
        } else {
            $sql = "INSERT INTO account VALUES (NULL, '$userEmail', '$password');";

            mysqli_query($db_conn, $sql);

            header("Location: ./chat.php");
        }

        return '';
    }

    function logout() {
        #set SESSION cookie to expire ==> delete cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(),'',time()-3600, '/');
        }
        session_unset();
        session_destroy();
        #Set redirection
        header('location: login.php');
    }

    function display_login_form($errMsg='') {
        ?>
        <!DOCTYPE html>
        <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <title>Assignment 3</title>
                <link rel='stylesheet' href='login.css'>
                <script src="jquery-3.7.1.min.js"></script>
                <script async src="login.js"></script>
            </head>
            <body>
                <h1>A Simple Chatroom Service</h1>
                <section id="inputSection">
                    <h2>Login to Chatroom</h2>
                    <form action="login.php" method="POST" id="loginForm">
                        <fieldset name="loginInfo">
                            <legend>Login</legend>
                            <label for="formEmail">Email:</label>
                            <input type="email" name="user" id="formEmail" pattern="[a-z0-9]+@connect.hku.hk" required>
                            <label for="formPassword">Password:</label>
                            <input type="password" name="password" id="formPassword" required>
                            <input type="submit" name="login" value="login" id="submitBtn">
                        </fieldset>
                    </form>
                    <p>Click <a href="" id="loginRegSwap">here</a> to register an account</p>
                    <p id="errMsg"><?php echo "$errMsg";?></p>
                </section>
            </body>
            
        </html>
        <?php
    }

    start();

?>
