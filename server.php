<?php

// initializing variables
$name = "";
$email    = "";
$phone ="";
$errors = array();

// REGISTER USER
if (isset($_POST['reg_user'])) {
  // receive all input values from the form
  $name = mysqli_real_escape_string($conn, $_POST['name']);
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $phone = mysqli_real_escape_string($conn, $_POST['phone']);
  $password_1 = mysqli_real_escape_string($conn, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($conn, $_POST['password_2']);

  // form validation: ensure that the form is correctly filled ...
  // by adding (array_push()) corresponding error unto $errors array
  if (empty($name)) { array_push($errors, "Name is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($phone)) { array_push($errors, "Phone number is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  // first check the database to make sure
  // Company does not already exist with the same phone and/or email
  $account_check_query = "SELECT * FROM company WHERE Id='$phone' OR email='$email' LIMIT 1";
  $result = mysqli_query($conn, $account_check_query);
  $user = mysqli_fetch_assoc($result);

  if ($user) { // if company exist
    if ($user['phone'] === $phone) {
      array_push($errors, "Phone already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "Email already exists");
    }
  }

  // Finally, register user if there are no errors in the form
  if (count($errors) == 0) {
  	$password = md5($password_1);//encrypt the password before saving in the database

  	$query = "INSERT INTO company (name, email, phone, password)
  			  VALUES('$name', '$email', '$phone', '$password')";
  	mysqli_query($conn, $query);
  	$_SESSION['name'] = $name;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: index.php');
  }
}
if (isset($_POST['login_user'])) {
  $email = mysqli_real_escape_string($conn, $_POST['email']);
  $password = mysqli_real_escape_string($conn, $_POST['password']);

  if (empty($email)) {
  	array_push($errors, "Email is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }
  if (count($errors) == 0) {
  	$password = md5($password);
  	array_push($errors, "$password");
  	$query = "SELECT * FROM company WHERE email='$email' AND password='$password'";
  	$results = mysqli_query($conn, $query);
  	if (mysqli_num_rows($results) == 1) {
  	  $_SESSION['email'] = $email;
  	  $_SESSION['success'] = "You are now logged in";
  	}else {
  		array_push($errors, "Wrong email/password combination");
  	}
  }
}
?>
