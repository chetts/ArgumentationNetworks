<?php

	$nodeList = new NodeList();	

	$nodeResult = mysql_query("SELECT `id` , `title` FROM node");

	$counter = 0;
	
	while($nodeRow = mysql_fetch_array($nodeResult)){
		
		//printf("$nodeRow[0] <br />");  
		$nodeList->newNode($nodeRow[0]);
		
		$query = mysql_query("SELECT * FROM `agrees` WHERE `nodeId` = '$nodeRow[0]'");
		$agreeCount = mysql_num_rows($query);
		//printf("agrees = $agreeCount <br />"); 
		$nodeList->getNode($counter)->setAgree($agreeCount);
			
		$query = mysql_query("SELECT * FROM `disagrees` WHERE `nodeId` = '$nodeRow[0]'");
		$disagreeCount = mysql_num_rows($query);
		//printf("Disagrees = $disagreeCount <br />");  
		$nodeList->getNode($counter)->setDisagree($disagreeCount);

		$attackResult = mysql_query("SELECT `AttackedById` , `weight` FROM attackedBy WHERE nodeId = $nodeRow[0]");
		while($attackRow = mysql_fetch_array($attackResult)) {
			//printf("Attacked By = $attackRow[0], Weight = $attackRow[2] <br />");  
			$nodeList->getNode($counter)->setAttackedBy($attackRow[0], $attackRow[1]);
		}
		//printf("<br />");
		$counter ++;
	}
	
	mysql_close();
	
	
	/*
	//test 
	$agree =  array('1','1');//,'1','56','19','45','0',);
    $disagree = array('0','0');//,'0','12','28','0','0',);
                
	//adds nodes, then inputs agree's and disagree
	for($i=0;$i<count($agree);$i++){
		$nodeList->newNode($i*10);
		$nodeList->getNode($i)->setAgree($agree[$i]);
		$nodeList->getNode($i)->setDisagree($disagree[$i]); 
	}
	
	//adds edges or 'attacks' to the graph
	$nodeList->getNode('0')->setAttackedBy('10','1');
	$nodeList->getNode('1')->setAttackedBy('0','1');
	/*$nodeList->getNode('0')->setAttackedBy('1','1');
	$nodeList->getNode('3')->setAttackedBy('40','1');
	$nodeList->getNode('0')->setAttackedBy('40','1');
	$nodeList->getNode('4')->setAttackedBy('0','1');
	$nodeList->getNode('6')->setAttackedBy('30','1');*/
	
	/*for($i=0;$i<$nodeList->getSize();$i++){
		echo "Array ".$i." : ".$nodeList->getNode($i)->getId()."<br />"	;
	}
	
	echo "<br />----------------------------------<br /><br />";
	echo "Attacks:<br />";
	
	$nodeList->getNodeArray();
	
	echo "<br />----------------------------------<br /><br />";*/

	ModifiedNewton::calculateStrength($nodeList);
	
	$sortedList = ArgMergeSort::MergeSort($nodeList);


    echo "Node Order: <br />";
        
    //prints sorted list to system
    for($i=0;$i<$sortedList->getSize();$i++){
        echo $i+'1'.": ".$sortedList->getNode($i)->getId()." with strength ".$sortedList->getNode($i)->getFinalValue()."<br />";
	}
	
	/**
	*
	*/
	class Node{
		private $Id;
		private $attackedBy = array();
		private $weight = array();
		private $finalValue = 0;
		private $agree;
		private $disagree;
		
		public function Node($Id){
			$this->Id = $Id;
		}
		/**
		 * Sets agree variable for the node
		 */
		public function setAgree($agree){
			$this->agree = $agree;
		}

		/**
		 * Sets disagree variable for the node
		 */
		public function setDisagree($disagree){
			$this->disagree = $disagree;
		}
		
		/**
		 * Returns agree
		 */
		public function getId(){
			return $this->Id;
		}
		
		/**
		 * Returns agree
		 */
		public function getAgree(){
			return $this->agree;
		}
		
		/**
		 * Returns disagree
		 */
		public function getDisagree(){
			return $this->disagree;
		}
		
		/**
		 * Adds an attacking node and strength to the attackedBy array
		 */
		public function setAttackedBy($attackingNode, $attackStrength){
			$this->attackedBy[] = $attackingNode;
			$this->weight[] = $attackStrength;
		}

		/**
		 * return the node Id at array location x
		 */
		public function getAttackedBy($x){
			return $this->attackedBy[$x];
		}
	 
		/**
		 * Returns the strength of the attack at array location x
		 */
		public function getWeight($x){
			return $this->weight[$x];   
		}
		
		/**
		 * Returns the number of attacks on the node
		 */
		public function getNumAttacks(){
			return count($this->attackedBy);
		}
		
		/**
		 * Returns the nodes finalValue
		 */
		public function getFinalValue(){
			return $this->finalValue;
		}
		
		/**
		 * Set's the final value of the node
		 */
		public function setFinalValue($finalValue){
			$this->finalValue = $finalValue;
		}
	}
	
	/**
	*
	*/
	class NodeList {
        private $nodeList = array();
        /**
         * Creates a new node with Id, Id and adds it to the end of the nodeList
         */
        public function newNode($Id){
            $this->nodeList[] = new Node($Id);
        }
        
        /**
         * Adds a node to the end of the array, used for sorting
         */
        public function addNode($node){
            $this->nodeList[] = $node;
        }
        
        /**
         * Returns the node at position x of nodeList
		*/
        public function getNode($x){
            return $this->nodeList[$x];
        }
        
        /**
         * Returns the size of the nodeList
         */
        public function getSize(){
            return count($this->nodeList);
        }
        
        /**
         * Removes Node x from the node array, used for sorting 
         */
        public function removeNode($x){
            unset($this->nodeList[$x]);
			$this->nodeList = array_values($this->nodeList);
        }
        
        // Returns a string containing the details of all the Nodes and attacking nodes of the array         
        public function getnodeArray(){
			for( $i=0;$i<count($this->nodeList);$i++){
				echo "Node: ";
				echo $this->nodeList[$i]->getId();
				echo " is attacked by ";
				if($this->nodeList[$i]->getNumAttacks()==0){
					echo "no other nodes";
				}
				else{
					for( $j=0;$j<$this->nodeList[$i]->getNumAttacks();$j++){
						echo "Node ";
						echo $this->nodeList[$i]->getAttackedBy($j);
						echo " with Strength: ";
						echo $this->nodeList[$i]->getWeight($j);
						echo ", ";
					}
				}
				echo "<br />";   
			}
		}
	}
	
	final class ModifiedNewton{	
	//var id= new Array(); //Used to keep track of the ID's associated with the array's (DB only)
        /**
         * Takes a NodeList as a parameter, uses a modified version of Newton's method to
         * calculate the finalValues of each node and set's each node in nodelist's finalValue
         * as this final value.
         */
        public static function calculateStrength($nodeList){
            //Find the support for the nodes. 
            //This an estimate number of users which is max(agree+disagree)
            $size = $nodeList->getSize();
            //[] position = new [size];
			
			static  $max = 0;
			static  $support = 0;
			static  $flag= false;
			static  $reset = 0;
			static  $k = 1;
			
			$diffFlag = false;
            $reset = 0;
			$difference = 0;
			$prevDiff = null;
			
            //Three arrays used to calculate the finalValue of each node
            $initialValue = array();  
            $finalValue =  array();
            $tempValue = array();
            $id = array();
            //Loop adds the agree and disagree of each node to find the support of this node
            //Then uses this support to calculate the maximum support of the nodelist
            for($i=0;$i<$size;$i++){
                    $max = $nodeList->getNode($i)->getAgree() + $nodeList->getNode($i)->getDisagree();
                    if($max > $support){
                            $support = $max;
                    }
            }
            //echo "support:".$support."/".$max."<br />";
            //Normalise the initial values and initialize final values for Newtons Method
            //This is done by using the equation - agree/support or agree/agree+2(disagree)+support where support is only used if agree + 2(disagree) < support
            for ($i=0;$i<$size;$i++){
					$id[$i] = $nodeList->getNode($i)->getId();
                    //(list.getNode(i).getAgree());
                    //System.out.println((double)list.getNode(i).getAgree()/support);
                    if($nodeList->getNode($i)->getAgree() == 0 || $support == 0){//stops error when dividing by 0
                            $initialValue[$i]=0;
                    }
                    else{
                            $initialValue[$i] = ($nodeList->getNode($i)->getAgree()/$support);
                    }
                    //initializes finalValue and initialValue as the initialValue
					//echo "initialValue".$i." = ".$initialValue[$i]."<br />";
                    $finalValue[$i] = $initialValue[$i];
            }
			
            //Use a modified version of newtons method to try and find acceptable final values for the nodes
            //We do this by solving the equations using the current final values (initialy the same as the initial values)
            //We then save the solution into a tempValues Array and compare this with the finalValues Array
            //If the two arrays are within a certain degree of difference then we can say that we have found an acceptable solution

			$equationString = "Equations:<br />";
			$equationString .= "Vf(x) = Final Value of x <br />";
			$equationString .= "Vi(x) = Initial Value of x (based on agree/disagree) <br />";
			$equationString .= "k = A high number between 0 and 1 used to force loop closure<br />"; 
			$equationString .= "str = The strength of the attack <br /><br />";
			for($i=0;$i<$size;$i++){
				$equationString.="Vf(".$nodeList->getNode($i)->getId().") = Vi(".$nodeList->getNode($i)->getId().")";
				for($j=0;$j<$nodeList->getNode($i)->getNumAttacks();$j++){
					$equationString .= " * ( k * ( 1- ( Vf( ".$nodeList->getNode($i)->getAttackedBy($j).") * str)))";
				}
				$equationString .= "<br/>";
			}
			echo $equationString;
			echo "<br />----------------------------------<br /><br />";
		
			
			while($flag == 0 && $reset < 1000000){//flag is used to check for difference between final and temp values, reset is used to stop infinate loop				
				//echo "Reset = ".$reset."<br />";
				$flag = 1;
				$reset++;
				
				if($diffFlag){
					$k = 0.999;
				}
					
				//Loop used to calculate current final value in newtons method.
				//Uses equation - initialValue of N * (for all attacks against n-) 1 - strength of attack * finalValue of attack  
				for($i=0;$i<$size;$i++){
					$value = $initialValue[$i];	
					$equations ="Vf(".$nodeList->getNode($i)->getId().") = ".$initialValue[$i];
					//echo "initialvalue ".$i." = ".$value;
					for($j=0;$j<$nodeList->getNode($i)->getNumAttacks();$j++){
							$strength = $nodeList->getNode($i)->getWeight($j);
							$index = array_search($nodeList->getNode($i)->getAttackedBy($j), $id);
							$value*=$k*(1-($strength * $finalValue[$index]));
							$equations .= " * ( ".$k." * ( 1- (".$finalValue[$index]." * ".$strength.")";
					}
					
					//$equations.="<br/>";
					//echo $equations;
					
					$tempValue[$i] = $value;
					$prevDiff = null;
					//echo "TempValue = ".$tempValue[$i]."<br />"."FinalValue = ".$finalValue[$i]."<br />";
					$difference = $finalValue[$i]-$tempValue[$i];
					//echo "Difference = ".$difference."<br />";
					//echo "Flag b4 = ".$flag."<br />";
					if(($difference > (-0.0001)) && ($difference <0.0001)){//compares previous finalValue with new finalValue
						 
					}
					else{
						$flag=0;
					}
					
					if((($difference - $prevDiff) < (-0.999)) || (($difference - $prevDiff) > 0.999)){
						$diffFlag = true;
					}
					$prevDiff = $difference;
					
					//echo "Flag after = ".$flag."<br />";
					//echo"<br />";
				}
				
				for($i=0;$i<$size;$i++){
					$finalValue[$i] = $tempValue[$i];
				}
				
			}
			//echo "Reset = ".$reset."<br />";
			
			//If loop has ended then FinalValues(x) is correct
			for($i=0;$i<$size;$i++){
				//list.getNode(i).setFinalValue(finalValue[i]);
				$nodeList->getNode($i)->setFinalValue($finalValue[$i]);//if newtons method has converged sets all nodes final value as finalValue
				//echo "final ".$i.": ".$finalValue[$i]."<br />";
			}   
		}
	}

	final class ArgMergeSort{
        /**
         * Sorts a node list using merge sort
         * @param nodeList
         * @return nodeList
         */
        public static function MergeSort($nodeList){
              
            //if the node lists size is less than or equal to 1, returns the nodeslist as sorted
            if($nodeList->getSize()<='1'){
                return $nodeList;
            }
			
            //creates new nodeLists and seperates current node list into a left side and right side
            $left = new NodeList();
            $right = new NodeList();
            $middle = round($nodeList->getSize()/2);
	
			for($i=0;$i<$middle;$i++){
                $left->addNode($nodeList->getNode($i));
            }
		
            for($i=$middle;$i<$nodeList->getSize();$i++){
                $right->addNode($nodeList->getNode($i));
            }
			
            //recursively sorts each list until the lists have been seperated into lists with only 1 node
            $left = self::MergeSort($left);
            $right = self::MergeSort($right); 
            
            //merges each of these single nodes
            return self::Merge($left, $right);
        }
        
        /**
         * Merges two node lists one node at a time using the finaValue of each node as comparison
         */
        private static function Merge($left, $right){
            $nodeList = new NodeList();
            
            while($left->getSize()>0 || $right->getSize()>0){
                if($left->getSize()>0 && $right->getSize()>0){
                    if($left->getNode(0)->getFinalValue()>=$right->getNode(0)->getFinalValue()){
                        $nodeList->addNode($left->getNode(0));
                        $left->removeNode(0);
                    }
                    else{
                        $nodeList->addNode($right->getNode(0));
                        $right->removeNode(0);
                    }
                }
                else if($left->getSize()>0){
                    $nodeList->addNode($left->getNode(0));
                    $left->removeNode(0);
                }
                else{
                    $nodeList->addNode($right->getNode(0));
                    $right->removeNode(0);
                }       
            }
            return $nodeList;
        } 
    }
	
	
?>