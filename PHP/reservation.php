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
?>


<?php
// include header
include('header.php');
?>

<script type="text/javascript">
	function formCheck(frm){
		var error = "";
		if(frm.room.value==""){
			error+= "Select room \n";
		}
		if(frm.group.value==""){
			error+= "Select group \n";
		}
		if(frm.capacity.value=="" || parseInt(frm.capacity.value)<=0 || isNaN(frm.capacity.value) ){
			error+= "Input number of people \n";
		}else{
			// number of people can't be greater than capacity of room
			for(i=0; i<dataRoom.length; i++){
				if(dataRoom[i].room_id==frm.room.value){
					if(parseInt(frm.capacity.value) > parseInt(dataRoom[i].capacity)){
						error+= "Number of seats is limited to "+dataRoom[i].capacity;
					}
					break;
				}
			}
		}
		
		if(error!=""){
			alert(error);
			return false;
		}else{
			return true;
		}
	}
	
	// Angular Data for Room and Group
	<?php
		// select room
		$data = $db->selectQuery('SELECT * FROM `tb_room` order by room_id');
		echo 'var dataRoom='.json_encode($data).';';
		echo "\r\n";
		// select group
		$sql = 'SELECT g.group_id, group_name FROM tb_group as g join tb_member as m on g.group_id=m.group_id '
								.' where m.user_id ='. $userid
								.' order by group_name';
								
		$data = $db->selectQuery($sql);
		echo 'var dataGroup='.json_encode($data).';';
	?>
	
	angular.module('appBooking',[]).controller('ctrlBooking',function($scope){
			$scope.dataRoom = dataRoom;
			$scope.dataGroup = dataGroup;
	});
</script>
<!-- end of script -->

<!-- html -->	
<div class="container" ng-app="appBooking" ng-controller="ctrlBooking">
	<ol class="breadcrumb">
		<li><a href="user.php">Home</a></li>
		<li class="active">Reservation</li>
	</ol>
	<br>
	
	<form class="form-horizontal" action="booking.php" onsubmit="return formCheck(this);">
		<div class="form-group">
			<label for="room" class="col-sm-2 control-label">Room</label>
			<div class="col-sm-10">
				<select class="form-control" id="room" name="room">
					<option value="">Select room</option>
					<option ng-repeat="x in dataRoom" value="{{x.room_id}}">{{x.room_id + ' - ' + x.capacity + ' seats'}}</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="group" class="col-sm-2 control-label">Group</label>
			<div class="col-sm-10">
				<select class="form-control" id="group" name="group">
					<option value="">Select group</option>
					<option ng-repeat="x in dataGroup" value="{{x.group_id}}">{{x.group_name}}</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label for="capacity" class="col-sm-2 control-label">Number of people</label>
			<div class="col-sm-10">
				<input type="text" name="capacity" id="capacity" class="form-control" value="1">
			</div>
		</div>
		<div class="form-group">
			<div class="col-sm-offset-2 col-sm-10">
				<button type="submit" class="btn btn-primary">Select</button> &nbsp; &nbsp;
				<a href="user.php" class="btn btn-default">Close</a>
			</div>
		</div>
	</form>
</div>
<!-- end of html -->


<?php
// include footer
include('footer.php');
?>