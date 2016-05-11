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
	// start program
	$msg = "";
	
	// loading room details
	if(isset($_GET['action']) && $_GET['action']=='loaddetail'){
		$sql = 'Select booking_id,DATE_FORMAT(booking_start,"%H:%i") AS times from tb_booking where booking_date="'.$_GET['days'].'" and room_id="'.$_GET['room'].'"';
		$data = $db->selectQuery($sql);
		if(count($data)>0){
			echo json_encode(array('result'=>$data));
		}else{
			echo json_encode(array('result'=>''));
		}
		exit();
	}
	
	// delete from database
	if(isset($_GET['action']) && $_GET['action']=='delete' && !empty($_GET['id']) ){
		$sql = 'Delete from tb_booking where booking_id='.$_GET['id'];
		if($db->deleteQuery($sql)!=false){
			$msg = "<span class='text-success'>Room booking has been cancelled.</span>";
		}
		exit();
	}
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
	
	a.icon-delete{
		display : block;
		float : right;
		visibility : hidden;
	}
	a.icon-delete:hover{
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
	function doLoadDetail(d,r){
		$('#modalMain').modal({'backdrop':false});
		$('#modalBody').html('<img src="images/loading.gif" style="width:18px;" alt="img">&nbsp; Loading ...');
		$.ajax({
			'url':'bookinglist.php','method':'GET'
			,'data':{'action':'loaddetail','days':d,'room':r}
			,'success':function(data){
				try{
					jData = JSON.parse(data);
					if(jData.result){
						var html = '<table class="table table-bordered"><tr><th>Time</th><th></th></tr>';
						for(i=0; i<jData.result.length; i++){
							html+= '<tr><td>'+jData.result[i]['times']+'</td>';
							html+= '<td style="text-align:right;"><a href="#" onclick="doCancel(\''+jData.result[i]['booking_id']+'\',\''+d+'\',\''+r+'\'); return false;" title="Cancel"><span class="glyphicon glyphicon-trash"></span></a> &nbsp; </td>';
							html+= '</tr>';
						}
						html+= '</table>';
						$('#modalBody').html(html);
					}else{
						$('#modalBody').html('<span>Record has been removed. Reload page &nbsp; <a href="javascript:doReload();" title="Reload"><span class="glyphicon glyphicon-refresh"></span></a></span>');
					}
				}catch(ex){
					$('#modalBody').html('<span class="text-danger">Sorry, loading error.</span>');
				}
			}
		});
	}
	
	function doCancel(id,d,r){
		if(confirm('Are you sure you want to remove this booking?')){
			$('#modalBody').html('<img src="images/loading.gif" style="width:18px;" alt="img">&nbsp; Loading ...');
			$.ajax({
				'url':'bookinglist.php?action=delete&id='+id,'method':'GET'
				,'complete':function(data){
					doLoadDetail(d,r);
				}
			});
		}
	}
	
	function doReload(){
		location.reload(true);
	}
</script>

<!-- html -->
<div class="container">
	<ol class="breadcrumb">
		<li><a href="user.php">Home</a></li>
		<li class="active">Booking list</li>
	</ol>
	<br>
	
	<div>
		<?php
		if(!empty($msg)){
			echo $msg;
		}
		?>
	</div>
	
	<?php
	// select all record that this user's group have booked
	$sql = "SELECT DISTINCT booking_date,room_id,group_name,capacity FROM tb_booking AS b,tb_member AS m, tb_group as g WHERE b.group_id=m.group_id AND m.group_id AND m.group_id=g.group_id AND user_id=".$userid." ORDER BY booking_date,room_id";
	$data = $db->selectQuery($sql);
	if(count($data)==0){
		echo "You haven't booked any room yet.";
		echo "<br>Go to <a href='reservation.php'>room servation</a> to book your target room.";
	}else{
		?>
			<table class="table table-bordered table-hover">
				<tr>
					<th style="width:150px">Date</th>
					<th>Room</th>
					<th>Group</th>
					<th>Attendees</th>
				</tr>
		<?php
		foreach($data as $d){
			echo "<tr style='cursor: pointer;' onclick='doLoadDetail(\"".$d['booking_date']."\",\"".$d['room_id']."\")'>";
			echo "<td>".date('D d-m-Y',strtotime($d['booking_date']))."</td>";
			echo "<td>".$d['room_id']."</td>";
			echo "<td>".$d['group_name'].'</td>';
			echo "<td>".$d['capacity'].'</td>';
			echo "</tr>\n";
		}
		?>
			</table>
		<?php
	}
	?>
	
</div>

<!-- modal -->
<div class="modal" id="modalMain">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Booking details</h4>
      </div>
      <div id="modalBody" class="modal-body">
		Loading ...
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