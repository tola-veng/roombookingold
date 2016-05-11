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

?>

<?php
// include header
include('header.php');
?>

<style type="text/css">
	.control-item{
		display : block;
		text-align : center;
		vertical-align : middle;
		text-decoration : none;
		float : left;
		padding : 10px;
		margin : 20px;
		border : 1px solid #CCCCCC;
		border-radius : 8px;
		width : 120px;
		height : 120px;
		
	}
	.control-item:link, .control-item:active, .control-item:visited, .control-item:hover{
		text-decoration : none;
		color : #0000EE;
	}
	.control-item:hover{
		border : 1px solid #0000EE;
	}
	.control-item img{
		margin : 0 auto;
		margin-bottom : 10px;
		text-align : center;
		vertical-align : middle;
		border : none;
	}
</style>


<!-- html -->
<div class="container">
	<a href="bookinglist.php" title="Booking List" class="control-item">
		<img src="images/bookinglist.png" alt="booking list"><br>
		Booking List
	</a>
	
	<a href="reservation.php" title="Room reservation" class="control-item">
		<img src="images/calendar.png" alt="room booking"><br>
		<span>Room reservation</span>
	</a>
	
	<a href="usermember.php" title="Group" class="control-item">
		<img src="images/group.png" alt="room booking"><br>
		Group
	</a>
</div>
<!-- end of html -->

<?php
// include footer
include('footer.php');
?>