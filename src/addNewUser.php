<?php
	session_start();

	$user='root';
	$password='';
	$dbName='argument';
	$con = mysql_connect('localhost',$user) or die ('Could not connect: ' . mysql_error());
	mysql_select_db($dbName) or die('Unable to select database');	
	
	$username=$_POST['newUser'];
	$password=$_POST['newPassword'];
	
	
	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);
	$username = stripslashes($username);
	$password = stripslashes($password);
	
	$password=md5($password);
	
	$query="SELECT * FROM `user` WHERE username='$username'";
	$temp=mysql_query($query);
	
	$count=mysql_num_rows($temp);
	
	if($count==1){//found
		header("Location: index.php?register=true&found=true");
	}
	else{
		$query="INSERT INTO `user` (`username` ,`password`)VALUES ('$username', '$password')";
		$result=mysql_query($query);
		$_SESSION['username']=$username;
		header("Location: index.php");
	}
	
	?>