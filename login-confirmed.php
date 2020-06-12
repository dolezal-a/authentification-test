<h2>LOGED IN</h2>
<p>You are loged in as: <?php echo $_SESSION['firstname'] . ' ' . $_SESSION['lastname']; ?></p>
<p>
    <form action="logout.php" id="logout" mehtod="post">
        <button type="submit" name="submit-logout">Log out</button>
    </form>
</p>
