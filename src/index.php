<?php
	session_start();
?>
<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=UTF-8'></meta>
        <title>Argumentation Network</title>
		<link rel='stylesheet' type='text/css' href='argNetwork.css' />
		<script type='text/javascript' src='argNetwork.js'></script>

    </head>	
    <body>	
			<div id='frame'>
				<div id='argNav'>
					<ul id="navlist">
						<li><a href="index.php">Home</a></li>				
					
					<?php	
							if(isset($_SESSION['username'])){
								echo "<li><a href='index.php?addNew'>Add New</a></li>";
								echo "<li><a href='logout.php'>Logout</a></li>";
							}
							else{
								echo "<li><a href='index.php?login'>Login/Register</a></li>";
							}
					?>
					
					</ul>
					
				</div>
				<div id='argGraph'>
					<?php	
						
						$user='root';
						$password='';
						$dbName='argument';
						$con = mysql_connect('localhost',$user) or die ('Could not connect: ' . mysql_error());
						mysql_select_db($dbName) or die('Unable to select database');	
						include('argGraph.php');
					?>
				</div>
			</div>
			<div id='inOut'>
				<div id='input'>
					<?php
						include('argForms.php');
					?>	
				</div>
				
				<div id='output'>
				<?php
				
					if(isset($_REQUEST['calculate'])){
						include('argNetwork.php');
					}
					else{
							echo "<form action='index.php' method='post'>
										<input type='hidden' name='calculate' value='true' />
										<input type='submit' value='calculate' />
								</form>";
					}
				?>	
				
				</div>
			</div>
		</div> 
    </body>
</html>