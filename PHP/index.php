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
		if(frm.username.value!="" && frm.password.value!=""){
			return true;
		}
		document.getElementById('form-error').innerHTML = "Input User name and Password";
		return false;
	}
</script>

<!-- html -->
<div class="container">
	<form id="form-login" action="login.php" method="post" class="form-horizontal" onsubmit="return checkForm(this);">
		<div class="form-group">
			<div id="form-error" class="col-sm-offset-2 col-sm-10">
			<?php
				if(isset($_GET['error']))
					echo $_GET['error'];
			?>
			</div>
		</div>
		<div class="form-group">
			<label for="username" class="col-sm-2 control-label">User name</label>
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
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-default">Log in</button>
			</div>
		</div>
	</form>
	<br>
	<h3>Demo Login</h3>
	<div class="row">		
		<div class="col-xs-12 col-sm-6">
			<legend>Administrator</legend>
			Username : admin <br>
			Password : admin
		</div>
		<div class="col-xs-12 col-sm-6">
			<legend>User</legend>
			Username : demo <br>
			Password : demo
		</div>
	</div>
</div>
<!-- end of html -->

<?php
// include footer
include('footer.php');
?>