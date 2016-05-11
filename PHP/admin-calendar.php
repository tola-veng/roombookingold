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


//start program

//handle action
if(isset($_GET['action']) && $_GET['action']=='loadbookedroom'){
	$sql = 'SELECT booking_id,room_id,group_name,notification FROM tb_booking,tb_group where tb_booking.group_id=tb_group.group_id and booking_date="'.$_GET['days'].'" and booking_start="'.$_GET['times'].'"';
	$data = $db->selectQuery($sql);
	if(count($data)>0){
		$jData = array('result'=>$data);
		echo json_encode($jData);
	}else{
		$jData = array('result'=>'');
		echo json_encode($jData);
	}
	exit();
}//load

if(isset($_GET['action']) && $_GET['action']=='updatenotification'){
	// send mail
	$subject = "Room booking notification";
	$body = "Just friendly remind you that your room has been booked successfully";
	$headers = "From: webmaster@example.com" . "\r\n";
	
	$sql = "SELECT DISTINCT email FROM tb_user AS u JOIN tb_member AS m ON u.user_id=m.user_id JOIN tb_booking AS b ON m.group_id=b.group_id";
	$sql.= " WHERE b.booking_id='".$_GET['id']."'";
	$data = $db->selectQuery($sql);
	if($data){
		foreach($data as $d){
			mail($d['email'],$subject,$body,$headers);
		}
	}
	//update table
	$sql = "Update tb_booking set notification=1 where booking_id='".$_GET['id']."'";
	$db->updateQuery($sql);
	exit();
}//cancelroom

if(isset($_GET['action']) && $_GET['action']=='cancelroom'){
	$sql = "Delete from tb_booking where booking_id='".$_GET['id']."'";
	$db->deleteQuery($sql);
	exit();
}//cancelroom

// generate day and time for html
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
	$days[$i] = clone $startday;
	$startday->add(new DateInterval('P1D'));
}

// select all booking in this week
$sql = "SELECT Distinct DATE_FORMAT(booking_date,'%d-%m-%Y') AS days, DATE_FORMAT(booking_start,'%H:%i') AS times FROM tb_booking ";
$sql.= " Where booking_date>='".$days[0]->format('Y-m-d')."' AND booking_date<='".$days[4]->format('Y-m-d')."'";
// filter
if(isset($_GET['roomid']) && !empty($_GET['roomid']) )
	$sql.= " AND room_id='".$_GET['roomid']."'";
if(isset($_GET['groupid']) && !empty($_GET['groupid']) )
	$sql.= " AND group_id='".$_GET['groupid']."'";
$sql.= " Order by days,times";
$dataBooking = $db->selectQuery($sql);
//echo $sql;
	
// include header
include('header.php');
?>

<style type="text/css">
	.floatLeft{
		float : left;
		font-weight : bold;
	}
	.floatRight{
		float: right;
		font-weight : bold;
	}
	.centerAlign{
		margin : 0 auto;
		text-align : center;
	}
	
	.table-zebra tr:nth-child(even) {
		background: #F5F5F5;
	}
	.table-zebra tr:nth-child(odd) {
		background: #FFFFFF;
	}
	
	.td-room-booked{
		background-color : #FFDDDD;
		cursor : pointer;
	}
	
	#msg{
		font-size : 11px;
		font-weight : bold;
		text-align : center;
	}
	a,a:link,a:visited,a:active,a:hover{
		text-decoration: none;
	}
</style>

<script type="text/javascript">
	function prevWeek(){
		var frm = document.getElementById('frmFilter');
		frm.currentweek.value--;
		frm.action.value = "";
		frm.submit();
	}
	function nextWeek(){
		var frm = document.getElementById('frmFilter');
		frm.currentweek.value++;
		frm.action.value = "";
		frm.submit();
	}
	function thisWeek(){
		var frm = document.getElementById('frmFilter');
		frm.currentweek.value = 0;
		frm.action.value = "";
		frm.submit();
	}

	// show room booked at that day and time
	function doShowDetail(days,times){
		$('#bookedRoom').modal({'backdrop':false});
		$('#bookedDetail').html('<tr><td colspan="4"><img src="images/loading.gif" style="width:18px;" alt="img">&nbsp; Loading ...</td></tr>');
		$.ajax({
			'url':'admin-calendar.php','method':'GET'
			,'data':{'action':'loadbookedroom','days':days,'times':times}
			,'success':function(data){
				//alert(data);
				try{
					jData = JSON.parse(data);
					if(jData.result!=''){
						var html = '';
						for(i=0; i<jData.result.length; i++){
							html+= '<tr><td>'+jData.result[i]['room_id']+'</td><td>'+jData.result[i]['group_name']+'</td>';
							if(jData.result[i]['notification']==1){
								html+='<td style="text-align:center;" title="sent"><span class="glyphicon glyphicon-ok"></span></td>';
							}else{
								html+='<td style="text-align:center;"><a href="#" onclick="doNotify(\''+jData.result[i]['booking_id']+'\',\''+days+'\',\''+times+'\');return false;" title="Send notification"><span class="glyphicon glyphicon-envelope"></span></a></td>';
							}
							html+= '<td style="text-align:right;"><a href="#" onclick="doCancel(\''+jData.result[i]['booking_id']+'\',\''+days+'\',\''+times+'\'); return false;" title="Cancel"><span class="glyphicon glyphicon-trash"></span></a> &nbsp; </td>';
							html+= '</tr>';
						}
						$('#bookedDetail').html(html);
					}else{
						$('#bookedDetail').html('<tr><td colspan="3">Record has been removed. Reload page</td><td style="text-align:center;"><a href="javascript:doReload();" title="Reload"> &nbsp; <span class="glyphicon glyphicon-refresh"></span> &nbsp; </a></td></tr>');	
					}
				}catch(ex){
					$('#bookedDetail').html('<tr><td colspan="4"><span class="text-danger">Loading error</span></td></tr>');
				}
			}
		});
	}//
	function doNotify(id,days,times){
		$.ajax({
			'url':'admin-calendar.php?action=updatenotification&id='+id,'method':'GET'
			,'complete':function(data){
				doShowDetail(days,times);
			}
		});
	}
	
	function doCancel(id,days,times){
		if(confirm('Are you sure you want to cancel this booking?')){
			$('#bookedDetail').html('<tr><td colspan="4"><img src="images/loading.gif" style="width:18px;" alt="img">&nbsp; Loading ...</td></tr>');
			$.ajax({
				'url':'admin-calendar.php?action=cancelroom&id='+id,'method':'GET'
				,'complete':function(data){
					doShowDetail(days,times);
				}
			});
		}
	}//

	function doReload(){
		location.reload(true);
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="admin.php">Home</a></li>
		<li class="active">Calendar</li>
	</ol>
	<br>
	<div class="centerAlign">
		<form class="form form-inline" id="frmFilter" name="frmFilter" method="get" action="admin-calendar.php">
			Filter by : 
			<select name="roomid" id="roomid" class="form-control" style="width:150px;">
				<option value="">All room</option>
				<?php
					$data = $db->selectQuery('Select * from tb_room order by room_id');
					foreach($data as $d){
						echo '<option value="'.$d['room_id'].'">'.$d['room_id'].'</option>';
					}
				?>
			</select> &nbsp;
			<select name="groupid" id="groupid" class="form-control" style="width:150px;">
				<option value="">All group</option>
				<?php
					$data = $db->selectQuery('Select * from tb_group order by group_name');
					foreach($data as $d){
						echo '<option value="'.$d['group_id'].'">'.$d['group_name'].'</option>';
					}
				?>
			</select> &nbsp;
			<button type="submit" class="btn btn-sm btn-default">Go</button>
			<input type="hidden" name="action" value="">
			<input type="hidden" name="id" value="0">
			<input type="hidden" name="currentweek" value="<?=(empty($_GET['currentweek'])?0:$_GET['currentweek']);?>">
		</form>
		<script type="text/javascript">
			<?php
			if(isset($_GET['roomid']) && !empty($_GET['roomid']) )
				echo "$('#roomid').val('".$_GET['roomid']."');";	
			if(isset($_GET['groupid']) && !empty($_GET['groupid']) )
				echo "$('#groupid').val('".$_GET['groupid']."');"	
			?>
		</script>
	</div>
	<br><br>
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
		if(count($dataBooking)<1){
			echo "<span>There is not room booking this week.</span>";
		}
		?>
	</div>
	<table class="table table-zebra table-bordered">
		<tr>
			<th>Time / Day</th>
			<th style="width: 17%;">Monday <br><?=$days[0]->format('d/m/Y');?></th>
			<th style="width: 17%;">Tuesday <br><?=$days[1]->format('d/m/Y');?></th>
			<th style="width: 17%;">Wednesday <br><?=$days[2]->format('d/m/Y');?></th>
			<th style="width: 17%;">Thursday <br><?=$days[3]->format('d/m/Y');?></th>
			<th style="width: 17%;">Friday <br><?=$days[4]->format('d/m/Y');?></th>
		</tr>
		<?php
			for($t=0; $t<count($times); $t++){
				?>
				<tr>
					<td><?=$times[$t];?></td>
				<?php
				foreach($days as $day){
					// check whether this day and time have booking or not
					$hasBooking = false;
					foreach($dataBooking as $booked){
						if($booked['days']==$day->format('d-m-Y') && $booked['times']==$times[$t]){
							$hasBooking = true;
							break;
						}
					}//for

					if($hasBooking){
						echo '<td class="td-room-booked" onclick="doShowDetail(\''.$day->format('Y-m-d').'\',\''.$booked['times'].'\');">view room-booked</td>';
					}else{
						echo '<td>&nbsp;</td>';
					}					
				}
				?>
				</tr>
				<?php
			}
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
		<a href="admin.php" class="btn btn-default" style="float:right;">Close</a>
		<div style="clear:both;"></div>
	</div>
</div>

<!-- modal -->
<div class="modal" id="bookedRoom">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Booking details</h4>
      </div>
      <div class="modal-body">
      	<table class="table table-bordered table-hover" style="width:80%;">
      		<tr>
      			<th>Room ID</th>
      			<th>Group Name</th>
				<th>Notification</th>
      			<th>&nbsp;</th>
      		</tr>
      		<tbody id="bookedDetail">
      		</tbody>
      	</table>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- end of modal -->

<!-- end of html -->

<?php
// include footer
include('footer.php');
?>