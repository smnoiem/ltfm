<?php
    session_start();
    $conn = mysqli_connect("localhost", "root", "bd7toRy5%", "ltfm");
	if(mysqli_connect_errno()){
	    echo"Problem occurred connecting DB!";
	}
?>
<html>
    <head>
        <title>Local Transport Fare Management</title>
    </head>
    <body style="margin:auto; max-width: 500px; border:0px solid teal;
          background-image: url(''); background-color: white">
        <br>
        <br>
        <p>Welcome to</p>
        <h1>
            Local Transport Fare Management
        </h1>
