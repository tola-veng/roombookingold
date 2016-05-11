<?php
// test

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
		if($_GET['action']=='update' && !empty($_GET['groupname'])){
			// check duplicate
			$sql = 'select * from tb_group where group_name="'.$_GET['groupname'].'" AND group_id!="'.$_GET['groupid'].'"';
			$data = $db->selectQuery($sql);
			if(count($data)>0){
				$msg = '<span class="text-danger">Duplicate Group name '.$_GET['groupname'].'</span>';
			}else{
				// update
				$sql = 'Update tb_group set group_name="'.$_GET['groupname'].'" where group_id="'.$_GET['groupid'].'"';
				$id = $db->updateQuery($sql);
				if($id===false ){
					$msg = '<span class="text-danger">Group update failed</span>';
				}else{
					$msg = '<span class="text-success">Group has been updated successfully</span>';
				}
			}
		}
		
		//delete from database
		if($_GET['action']=='delete' && !empty($_GET['id'])){
			$sql = "Delete from tb_group where group_id='".$_GET['id']."'";
			$db->deleteQuery($sql);
			if($db!==false){
				$db->deleteQuery("Delete from tb_member where group_id=".$_GET['id']);
				$db->deleteQuery("Delete from tb_booking where group_id='".$_GET['id']."'");
				$msg = '<span class="text-success">Group has been deleted successfully</span>';
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
		if(frm.groupname.value==""){
			error += "Input group name \n";
		}
		if(frm.groupid.value==""){
			error += "Unexpected Error";
		}
		
		if(error==""){
			frm.submit();
		}else{
			alert(error);
		}
	}
	
		
	function doEdit(id,name){
		frm = document.getElementById("frmModalForm");
		frm.groupid.value = id;
		frm.groupname.value = name;
		frm.action.value = "update";
		$('#frmModal').modal('show');
	}
	
	function doDelete(id){
		if(confirm("Are you sure to delete this group? \nAll booking records will also be deleted!")){
			location.href = "admin-group.php?action=delete&id="+id;
		}		
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="admin.php">Home</a></li>
		<li class="active">Group</li>
	</ol>
	<br>
	<div>
		<?php
		if(!empty($msg)){
			echo $msg;
		}
		?>
	</div>
	<table class="table table-striped table-bordered table-hover"  style="width:500px;">
		<tr>
			<th style="width:200px;">Group Name</th>
			<th style="width:100px;">&nbsp;</th>
		</tr>
		<?php
			// select all rooms
			$data = $db->selectQuery("Select * from tb_group order by group_name");
			foreach($data as $d){
				echo "<tr>";
				echo "<td>".$d['group_name']."</td>";
				echo '<td style="text-align:right;">';
				echo '<a href="#" onclick="doEdit(\''.$d['group_id'].'\',\''.$d['group_name'].'\'); return false;" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; ';
				echo '<a href="#" onclick="doDelete(\''.$d['group_id'].'\'); return false;" title="Delete"><span class="glyphicon glyphicon-trash"></span></a> &nbsp; ';
				echo '</td>';
				echo "</tr>";
			}
		?>
	</table>

	<div style="width:500px;">
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
        <h4 class="modal-title" id="frmModalTitle">Group</h4>
      </div>
      <div class="modal-body">
        <form id="frmModalForm" method="get" action="admin-group.php" class="form-horizontal">
			<div class="form-group">
				<label for="groupname" class="col-sm-3 control-label">Group Name</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" id="groupname" name="groupname">
				</div>
			</div>
			
			<input type="hidden" name="groupid" value="">
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