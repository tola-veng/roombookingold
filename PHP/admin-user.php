<?php
session_start();

require_once('dbhandler.php');
$db = new dbhandler('roombooking');
if($db->error!=''){
	// error occurred, exit
	echo $db->error;
	exit();
}

// restricted direct access
if( !isset($_SESSION['username']) || $_SESSION['usertype']!=1 ){
	header("location: index.php");
	exit();
}else{
	// check user in database
	$sql = 'SELECT user_id,username from tb_user where username="'.$_SESSION['username'].'"';
	$data = $db->selectQuery($sql);
	if(count($data)>0){
		$userid = $data[0]['user_id'];
		$username = $data[0]['username'];
	}else{
		header("location: index.php");
		exit();
	}
}
// end restricted access
?>

<?php
	// start program
	$msg = "";
	if(isset($_GET['action'])){
		// update database
		if($_GET['action']=='update' && !empty($_GET['username'])){
			// check duplicate
			$sql = 'select * from tb_user where username="'.$_GET['username'].'" AND user_id!="'.$_GET['userid'].'"';
			$data = $db->selectQuery($sql);
			if(count($data)>0){
				$msg = '<span class="text-danger">Duplicate Username '.$_GET['username'].'</span>';
			}else{
				// update
				$sql = 'Update tb_user set username="'.$_GET['username'].'"';
				$sql.= ',firstname="'.$_GET['firstname'].'"';
				$sql.= ',lastname="'.$_GET['lastname'].'"';
				$sql.= ',email="'.$_GET['email'].'"';
				$sql.= ' where user_id="'.$_GET['userid'].'"';
				$id = $db->updateQuery($sql);
				if($id===false ){
					$msg = '<span class="text-danger">Update failed</span>';
				}else{
					$msg = '<span class="text-success">User has been updated successfully</span>';
				}
			}
		}
		
		//delete from database
		if($_GET['action']=='delete' && !empty($_GET['id'])){
			$sql = "Delete from tb_user where user_id='".$_GET['id']."'";
			$db->deleteQuery($sql);
			if($db!==false){
				$db->deleteQuery("Delete from tb_member where user_id=".$_GET['id']);
				$db->deleteQuery("Delete from tb_group where created_by='".$_GET['id']."'");
				$db->deleteQuery("Delete from tb_booking where booked_by='".$_GET['id']."'");
				$msg = '<span class="text-success">User has been deleted successfully</span>';
			}
		}
	}// action
	
?>

<?php
// include header
include('header.php');
?>

<script type="text/javascript">
	function submitForm(){
		frm = document.getElementById("frmModalForm");
		var error = "";
		if(frm.username.value==""){
			error += "Input Username \n";
		}
		if(frm.firstname.value==""){
			error += "Input First Name \n";
		}
		if(frm.lastname.value==""){
			error += "Input Last Name \n";
		}
		if(frm.email.value==""){
			error += "Input Email \n";
		}
		
		if(error==""){
			frm.submit();
		}else{
			alert(error);
		}
	}
	
		
	function doEdit(id,uname,fname,lname,email){
		frm = document.getElementById("frmModalForm");
		frm.userid.value = id;
		frm.username.value = uname;
		frm.firstname.value = fname;
		frm.lastname.value = lname;
		frm.email.value = email;
		frm.action.value = "update";
		$('#frmModal').modal('show');
	}
	
	function doDelete(id){
		if(confirm("Are you sure to delete this user and data?")){
			location.href = "admin-user.php?action=delete&id="+id;
		}		
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="admin.php">Home</a></li>
		<li class="active">User</li>
	</ol>
	<br>
	<div>
		<?php
		if(!empty($msg)){
			echo $msg;
		}
		?>
	</div>
	<table class="table table-striped table-bordered table-hover"  style="width:95%">
		<tr>
			<th style="width:20%;">Username</th>
			<th style="width:20%;">First Name</th>
			<th style="width:20%;">Last Name</th>
			<th style="width:20%;">Email</th>
			<th style="width:15%;">&nbsp;</th>
		</tr>
		<?php
			// select all rooms
			$data = $db->selectQuery("Select * from tb_user order by username");
			foreach($data as $d){
				echo "<tr>";
				echo "<td>".$d['username']."</td>";
				echo "<td>".$d['firstname']."</td>";
				echo "<td>".$d['lastname']."</td>";
				echo "<td>".$d['email']."</td>";
				echo '<td style="text-align:right;">';
				echo '<a href="#" onclick="doEdit(\''.$d['user_id'].'\',\''.$d['username'].'\',\''.$d['firstname'].'\',\''.$d['lastname'].'\',\''.$d['email'].'\'); return false;" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; ';
				echo '<a href="#" onclick="doDelete(\''.$d['user_id'].'\'); return false;" title="Delete"><span class="glyphicon glyphicon-trash"></span></a> &nbsp; ';
				echo '</td>';
				echo "</tr>";
			}
		?>
	</table>

	<div style="width:95%;">
		<a href="admin.php" class="btn btn-default" style="float:right;">Close</a>
		<div style="clear:both;"></div>
	</div>
</div>

<!-- form modal-->
<div class="modal" id="frmModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="frmModalTitle">User</h4>
      </div>
      <div class="modal-body">
        <form id="frmModalForm" method="get" action="admin-user.php" class="form-horizontal">
			<div class="form-group">
				<label for="firstname" class="col-sm-3 control-label">Userame</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="username" name="username">
				</div>
			</div>
			<div class="form-group">
				<label for="firstname" class="col-sm-3 control-label">First Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="firstname" name="firstname">
				</div>
			</div>
			<div class="form-group">
				<label for="lastname" class="col-sm-3 control-label">Last Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="lastname" name="lastname">
				</div>
			</div>
			<div class="form-group">
				<label for="email" class="col-sm-3 control-label">Email</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="email" name="email">
				</div>
			</div>
			<input type="hidden" name="userid" value="">
			<input type="hidden" name="action" value="">
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="submitForm();">Save</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end of form modal -->

<!-- end of html -->

<?php
// include footer
include('footer.php');
?>