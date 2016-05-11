<?php
session_start();
date_default_timezone_set('Australia/Melbourne');

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

// redirect to reservation if group and room are not selected
if(empty($_GET['group']) || empty($_GET['room']) ){
	header("location: reservation.php");
	exit();
}


// insert to database
if(isset($_GET['action']) && $_GET['action']=='book'){
	$isError = false;
	$dup = false;
	$bookingdate = new DateTime($_GET['bookingdate'].' '.$_GET['bookingtime']);
	// cannot booking in the past
	$today = new DateTime();
	if($bookingdate<$today){
		$msg = '<span class="text-danger">Sorry, you cannot book in the past date time.</span>';
		$isError = true;
	}
	
	// check, prevent duplicate booking	
	$sql = "Select * from tb_booking where room_id='".$_GET['room']."' and booking_date='".$bookingdate->format('Y-m-d')."' and booking_start='".$bookingdate->format('H:i')."'";
	$data = $db->selectQuery($sql);
	if(count($data)>0){
		$dup = true;
		$isError = true;
		//$msg = '<span class="text-success">This room have already booked on '.$bookingdate->format('d/m/Y').' at '.$bookingdate->format('H:i').'</span>';
	}
	// insert booking
	if($isError==false && $dup==false){
		$sql = "INSERT INTO tb_booking(group_id,room_id,capacity,booking_date,booking_start,booking_end,notification,booked_by)";
		$sql.= " VALUES(".$_GET['group'].",'"
			.$_GET['room']."',"
			.$_GET['capacity'].",'"
			.$bookingdate->format('Y-m-d')."','"
			.$bookingdate->format('H:i')."','"
			.$bookingdate->add(new DateInterval('PT1H'))->format('H:i')."',0,"
			.$userid
			.")";
		$id = $db->insertQuery($sql);
		if($id==false){
			$msg = '<span class="text-danger">Sorry, booking failed.</span>';
		}else{
			$msg = '<span class="text-success">Booking on '.$bookingdate->format('d/m/Y').' at '.$bookingdate->format('H:i').' successfully.</span>';
		}
	}
}
// end insert to database


// delete from database
if(isset($_GET['action']) && $_GET['action']=='delete' && !empty($_GET['bookingid']) ){
	$sql = "DELETE FROM tb_booking where booking_id='".$_GET['bookingid']."' AND group_id='".$_GET['group']."'";
	$id = $db->deleteQuery($sql);
	if($id!=false){
		$msg = '<span class="text-success">Room booking have been cancelled.</span>';
	}
}
// end delete from database

// generate for html

$times = array('08:30','09:30','10:30','11:30','12:30','13:30','14:30','15:30','16:30','17:30','18:30','19:30');
$days = array();

$today = new DateTime();
// if the week has changed, modify today
if(isset($_GET['currentweek']) && is_numeric($_GET['currentweek']) && $_GET['currentweek']!=0){
	$today->modify($_GET['currentweek'].' week');
}

$dayofweek = $today->format('w');
// make 7 is sunday
if($dayofweek==0)
	$dayofweek = 7;

// calculate date of the start day
$startday = $today;
$startday->sub(new DateInterval('P'.($dayofweek-1).'D'));

// date of the week, make 0 is monday
for($i=0; $i<5; $i++){
	$days[$i] = $startday->format('Y-m-d');
	$startday->add(new DateInterval('P1D'));	
}


// select all booking in this week
$sql = "SELECT booking_id,group_id,booked_by, DATE_FORMAT(booking_date,'%Y-%m-%d') AS days, DATE_FORMAT(booking_start,'%H:%i') AS times FROM tb_booking WHERE room_id='".$_GET['room']."'";
$sql.= " AND booking_date>='".$days[0]."' AND booking_date<='".$days[4]."'";
$dataBooking = $db->selectQuery($sql);
//print_r($dataBooking);
?>


<?php
// include header
include('header.php');
?>

<style type="text/css">
	table tr:nth-child(even) {
		background: #F5F5F5;
	}
	table tr:nth-child(odd) {
		background: #FFFFFF;
	}
	#msg{
		font-size : 11px;
		font-weight : bold;
		text-align : center;
	}
	
	.td-room-booked{
		background-color : #FFDDDD;
	}
	.td-room-booked a{
		visibility : hidden;
		float : right;
	}
	.td-room-booked:hover > a{
		visibility : visible;
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
	
	.td-room-vacant a{
		text-align : center;
		display : block;
		visibility : hidden;
	}
	.td-room-vacant:hover{
		background-color : #EEEEFF;
	}
	.td-room-vacant:hover > a, .td-room-vacant a:hover{
		visibility : visible;
	}
	.td-room-disabled{
	}
	.floatLeft{
		float : left;
		font-weight : bold;
	}
	.floatRight{
		float: right;
		font-weight : bold;
	}
	.centerAlign{
		text-align : center;
	}
</style>

<script type="text/javascript">
	function updateBooking(bookday,booktime){
		var bday = new Date(bookday);
		/* remove confirm
		if(confirm('You are booking on '+bday.getDate()+'/'+(bday.getMonth()+1)+'/'+bday.getFullYear()+' at '+booktime)){
			
		}
		*/
		var frm = document.getElementById('formbooking');
		frm.action.value = 'book';
		frm.bookingdate.value = bookday;
		frm.bookingtime.value = booktime;
		frm.submit();
	}// updateBooking
	
	function deleteBooking(id){
		if(confirm('Are you sure to cancel this booking?')){
			var frm = document.getElementById('formbooking');
			frm.action.value = 'delete';
			frm.bookingid.value = id;
			frm.submit();
		}
	}
	
	function prevWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value--;
		document.location.href = "booking.php?room="+frm.room.value+"&group="+frm.group.value+"&capacity="+frm.capacity.value+"&currentweek="+frm.currentweek.value;
	}
	function nextWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value++;
		document.location.href = "booking.php?room="+frm.room.value+"&group="+frm.group.value+"&capacity="+frm.capacity.value+"&currentweek="+frm.currentweek.value;
	}
	function thisWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value=0;
		document.location.href = "booking.php?room="+frm.room.value+"&group="+frm.group.value+"&capacity="+frm.capacity.value;
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="user.php">Home</a></li>
		<li><a href="reservation.php">Reservation</a></li>
		<li class="active">Booking</li>
	</ol>
	<br>
	
	<div class="centerAlign">
		<a href="javascript:thisWeek();">This week</a>
		<div class="floatLeft"><a href="javascript:prevWeek();">&lt;&lt; Previous week</a></div>
		<div class="floatRight"><a href="javascript:nextWeek();">Next week &gt;&gt;</a></div>
		<div class="clear" style="clear:both;"></div>
	</div>
	<br>
	<div id="msg">
		<?php
		if(!empty($msg)){
			echo $msg;
		}
		?>
	</div>
	<br>
	<table class="table table-bordered">
		<tr>
			<th>Time / Day</th>
			<th style="width: 17%;">Monday <br><?=date('d/m/Y',strtotime($days[0]));?></th>
			<th style="width: 17%;">Tuesday <br><?=date('d/m/Y',strtotime($days[1]));?></th>
			<th style="width: 17%;">Wednesday <br><?=date('d/m/Y',strtotime($days[2]));?></th>
			<th style="width: 17%;">Thursday <br><?=date('d/m/Y',strtotime($days[3]));?></th>
			<th style="width: 17%;">Friday <br><?=date('d/m/Y',strtotime($days[4]));?></th>
		</tr>
		<?php
			for($t=0; $t<count($times); $t++) :
		?>
			<tr>
				<td><?=$times[$t];?></td>
				<?php
				for($d=0; $d<5; $d++){
					$isBooked = false;
					// finding whether is booked or not
					for($f=0; $f<count($dataBooking); $f++){
						if($dataBooking[$f]['days']==$days[$d] && $dataBooking[$f]['times']==$times[$t]){
							$isBooked = true;
							break;
						}
					}
					if($isBooked){
						if($dataBooking[$f]['group_id']==$_GET['group']){ // is your group, you can delete it
							?>
							<td class="td-room-booked">
								You booked &nbsp; 
								<a href="#" title="cancel" onclick="deleteBooking('<?=$dataBooking[$f]['booking_id'];?>'); return false;">
									<span class="icon-delete">&times;</span>
								</a>								
							</td>
							<?php
						}else{
							?>
							<td class="td-room-booked">
								&nbsp;
							</td>
							<?php
						}
					}else{
						// not allow to book if the datetime in the past
						$today = new DateTime();
						$bookdate = new DateTime($days[$d].' '.$times[$t]);
						if($bookdate<$today){
							?>
							<td class="td-room-disabled">
								&nbsp;
							</td>
							<?php							
						}else{
							?>
							<td class="td-room-vacant">
								<a href="#" onclick="updateBooking('<?=$days[$d];?>','<?=$times[$t];?>'); return false;">
									<span class="glyphicon glyphicon-plus"></span>
								</a>
							</td>
							<?php
						}
					}
				}//days
				?>				
			</tr>
		<?php
			endfor;
		?>
	</table>
	<br>
	<div class="centerAlign">
		<a href="javascript:thisWeek();">This week</a>
		<div class="floatLeft"><a href="javascript:prevWeek();">&lt;&lt; Previous week</a></div>
		<div class="floatRight"><a href="javascript:nextWeek();">Next week &gt;&gt;</a></div>
		<div class="clear" style="clear:both;"></div>
	</div>
	<br>
	<div>
		<a href="user.php" class="btn btn-default" style="float:right;">Close</a>
		<div style="clear:both;"></div>
	</div>
</div>



<form id="formbooking" name="formbooking" method="get">
	<input type="hidden" name="room" value="<?=$_GET['room'];?>">
	<input type="hidden" name="group" value="<?=$_GET['group'];?>">
	<input type="hidden" name="capacity" value="<?=$_GET['capacity'];?>">
	<input type="hidden" name="bookingdate">
	<input type="hidden" name="bookingtime">
	<input type="hidden" name="bookingid">
	<input type="hidden" name="currentweek" value="<?=(empty($_GET['currentweek'])?0:$_GET['currentweek']);?>">
	<input type="hidden" name="action" value="book">
</form>
<!-- end of html -->


<?php
// include footer
include('footer.php');
?>