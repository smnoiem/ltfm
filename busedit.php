<?php
    include('header.php');
    include('server.php');
    if(isset($_SESSION['email'])){
    }
    else if(isset($_SESSION['email'])) //header('Location: admin.php');
?>
    <div class="loginbox">
        <div class="header">
        <h1>LTFM</h1>
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
