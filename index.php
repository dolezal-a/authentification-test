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

    // 3. Logout
    if(isset($_POST['submit-logout']))
    {
        session_unset();
        session_destroy();
        header('Location: index.php');
    }

    // Website
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8" >
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>PHP - Authentification Test</title>
        <!-- <link rel="stylesheet" href="style.css"> -->
        <style>
            label{
                display: inline-block;
                width: 10rem;
            }
        </style>
    </head>
    <body>
        <main>
<?php
    if(!isset($_GET['logedin']))
    {
?>
            <h2>LOGIN</h2>
            <form action ="index.php" id="login" method="post">
                <label for="username">User</label><input type="text" id="username" name="username"><br>
                <label for="password">Password</label><input type="password" id="password" name="password"><br>
                <button type="submit" name="submit-login">Login</button>
            </form>
            <hr>
            <h2>SIGNUP</h2>
            <form action="index.php" id="signup" method="post">
                <label for="username">User</label><input type="text" id="username" name="username"><br>
                <label for="firstName">First Name</label><input type="text" id="firstName" name="firstName"><br>
                <label for="lastName">Last Name</label><input type="text" id="lastName" name="lastName"><br>
                <label for="email">Email Address</label><input type="email" id="email" name="email"><br>
                <label for="password">Password</label><input type="password" id="password" name="password"><br>
                <label for="password-repeat">Repeat Password</label><input type="password" id="password-repeat" name="password-repeat"><br>
                <button type="submit" name="submit-signup">Signup</button>
            </form>
<?php
    }
    else
    {
        if(isset($_GET['logedin']) && $_GET['logedin'] == 'true')
        {
?>
            <p>You are loged in as: <?php echo $_SESSION['firstname'] . ' ' . $_SESSION['lastname']; ?></p>
            <p>
                <form action="index.php" id="logout" mehtod="post">
                    <button type="submit" name="submit-logout">Logout</button>
                </form>
            </p>
<?php
        }
    }
?>
        </main>
    </body>
</html>
