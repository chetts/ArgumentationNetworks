var attacks = new Array();

function deleteArg()
{ 
    return confirm("Are you sure you want to delete this argument?")
}

function agreeArg()
{ 
    return confirm("You are agreeing with this argument")
}

function disagreeArg()
{ 
    return confirm("You are disagreeing with this argument")
}

function testAddNode(){
	var e=document.forms["testform"]["argTitle"].value;
	if (e==null || e==""){
		alert("Title cannot be empty");
		return false;
	}

	var e=document.forms["testform"]["argBody"].value;
	if(e == "" || e == null){
		alert("Body cannot be empty");
		return false;
	}
	return true;
}

function testLogin(){
	var e = document.forms["userForm"]["username"].value;
	
	if (e == "" || e == null){
		alert("Username cannot be empty");
		return false;
	}

	var e = document.forms["userForm"]["password"].value;
	if(e == "" || e == null){
		alert("Password cannot be empty");
		return false;
	}
	return true;
}

function testRegistration(){
	var e=document.forms["newUserForm"]["newUser"].value;
	if (e==null || e==""){
		alert("Username cannot be empty");
		return false;
	}

	var e=document.forms["newUserForm"]["newPassword"].value;
	if(e == "" || e == null){
		alert("Password cannot be empty");
		return false;
	}
	return true;
}

function addAttack(x){
	if(x<0){
		var e = document.getElementById("attackList");
		var attack = e.options[e.selectedIndex].text;
	}
	else{
		attack = x;
	}
		var found = 0;
		
		for(i=0;i<attacks.length;i++){
			if(attacks[i] == attack){
				found = 1;
			}
		}
		if(found == 0){
			attacks.push(attack);
		}
		else{
			alert("Node already selected")
		}
		var output = "";
		var outArray = "";
		for(i=0;i<attacks.length;i++){
			output += "<label id = attackId" + attacks[i] + ">" + attacks[i] + "</label><button onclick='removeAttack(" + i + ")'>x</button>,  ";
			outArray += attacks[i] + "!";
		}
		document.getElementById("attacks").innerHTML = output;
		document.getElementById("argArray").value = outArray;
		if(x>0){
			document.getElementById("prevArray").value = outArray;
		}
}

function removeAttack(x){
	attacks.splice(x, 1);
	var output = "";
	var outArray = "";
	for(i=0;i<attacks.length;i++){
		output += "<label id = attackId" + attacks[i] + ">" + attacks[i] + "</label><button onclick='removeAttack(" + i + ")'>x</button>,  ";
		outArray += attacks[i] + "!";
	}
	document.getElementById("attacks").innerHTML = output;
	document.getElementById("argArray").value = outArray;
}