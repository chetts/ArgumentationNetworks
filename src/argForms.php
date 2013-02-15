<?php

	function outputForm($formUser,$formId,$formName,$formTitle,$formBody,$formAttacks,$formViewAttacks,$formAgrees,$formSubmit,$formEnd){
		echo "
		<form name='testform' action='index.php' method='post'>
			<table>
				<th colspan='2' align='left'>$formName</th><th>$formUser</th>
				<tr>
					<td>Title:</td>
					<td>$formTitle</td>
				</tr>
				<tr>
					<td valign='top'>Argument:</td>
					<td>$formBody</td>
				</tr>
				$formAttacks
				<tr>
					<td>Attacks:</td>
					<td id=attacks>$formViewAttacks</td>
					"; 
					if(isset($_REQUEST['update'])){		
						$query = mysql_query("SELECT nodeId FROM attackedby WHERE attackedById = $formId");
							while($temp = mysql_fetch_assoc($query)){
								echo "<script language=javascript>addAttack(".$temp['nodeId'].")</script>";
							}
					}
				echo "
				</tr>
				<tr>
					<td colspan='3'>$formSubmit $formAgrees</td>
				</form>	
				</tr>
				<tr>
				<td colspan='3'>$formEnd</td>
			</tr>
		</table>";
	}
	
	function loginForm(){
		echo "
		<form name='userForm' id='userForm' method='post' action='checkUser.php'>
			<table>
				<tr>
					<td>User Login</td>
				<tr>
					<td>UserName</td>
					<td><input name='username' id='username' type='text' value=''></td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input name='password' id='password' type='password'></td>
				</tr>
				<tr>
					<td><input type='submit' name='Submit' value='Login'  onclick='return testLogin()'></td>
		</form>
					<td><form action='index.php'><input type='submit' name='register' value='Register'></form></td>
				</tr>
				
			</table>";
			if(isset($_REQUEST['wrong'])){
				echo "incorrect username or password";
			}
	}
	
	function registerForm(){
		echo "
		<form name='newUserForm' method='post' action='addNewUser.php'>
			<table>
				<tr>
					<td>Register</td>
				<tr>
					<td>UserName</td>
					<td><input name='newUser' id='newUser' type='text' value=''</td>
				</tr>
				<tr>
					<td>Password</td>
					<td><input name='newPassword' id='newPassword' type='password'></td>
				</tr>
				<tr>
					<td><input type='submit' name='Submit' value='Register' onclick='return testRegistration()'></td>
				</tr>
			</table>
		</form>";
		if(isset($_REQUEST['found'])){
				echo 'Username Taken';
		}
	}
	
	if(isset($_REQUEST['delete'])){
			$deleteArg = $_REQUEST['delete'];
			$result="DELETE FROM `argument`.`node` WHERE `node`.`id` = $deleteArg";
			$result=mysql_query($result) or die("This is not a valid node");
			header("Location: index.php");
	}
	
	if(isset($_REQUEST['disagree'])){
		$disagreeArg = $_REQUEST['view'];
		$disagreeUser = $_SESSION['username'];
		$disagreeUser = addslashes($disagreeUser);
		
		if($_REQUEST['disagree']==0){
			$query = "INSERT INTO `disagrees` (`nodeId`,`username`) VALUES ('$disagreeArg', '$disagreeUser')";
			mysql_query($query) or die(header("Location: index.php"));
		}
		else{
	
			$query ="DELETE FROM `disagrees` WHERE `nodeId` = '$disagreeArg' AND `username` = '$disagreeUser'";
			mysql_query($query) or die(header("Location: index.php"));
		}
	}
		
	if(isset($_REQUEST['agree'])){
		$agreeArg = $_REQUEST['view'];
		$agreeUser = $_SESSION['username'];
		$agreeUser = addslashes($agreeUser);
		
		if($_REQUEST['agree']==0){
			$query = "INSERT INTO `agrees` (`nodeId`,`username`) VALUES ('$agreeArg', '$agreeUser')";
			mysql_query($query) or die(mysql_error());
		}
		else{
			
			$query="DELETE FROM `agrees` WHERE `nodeId` = '$agreeArg' AND `username` = '$agreeUser'";
			mysql_query($query) or die(mysql_error());
		}
	}

	if ((isset($_REQUEST['submit']))&&(isset($_SESSION['username']))) {
		//load all arguments from input form		
		$argUser = $_SESSION['username'];
		$argTitle = $_POST['argTitle'];
		$argBody = $_POST['argBody'];
		$argAttacks = $_POST['argArray'];
		$prevAttacks = $_POST['prevArray'];
		
		//if there is a node selected set node selected as current node to update
		if (isset($_POST['node'])) {
			$argId=$_POST['node'];	
			//UPDATE
			$currentNode = $argId;
			$query = "UPDATE node SET title = '$argTitle' WHERE id = '$currentNode'"; 
			mysql_query($query) or die ("Error in query: $query. ".mysql_error());

			$query = "UPDATE node SET body = '$argBody' WHERE id = '$currentNode'"; 
			mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			
			$prevArray = array();
			$attackArray = array();
			while(trim($prevAttacks)!=''){
					$prevPos = strpos($prevAttacks, '!');
					$prevAttackedBy = substr($prevAttacks, 0, $prevPos);
					$prevAttacks = substr($prevAttacks, $prevPos+1);
					$prevArray[] = $prevAttackedBy;
			}
			while(trim($argAttacks)!=''){
					$pos = strpos($argAttacks, '!');
					$attackedBy = substr($argAttacks, 0, $pos);
					$argAttacks = substr($argAttacks, $pos+1);
					$attackArray[] = $attackedBy;
			}
			for($i = 0; $i<count($prevArray); $i++){
			//echo $prevArray[$i];
			}
			//echo "<br/>";
			for($j = 0; $j<count($attackArray); $j++){
			//echo $attackArray[$j];
			}
			//echo "<br/>";
			for($i = 0; $i<count($prevArray); $i++) {
				$foundFlag = -1;
				for($j = 0; $j<count($attackArray); $j++){
					//echo $i.$prevArray[$i]."<br />";
					//echo $j.$attackArray[$j]."<br />";
					if($prevArray[$i] == $attackArray[$j]){
							$foundFlag = $j;
					}
					//echo $prevArray[$i]." compared to ".$attackArray[$j]." flag is ".$foundFlag."<br />";
				}	
				if($foundFlag >= 0){
					//echo "splice ".$attackArray[$foundFlag]."<br />";
					array_splice($attackArray,$foundFlag,1);
				}
				else{
					//echo "delete ".$prevArray[$i]." cur=".$currentNode."<br />";					
					$query = "DELETE FROM `attackedby` WHERE `nodeId` = '$prevArray[$i]' AND `attackedById` = '$currentNode'";
					mysql_query($query) or die ("Error in query: $query. ".mysql_error());
						
				}
			}
			for($i = 0; $i<count($attackArray); $i++){
				$query = "INSERT INTO attackedby VALUES('$attackArray[$i]','$currentNode','1')";
				mysql_query($query) or die ("Error in query: $query. ".mysql_error());
				//echo "add $i = ".$attackArray[$i]."<br />";
			}
			header("Location: index.php?view=$currentNode");
			
		}
		
		//If no node selected, add a new node
		
		else{
			$query = "INSERT INTO `node` (`username`,`title` ,`body`) VALUES ('$argUser','$argTitle', '$argBody')";
			mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			
			//use select max to find the node just added and set as current node
			$query = "SELECT MAX(id) FROM `node`";
			$temp = mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			$result = mysql_fetch_array($temp);
			$currentNode = $result['MAX(id)'];

			//add any attacks selected to the current node	
			$argOutput = " "; 
			while(trim($argAttacks)!=''){
				$pos = strpos($argAttacks, '!');
				$attackedBy = substr($argAttacks, 0, $pos);
				$argAttacks = substr($argAttacks, $pos+1);
				$argOutput .= $attackedBy.", ";			
				$query = "INSERT INTO attackedby VALUES('$attackedBy','$currentNode','1')";
				mysql_query($query) or die ("Error in query: $query. ".mysql_error());
			}
			header("Location: index.php?view=$currentNode");
		}
	}
	if(isset($_REQUEST['login'])){
		if(isset($_SESSION['username'])){
			header("Location: index.php");
		}
		else{
			loginForm();
		}
	}
	else if(isset($_REQUEST['register'])){
		if(isset($_SESSION['username'])){
			header("Location: index.php");
		}
		else{
			registerForm();
		}
		
	}
	else if (isset($_REQUEST['view'])){//View
		$formId = $_REQUEST['view'];
		$formName = "View Argument";
		
		$query = mysql_query("SELECT `username`,`title`, `body` FROM `node` WHERE `id` = '$formId'");
		$temp = mysql_fetch_assoc($query);

		$formUser = $temp['username'];
		$formTitle = "<input type='text' id='argTitle' name='argTitle' value='".$temp['title']."' size='25' maxlength='20' readonly='true'>(max 20 chars)";
		$formBody = "<textarea id='argBody' rows='5' cols='50' name='argBody' readonly='true'>".$temp['body']."</textarea>";
		
		$formAttacks = "";
		$formViewAttacks = "<table><tr>";
		
		$query = mysql_query("SELECT `nodeId` FROM `attackedBy` WHERE `attackedById` = '$formId'");
			while($temp = mysql_fetch_assoc($query)){
				 $formViewAttacks .= "<td width='30px'>".$temp['nodeId']."</td>";
			};
		$formViewAttacks .= "</tr></table>";
		
		$query = mysql_query("SELECT * FROM `agrees` WHERE `nodeId` = '$formId'");
		$agreeCount = mysql_num_rows($query);
		
		$query = mysql_query("SELECT * FROM `disagrees` WHERE `nodeId` = '$formId'");
		$disagreeCount = mysql_num_rows($query);

		$formAgrees = $agreeCount." agree  | ".$disagreeCount." disagree";
		
		$formSubmit = "";
		
		$formEnd = "
			<table>
				<tr>";
				if((isset($_SESSION['username']))){
					$sessionUser = $_SESSION['username'];

					$query=mysql_query("SELECT `username` FROM `agrees` WHERE `username` = '$sessionUser' AND `nodeId` = '$formId'");
					$agreeCount=mysql_num_rows($query);
					
					$query=mysql_query("SELECT `username` FROM `disagrees` WHERE `username` ='$sessionUser' and `nodeId` = '$formId'");
					$disagreeCount=mysql_num_rows($query);
					
					$formEnd .=" 
					<td>
						<form action='index.php' method='post'>
							<input type='hidden' name='view' value='".$formId."' />";
							if($agreeCount==1){//already agreed with argument
								$formEnd .="
								<input type='hidden' name='agree' value='1' />
								<input type='submit' value='Cancel Agree' />";
							}
							else{
								$formEnd .="
								<input type='hidden' name='agree' value='0' />
								<input type='submit' value='Agree' />";
							}
					$formEnd .=" 		
						</form>
					</td>
					<td>
						<form action='index.php' method='post'>
							<input type='hidden' name='view' value='".$formId."' />";
							if($disagreeCount==1){//already disagreed with argument
								$formEnd .="
								<input type='hidden' name='disagree' value='1' />
								<input type='submit' value='Cancel Disagree' />";
							}
							else{
								$formEnd .="
								<input type='hidden' name='disagree' value='0' />
								<input type='submit' value='Disagree' />";
							}
					$formEnd .="
						</form>
					</td>";
					if($sessionUser==$formUser){
						$formEnd .="
						<td>
							<form action='index.php' method='post'>
									<input type='hidden' name='delete' value='".$formId."' />
									<input type='submit' value='Delete' onclick='return deleteArg()' />
							</form>
						</td>
						<td>
							<form action='index.php' method='post'>
									<input type='hidden' name='update' value='".$formId."' />
									<input type='submit' value='Update' />
							</form>
						</td>";
					}
				}
				$formEnd .= "
					<td>
						<a href='index.php' >
							<button type='button' value='ExitView type='button'>Exit View</button>
						</a>
					</td>
				</tr>
			</table>";
			
		outputForm("Created by:".$formUser, $formId,$formName,$formTitle,$formBody,$formAttacks,$formViewAttacks,$formAgrees,$formSubmit,$formEnd);			
	}
	else if((isset($_REQUEST['update']))&& (isset($_SESSION['username']))){//Update
		$formUser = "";
		$formId = $_REQUEST['update'];
		
		$query = mysql_query("SELECT `title`, `body` FROM `node` WHERE `id` = '$formId'");
		$temp = mysql_fetch_assoc($query);
		
		$formName = "Update Argument<input type='hidden' name='node' value='".$formId."' />";
		$formTitle = "<input type='text' id='argTitle' name='argTitle' value='".$temp['title']."' size='30' maxlength='30'>(max 30 chars)";
		$formBody = "<textarea id='argBody' rows='5' cols='50' name='argBody'>".$temp['body']."</textarea>";
		
		//ADD NODES TO ATTACK
		$formAttacks ="<tr><td>Nodes to attack:</td><td><select id='attackList'>";
		$query = mysql_query("SELECT `id` FROM `node`");
					while($temp = mysql_fetch_assoc($query)){
						$formAttacks .= "<option value='".$temp['id']."'>".$temp['id']."</option>";
					}
		$formAttacks .= "</select>";
		$formAttacks .= "<input type='hidden' id='prevArray' name='prevArray'>";
		$formAttacks .= "<input type='hidden' id='argArray' name='argArray'>";
		$formViewAttacks = "";
		
		
		$formAttacks .=	"<button value='add attack' type='button' onclick='addAttack(-1)'>Add Attack</button></td>";
		
		
		$formAgrees = "";
		$formSubmit = "<input type='submit' name='submit' value='Submit' onclick='return testAddNode()'>";
		$formEnd = "";
		
		outputForm($formUser,$formId,$formName,$formTitle,$formBody,$formAttacks,$formViewAttacks,$formAgrees,$formSubmit,$formEnd);	
	}
	else if((isset($_SESSION['username']))&&(isset($_REQUEST['addNew']))){//Add new
		$formUser = "";
		$formId = ""; 
		$formName = "Add New Argument";
		$formTitle = "<input type='text' id='argTitle' name='argTitle' size='30' maxlength='30'>  (max 30 chars)";
		$formBody = "<textarea id='argBody' rows='5' cols='50' name='argBody'></textarea>";
		
		//ADD NODES TO ATTACK
		$formAttacks ="<tr><td>Nodes to attack:</td><td><select id='attackList'>";
		$query = mysql_query("SELECT `id` FROM `node`");
					while($temp = mysql_fetch_assoc($query)){
						$formAttacks .= "<option value='".$temp['id']."'>".$temp['id']."</option>";
					}
		$formAttacks .= "</select>";
		$formAttacks .= "<input type='hidden' id='prevArray' name='prevArray'>";
		$formAttacks .= "<input type='hidden' id='argArray' name='argArray'>";
		$formAttacks .=	"<button value='add node' type='button' onclick='addAttack(-1)'>Add node</button></td>";
		
		$formViewAttacks = "";
		$formAgrees = "";
		$formSubmit = "<input type='submit' name='submit' value='Submit' onclick='return testAddNode()'>";
		$formEnd = "";
		
		outputForm($formUser,$formId,$formName,$formTitle,$formBody,$formAttacks,$formViewAttacks,$formAgrees,$formSubmit,$formEnd);	
	}
	else{
		echo "
			<h2>Welcome to the argumentation network<br /><br /></h2>
			<p> This is a project for King's college. <br />
				To view node's please click on an argument in the graph. <br />
				To add new arguments you must be a member. <br /><br />
				Please click Login/Register to login or register as a new user. <br />
				Once you have logged in you may add new arguments <br />
				and update/delete previously added arguments. <br /><br />
				The nodes which belong to you are highlighted with a blue border. <br /><br />
				To calculate the strongest argument please click below <br /></p>
			";
	}
?>