<?php include_once('header.php'); ?>
<h2>LOGIN</h2>
<form action ="index.php" id="login" method="post">
    <label for="username">User</label><input type="text" id="username" name="username"><br>
    <label for="password">Password</label><input type="password" id="password" name="password"><br>
    <button type="submit" name="submit-login">Log in</button>
</form>
<hr>
<?php include_once('footer.php'); ?>
