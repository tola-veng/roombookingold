<?php
session_start();

require_once('dbhandler.php');
$db = new dbhandler('roombooking');
if($db->error!=''){
	// error occurred, exit
	echo $db->error;
	exit();
}

// error handler
$error = '';

// insert new user
// check if duplicate user name; else insert to database
if(isset($_POST['username']) && trim($_POST['username'])!='' ){
	$sql = "SELECT user_id from tb_user where username='".trim($_POST['username'])."'";
	$data = $db->selectQuery($sql);
	if(count($data)>0){
		$error = 'This username is already taken, please choose other';
	}else{
		// insert new user
		$sql = "INSERT INTO tb_user(username,firstname,lastname,email,password,type) values("
			.$db->quote($_POST['username']).","
			.$db->quote($_POST['firstname']).","
			.$db->quote($_POST['lastname']).","
			.$db->quote($_POST['email']).","
			.$db->quote(md5($_POST['password'])).","
			."0)";
		
		$db->insertQuery($sql);
		if($db->error!=''){
			//$error = $db->error;
			$error = "Unexpected error, please sign up again later";
		}else{
			// successful
			$_SESSION['username'] = $_POST['username'];
			$_SESSION['usertype'] = 0; // default is user
			header("location: usermember.php");
			exit();
		}
	}
}
?>


<?php
// include header
include('header.php');
?>

<style type="text/css">
	#form-login{
		width : 600px;
		margin: 0 auto;
	}
	#form-error{
		color: #ff0000;
		font-size : 11px;
	}
</style>
<script type="text/javascript">
	function checkForm(frm){
		var error = "";
		if(frm.firstname.value==""){
			error+= "Input First name \n";
		}
		if(frm.lastname.value==""){
			error+= "Input Last name \n";
		}
		if(frm.email.value==""){
			error+= "Input email \n";
		}
		if(frm.username.value==""){
			error+= "Username is required \n";
		}
		if(frm.password.value==""){
			error+= "Input password \n";
		}else if(frm.password.value!=frm.repassword.value){
			error+= "Retype password doesn't match \n";
		}
		if(error!=""){
			alert(error);
			document.getElementById('form-error').innerHTML = "All fields are required.";
			return false;
		}else{
			return true;
		}
	}
</script>

<!-- html -->
<div class="container">
	<h3>New user register form</h3>
	<form id="form-login" method="post" class="form-horizontal" onsubmit="return checkForm(this);">
		<div class="form-group">
			<div id="form-error" class="col-sm-offset-2 col-sm-10">
			<?php
				if(isset($error) && $error!='')
					echo $error;
			?>
			</div>
		</div>
		<div class="form-group">
			<label for="firstname" class="col-sm-2 control-label">First name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="firstname" name="firstname">
			</div>
		</div>
		<div class="form-group">
			<label for="lastname" class="col-sm-2 control-label">Last name</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="lastname" name="lastname">
			</div>
		</div>
		<div class="form-group">
			<label for="email" class="col-sm-2 control-label">Email</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="email" name="email">
			</div>
		</div>
		<div class="form-group">
			<label for="username" class="col-sm-2 control-label">Username</label>
			<div class="col-sm-10">
				<input type="text" class="form-control" id="username" name="username">
			</div>
		</div>
		<div class="form-group">
			<label for="password" class="col-sm-2 control-label">Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="password" name="password">
			</div>
		</div>
		<div class="form-group">
			<label for="repassword" class="col-sm-2 control-label">Retype Password</label>
			<div class="col-sm-10">
				<input type="password" class="form-control" id="repassword" name="repassword">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Sign Up</button>
			</div>
		</div>
	</form>
</div>
<!-- end of html -->

<?php
// include footer
include('footer.php');
?>