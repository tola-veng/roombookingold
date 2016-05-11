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
		// insert to database
		if($_GET['action']=='insert' && !empty($_GET['roomid'])){
			// check duplicate
			$sql = 'select * from tb_room where room_id="'.$_GET['roomid'].'"';
			$data = $db->selectQuery($sql);
			if(count($data)>0){
				$msg = '<span class="text-danger">Duplicate Room ID '.$_GET['roomid'].'</span>';
			}else{
				// insert
				$sql = 'Insert into tb_room(room_id,capacity) values("'.$_GET['roomid'].'","'.$_GET['capacity'].'")';
				$id = $db->insertQuery($sql);
				if($id===false ){
					$msg = '<span class="text-danger">Room added failed</span>';
				}else{
					$msg = '<span class="text-success">Room has been inserted successfully</span>';
				}
			}
		}
		
		// update database
		if($_GET['action']=='update' && !empty($_GET['oldid'])){
			// check duplicate
			$sql = 'select * from tb_room where room_id="'.$_GET['roomid'].'" AND room_id!="'.$_GET['oldid'].'"';
			$data = $db->selectQuery($sql);
			if(count($data)>0){
				$msg = '<span class="text-danger">Duplicate Room ID '.$_GET['roomid'].'</span>';
			}else{
				// update
				$sql = 'Update tb_room set room_id="'.$_GET['roomid'].'", capacity="'.$_GET['capacity'].'" where room_id="'.$_GET['oldid'].'"';
				$id = $db->updateQuery($sql);
				if($id===false ){
					$msg = '<span class="text-danger">Room update failed</span>';
				}else{
					$msg = '<span class="text-success">Room has been updated successfully</span>';
				}
			}
		}
		
		//delete from database
		if($_GET['action']=='delete'){
			$sql = "Delete from tb_room where room_id='".$_GET['id']."'";
			$db->deleteQuery($sql);
			if($db!==false){
				$db->deleteQuery("Delete from tb_booking where room_id='".$_GET['id']."'");
				$msg = '<span class="text-success">Room has been deleted successfully</span>';
			}
		}
	}// action
?>


<script type="text/javascript">
	function submitForm(){
		frm = document.getElementById("frmModalForm");
		var error = "";
		if(frm.roomid.value==""){
			error += "Room ID is required \n";
		}
		if(frm.capacity.value==""){
			error += "Please input capacity \n";
		}else if(isNaN(frm.capacity.value)){
			error += "Capacity is number only \n";
		}
		if(error==""){
			frm.submit();
		}else{
			alert(error);
		}
	}
	
	function doAdd(){
		frm = document.getElementById("frmModalForm");
		frm.roomid.value = "";
		frm.capacity.value = "";
		frm.action.value = "insert";
		$('#frmModal').modal('show');
	}
	
	function doEdit(id,cap){
		frm = document.getElementById("frmModalForm");
		frm.oldid.value = id;
		frm.roomid.value = id;
		frm.capacity.value = cap;
		frm.action.value = "update";
		$('#frmModal').modal('show');
	}
	
	function doDelete(id){
		if(confirm("Are you sure to delete this room? \n All booking record will also be deleted!")){
			location.href = "admin-room.php?action=delete&id="+id;
		}		
	}
</script>


<?php
// include header
include('header.php');
?>


<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="admin.php">Home</a></li>
		<li class="active">Room</li>
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
			<th style="width:200px;">Room ID</th>
			<th style="width:200px;">Capacity</th>
			<th style="width:100px;"><button class="btn btn-default btn-sm" onclick="doAdd();"><span class="glyphicon glyphicon-plus"></span> &nbsp; Add room</button></th>
		</tr>
		<?php
			// select all rooms
			$data = $db->selectQuery("Select * from tb_room order by room_id");
			foreach($data as $d){
				echo "<tr>";
				echo "<td>".$d['room_id']."</td>";
				echo "<td>".$d['capacity']."</td>";
				echo '<td style="text-align:right;">';
				echo '<a href="#" onclick="doEdit(\''.$d['room_id'].'\',\''.$d['capacity'].'\'); return false;" title="Edit"><span class="glyphicon glyphicon-pencil"></span></a> &nbsp; ';
				echo '<a href="#" onclick="doDelete(\''.$d['room_id'].'\'); return false;" title="Delete"><span class="glyphicon glyphicon-trash"></span></a> &nbsp; ';
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
        <h4 class="modal-title" id="frmModalTitle">Room</h4>
      </div>
      <div class="modal-body">
        <form id="frmModalForm" method="get" action="admin-room.php" class="form-horizontal">
			<div class="form-group">
				<label for="roomid" class="col-sm-2 control-label">Roo ID</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="roomid" name="roomid">
				</div>
			</div>
			<div class="form-group">
				<label for="capacity" class="col-sm-2 control-label">Capacity</label>
				<div class="col-sm-10">
					<input type="text" class="form-control" id="capacity" name="capacity">
				</div>
			</div>
			<input type="hidden" name="oldid" value="">
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