<?php
    include('header.php');
    include('server.php');
    if(isset($_SESSION['email'])){
        header('Location: company.php');
    }
    else if(isset($_SESSION['email'])) header('Location: admin.php');
    if(isset($_GET['logout'])){
        if($_GET['logout']=='true') session_destroy();
        header('Location: index.php');
    }
?>
    <div class="loginbox">
        <div class="header">
        <br>
        <br>
        <h2>Login</h2>
      </div>

      <form method="post" action="login.php">
        <?php include('errors.php'); ?>
        <div class="input-group">
            <label>Email:</label>
            <input type="text" name="email" >
        </div>
        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password">
        </div>
        <div class="input-group">
            <button type="submit" class="btn" name="login_user">Login</button>
        </div>
        <p>
            Not yet a member? <a href="registration.php">Sign up</a>
        </p>
      </form>
    </div>
</body>
</html>
