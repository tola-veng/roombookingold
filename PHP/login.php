<?php
session_start();

if( isset($_POST['username']) && isset($_POST['password']) ){
	$query = "Select username, password,type from tb_user where username='".$_POST['username']."'";
	
	require('dbhandler.php');
	$db = new dbhandler('roombooking');
	
	$data = $db->selectQuery($query);
	if($db->error!=""){
		header("location: index.php?error=Check Database ".urlencode($db->error));
		exit();
	}
	
	if(count($data)>0){
		if( strcmp($data[0]['password'],md5($_POST['password']))==0 ){
			$_SESSION['username'] = $data[0]['username'];
			$_SESSION['usertype'] = $data[0]['type'];

			if( (int)$data[0]['type']==1){
				header("location: admin.php");
			}else{
				header("location: user.php");
			}
		}else{
			header("location: index.php?error=".urlencode("Invalid username or password"));
			exit();
		}
	}else{
		header("location: index.php?error=".urlencode("Invalid username or password"));
		exit();
	}

}else{
	// no direct access
	header("location: index.php");
}
?>