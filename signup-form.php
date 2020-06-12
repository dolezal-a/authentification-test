<?php include_once('header.php'); ?>
<h2>SIGNUP</h2>
<form action="index.php" id="signup" method="post">
    <label for="username">User</label><input type="text" id="username" name="username"><br>
    <label for="firstName">First Name</label><input type="text" id="firstName" name="firstName"><br>
    <label for="lastName">Last Name</label><input type="text" id="lastName" name="lastName"><br>
    <label for="email">Email Address</label><input type="email" id="email" name="email"><br>
    <label for="password">Password</label><input type="password" id="password" name="password"><br>
    <label for="password-repeat">Repeat Password</label><input type="password" id="password-repeat" name="password-repeat"><br>
    <button type="submit" name="submit-signup">Sign up</button>
</form>
<?php include_once('footer.php'); ?>
