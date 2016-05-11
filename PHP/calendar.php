<?php
session_start();
date_default_timezone_set('Australia/Melbourne');

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


?>

<style type="text/css">
	table tr:nth-child(even) {
		background: #F5F5F5;
	}
	table tr:nth-child(odd) {
		background: #FFFFFF;
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
	
	function prevWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value--;
		document.location.href = "calendar.php?currentweek="+frm.currentweek.value;
	}
	function nextWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value++;
		document.location.href = "calendar.php?currentweek="+frm.currentweek.value;
	}
	function thisWeek(){
		var frm = document.getElementById('formbooking');
		frm.currentweek.value=0;
		document.location.href = "calendar.php?currentweek="+frm.currentweek.value;
	}
</script>

<!-- html -->
<div class="container">
	<br>	
	<div class="centerAlign">
		<a href="javascript:thisWeek();">This week</a>
		<div class="floatLeft"><a href="javascript:prevWeek();">&lt;&lt; Previous week</a></div>
		<div class="floatRight"><a href="javascript:nextWeek();">Next week &gt;&gt;</a></div>
		<div class="clear" style="clear:both;"></div>
	</div>
	<br>
	<table style="width:90%">
		<!-- table header -->
		<tr>
			<th>Time / Day</th>
			<th style="width: 17%;">Monday <br><?=date('d/m/Y',strtotime($days[0]));?></th>
			<th style="width: 17%;">Tuesday <br><?=date('d/m/Y',strtotime($days[1]));?></th>
			<th style="width: 17%;">Wednesday <br><?=date('d/m/Y',strtotime($days[2]));?></th>
			<th style="width: 17%;">Thursday <br><?=date('d/m/Y',strtotime($days[3]));?></th>
			<th style="width: 17%;">Friday <br><?=date('d/m/Y',strtotime($days[4]));?></th>
		</tr>
		<!-- end table header -->
		<!-- table body -->
		<?php for($t=0; $t<count($times); $t++) : ?>
			<tr>
				<td><?=$times[$t];?></td>
				<?php for($d=0; $d<5; $d++) : ?>
					<td>&nbsp;</td>
				<?php endfor; ?>
			</tr>
		<?php endfor; ?>
		<!-- end table body -->
	</table>
	<br>
	<div class="centerAlign">
		<a href="javascript:thisWeek();">This week</a>
		<div class="floatLeft"><a href="javascript:prevWeek();">&lt;&lt; Previous week</a></div>
		<div class="floatRight"><a href="javascript:nextWeek();">Next week &gt;&gt;</a></div>
		<div class="clear" style="clear:both;"></div>
	</div>
	<br>
</div>



<form id="formbooking" name="formbooking" method="get">
	<input type="hidden" name="currentweek" value="<?=(empty($_GET['currentweek'])?0:$_GET['currentweek']);?>">
	<input type="hidden" name="action" value="book">
</form>
<!-- end of html -->