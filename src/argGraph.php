<?php
	$user='root';
	$password='';
	$dbName='argument';
	$con = mysql_connect('localhost',$user) or die ('Could not connect: ' . mysql_error());
	mysql_select_db($dbName) or die('Unable to select database');
	
	require_once 'Image/GraphViz.php';
	
	$gv = new Image_GraphViz(true,array(),'');
	
	$query = mysql_query("SELECT `id`, `username`, `title` FROM `node`");
		while($temp = mysql_fetch_assoc($query)){
			$colour = "red";
			if(isset($_SESSION['username'])){
				if(($_SESSION['username'])==$temp['username']){
					$colour = "blue";
				}
			}	
			$gv->addNode($temp['id'],array('URL'=>'index.php?view='.$temp['id'], 'label' => $temp['title']."<br />(".$temp['id'].")", 'shape' => 'box', 'color' => $colour));
		}
		
	$query = mysql_query("SELECT * FROM attackedBy");
		while($temp = mysql_fetch_assoc($query)){
			$gv->addEdge(array($temp['attackedById']=>$temp['nodeId']));
		}
	
	echo $gv->fetch();
?>