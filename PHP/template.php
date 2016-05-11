<?php
session_start();

require_once('dbhandler.php');
$db = new dbhandler('roombooking');
if($db->error!=''){
	// error occurred, exit
	echo $db->error;
	exit();
}
?>


<?php
// include header
include('header.php');
?>


<!-- html -->
<div class="container">
	
</div>
<!-- end of html -->


<?php
// include footer
include('footer.php');
?>