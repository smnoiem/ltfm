<?php
    include('header.php');
    include('server.php');
    if(isset($_SESSION['email'])){
        header('Location: index.php');
    }
?>
  <div class="header">
    <br><br>
  	<h2>Company Register</h2>
  </div>

  <form method="post" action="registration.php">
  	<?php include('errors.php'); ?>


  	<div class="input-group">
  	  <label>Email:</label>
  	  <input type="email" name="email" value="<?php echo $email; ?>">
  	</div>

  	<div class="input-group">
  	  <label>Name:</label>
  	  <input type="text" name="name" value="<?php echo $name; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Phone:</label>
  	  <input type="text" name="phone" value="<?php echo $phone; ?>">
  	</div>
  	<div class="input-group">
  	  <label>Password:</label>
  	  <input type="password" name="password_1">
  	</div>
  	<div class="input-group">
  	  <label>Confirm Password:</label>
  	  <input type="password" name="password_2">
  	</div>
  	<div class="input-group">
  	  <button type="submit" class="btn" name="reg_user">Register</button>
  	</div>
  	<p>
  		Already a member? <a href="login.php">Sign in</a>
  	</p>
  </form>
</body>
</html>
