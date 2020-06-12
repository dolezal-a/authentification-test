<?php
    session_start();

    $logedIn = false;
    $status = null;

    // Database handler
    include_once 'auth.config.php';

    /**
    * Creates a connection to a database.
    *
    * @param string $servername
    * @param string $dbUsername
    * @param string $dbPassword
    * @param string $dbName
    */
    function ConnectToDatabase($servername, $dbUsername, $dbPassword, $dbName)
    {
        $connection = mysqli_connect($servername, $dbUsername, $dbPassword, $dbName);
        if(!$connection)
        {
            die('DB ERROR: '.mysqli_connect_error());
        }
        return $connection;
    }


    // 1. Login
    if(isset($_POST['submit-login']))
    {
        $connection = ConnectToDatabase($servername, $dbUsername, $dbPassword, $dbName);

        $username = $_POST['username'] ?? null;
        $password = $_POST['password'] ?? null;

        // If another user is loged in, log out the loged in user.
        if ( isset( $_COOKIE[session_name()] ) )
        {
            setcookie( session_name(), '', time()-3600, '/');
        }
        session_regenerate_id(false);
        session_unset();


        $sql = 'SELECT userID, username, firstname, lastname, pass FROM users WHERE username=?';
        $stmt = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($stmt, $sql))
        {
            die('Die in mysqli_stmt_prepare().');
        }
        else
        {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultRows = mysqli_stmt_num_rows($stmt);

            if($resultRows != 1)
            {
                header('Location: index.php?error=login-error');
                mysqli_stmt_close($stmt);
                mysqli_close($connection);
                exit();
            }
            else
            {
                $userID;
                $username;
                $firstname;
                $lastname;
                $pwdHash;

                mysqli_stmt_bind_result($stmt, $userID, $username, $firstname, $lastname, $pwdHash);
                mysqli_stmt_fetch($stmt);
                if(password_verify($password, $pwdHash))
                {
                    $_SESSION['firstname'] = $firstname;
                    $_SESSION['lastname'] = $lastname;
                    $_SESSION['userID'] = $userID;
                    $_SESSION['username'] = $username;
                    header('Location: index.php?logedin=true');
                    mysqli_stmt_close($stmt);
                    mysqli_close($connection);
                    exit();
                }
                else
                {
                    header('Location: index.php?error=login-error');
                    mysqli_stmt_close($stmt);
                    mysqli_close($connection);
                    exit();
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
    }


    // 2. Signup
    if(isset($_POST['submit-signup']))
    {
        $connection = ConnectToDatabase($servername, $dbUsername, $dbPassword, $dbName);

        $username = $_POST['username'] ?? null;
        $firstName = $_POST['firstName'] ?? null;
        $lastName = $_POST['lastName'] ?? null;
        $email = $_POST['email'] ?? null;
        $password = $_POST['password'] ?? null;
        $passwordRepeat = $_POST['password-repeat'] ?? null;

        if(!preg_match('/^[a-zA-Z0-9]*$/', $username))
        {
            header('Location: index.php?error=invalid-username');
            exit();
        }

        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            header("Location: index.php?error=invalid-email");
            exit();
        }

        if(empty($password) || empty($passwordRepeat) || $password != $passwordRepeat)
        {
            header("Location: index.php?error=invalid-pwd");
            exit();
        }

        $sql = 'SELECT userID FROM users WHERE username=?';
        $stmt = mysqli_stmt_init($connection);
        if(!mysqli_stmt_prepare($stmt, $sql))
        {
            mysqli_stmt_close($stmt);
            mysqli_close($connection);
            die('Die in mysqli_stmt_prepare().');
        }
        else
        {
            mysqli_stmt_bind_param($stmt, 's', $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            $resultRows = mysqli_stmt_num_rows($stmt);

            if($resultRows > 0)
            {
                header('Location: index.php?error=user-exists');
                mysqli_stmt_close($stmt);
                mysqli_close($connection);
                exit();
            }
            else
            {
                $sql = 'INSERT INTO users (username, firstname, lastname, email, pass) VALUES (?, ?, ?, ?, ?)';
                $stmt = mysqli_stmt_init($connection);
                if(mysqli_stmt_prepare($stmt, $sql))
                {
                    $pwdHash = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, 'sssss', $username, $firstName, $lastName, $email, $pwdHash);
                    mysqli_stmt_execute($stmt);
                    $status = 'UserCreated';
                }
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($connection);
    }


    // Website
    include_once('header.php');

    if(!isset($_GET['logedin']))
    {
        include_once('home.php');
        if(isset($_SESSION['userID']))
        {
            include_once('login-confirmed.php');
        }
    }
    else
    {
        if(isset($_GET['logedin']) && $_GET['logedin'] == 'true')
        {
            include_once('login-confirmed.php');
        }
    }

    include_once('footer.php');
