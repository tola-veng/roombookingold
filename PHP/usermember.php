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
if( !isset($_SESSION['username'])){
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


// start program

// error handler
$error = '';
$success = '';

// update group member
if(isset($_POST['groupid'])){
	// remove all from table member
	$db->deleteQuery('Delete from tb_member where user_id='.$userid);
	
	// insert new to table member
	for($i=0; $i<count($_POST['groupid']); $i++){
		$sql = 'Insert into tb_member (group_id,user_id) values('.$_POST['groupid'][$i].','.$userid.')';
		$db->insertQuery($sql);
	}
	if($db->error!=""){
		$error = $db->error;
		$success = '';
	}else{
		$error = '';
		$success = 'Updated successfully';
	}
}


/* adding new group */
if( isset($_POST['action']) && $_POST['action']='addnewgroup' && !empty($_POST['groupname']) ){
	$groupname = $_POST['groupname'];

	// check duplicate
	$sql = "SELECT group_id from tb_group where group_name='$groupname' ";
	$data = $db->selectQuery($sql);
	if(count($data)>0){
		$error = 'This name of the group is existed.';
		$sucess = '';
	}else{
		$sql = 'Insert into tb_group (group_name,created_by) values("'.$groupname.'",'.$userid.')';
		$gId = $db->insertQuery($sql);
		if($gId!==false){
			$error = '';
			$success = 'Group successfully added.';
			// insert to member
			$sql = 'Insert into tb_member (group_id,user_id) values('.$gId.','.$userid.')';
			$db->insertQuery($sql);
		}else{
			$error = 'Cannot create group.';
			$sucess = '';
		}
	}
	// insert
}
/* end adding new group */

/* delete group */
if( isset($_GET['action']) && $_GET['action']='deletegroup' && !empty($_GET['groupid']) ){
	$sql = 'Delete from tb_group where group_id='.$_GET['groupid'].' and created_by='.$userid;
	$gId = $db->deleteQuery($sql);
	if($gId!==false && $gId>0){
		// delete from Member and booking
		$sql = 'Delete from tb_member where group_id='.$_GET['groupid'];
		$db->deleteQuery($sql);
		$db->deleteQuery("Delete from tb_booking where room_id='".$_GET['groupid']."'");
		$success = "The group has been deleted successfully.";
		$error = "";
	}else{
		$success = "";
		$error = "You can only delete the group which you created.";
	}
}	
/* end delete group */


?>

<?php
// include header
include('header.php');
?>

<style type="text/css">
	.table{
		width : 350px;
	}
	.btn-delete{
		display : block;
		visibility : hidden;
		float : right;
		color : #000000;
		text-decoration : none;
	}
	span.icon-delete{
		display : block;
		margin : 0 auto;
		width : 20px;
		width : 20px;
		height : 20px;
		line-height : 20px;
		padding : 0px;
		font-weight : bold;
		font-size : 22px;
		text-align : center;
		vertical-align : middle;
		color : #FFF;
		background : #FF0000;
		border : none;
		border-radius : 10px;
	}
</style>

<script type="text/javascript">
	function addNewGroup(){
		var frm = document.getElementById("form-newgroup");
		if(frm.groupname.value==""){
			frm.groupname.focus();
			alert("Input the name of new group");
			return false;
		}
		frm.submit();
	}
	
	function displayDelete(id){
		document.getElementById('btndelete'+id).style.visibility = 'visible';
	}
	function hideDelete(id){
		document.getElementById('btndelete'+id).style.visibility = 'hidden';
	}
	function deleteGroup(gid){
		if(confirm('Are you sure to delete this group?')){
			location.href = 'usermember.php?action=deletegroup&groupid='+gid;
		}
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="user.php">Home</a></li>
		<li class="active">Group</li>
	</ol>
	<br>
	<h3>Select your group</h3>
	<form id="form-login" action="usermember.php" method="post" class="form-horizontal">
		<?php
			if(isset($error) && $error!='')
				echo '<span class="text-danger">'.$error.'</span>';
			if(isset($success) && $success!='')
				echo '<span class="text-success">'.$success.'</span>';
		?>
		<table class="table table-striped table-bordered table-hover">
			<tr>
				<th>Select</th>
				<th>Group Name</th>
				<th style="text-align:right;">
					<button type="button" class="btn btn-default" data-toggle="modal" data-target="#newGroup">
						<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> new group
					</button>
				</th>
			</tr>
			<?php
				// select group that user belong to
				$sql = "SELECT group_id from tb_member where user_id=".$userid;
				$member = $db->selectQuery($sql);
				
				// select all group
				$sql = "SELECT group_id,group_name from tb_group order by group_name";
				$groups = $db->selectQuery($sql);
				for($i=0; $i<count($groups); $i++){
					// check the group if the user in it
					$checked = '';
					for($k=0; $k<count($member); $k++){
						if( $member[$k]['group_id']==$groups[$i]['group_id'] ){
							$checked = ' checked="checked" ';
							break;
						}
					}
					// generate html
					echo '<tr onmouseover="displayDelete('.$i.')" onmouseout="hideDelete('.$i.')">';
					echo '<td><input type="checkbox" name="groupid[]" '.$checked.' value="'.$groups[$i]['group_id'].'" id="'.$groups[$i]['group_id'].'"></td>';
					echo '<td colspan="2"><label for="'.$groups[$i]['group_id'].'">' .$groups[$i]['group_name'] .'</label>';
					echo '<a href="#" onclick="deleteGroup('.$groups[$i]['group_id'].'); return false;" id="btndelete'.$i.'" class="btn-delete"><span class="icon-delete">&times;</span></a>';
					echo '</td>';
					echo "</tr>";
				}
			?>
		</table>
		<button type="submit" class="btn btn-primary">Save</button> &nbsp;&nbsp; 
		<a href="user.php" class="btn btn-default">Close</a>
	</form>
</div>

<!-- create new group form modal-->
<div class="modal" id="newGroup">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">New group</h4>
      </div>
      <div class="modal-body">
        <form id="form-newgroup" method="post" action="usermember.php" class="form-horizontal">
			<div class="form-group">
				<label for="groupname" class="col-sm-2 control-label">Name</label>
					<div class="col-sm-10">
						<input type="text" class="form-control" id="groupname" name="groupname">
					</div>
			</div>
			<input type="hidden" name="action" value="addnewgroup">
		</form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" onclick="addNewGroup();">Add</button>
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end of create new group -->

<!-- end of html -->

<?php
// include footer
include('footer.php');
?>