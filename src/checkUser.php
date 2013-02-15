<?php 

	session_start();

	$user='root';
	$password='';
	$dbName='argument';
	$con = mysql_connect('localhost',$user) or die ('Could not connect: ' . mysql_error());
	mysql_select_db($dbName) or die('Unable to select database');	
	
	$username=$_POST['username'];
	$password=$_POST['password'];

	$username = mysql_real_escape_string($username);
	$password = mysql_real_escape_string($password);
	
	$username = stripslashes($username);
	$password = stripslashes($password);

	// encrypt password
	$password = md5($password);

	
	$query="SELECT * FROM `user` WHERE username='$username' and password='$password'";
	$temp=mysql_query($query);
	
	$count=mysql_num_rows($temp);
	
	if($count==1){//found
		echo "correct";
		$_SESSION['username']=$username;
		header("Location: index.php");
	}
	else{
		header("location:index.php?login=true&wrong=true");
	}
?>
	
	