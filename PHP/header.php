<!DOCTYPE HTML>
<!--
	Room Booking Assessment
-->

<html>
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="Room Booking">
	<meta name="keywords" content="Room Booking">
	<meta name="author" content="">

	<title>Room Booking</title>
	
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	
	<!-- link to bootstrap external css file -->
	<link rel="stylesheet" href="css/bootstrap.min.css">

	<!-- link to our custom external css file to override bootstrap css -->
	<link rel="stylesheet" href="css/customStyles.css">

	<!-- jQuery (necessary for Bootstrap's JS plugins) -->
	<script src="js/jquery.min.js"></script>
	
	<!-- link to required Bootstrap JS files -->
	<script src="js/bootstrap.min.js"></script>
	
	<!-- link to Bootstrap Datetime picker -->
	<link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
	<script src="js/moment.min.js"></script>
	<script src="js/bootstrap-datetimepicker.min.js"></script>

	<!-- Link to AngualarJS -->
	<script src="js/angular.min.js"></script>
	
</head>

<body>
	<!-- header -->
	<header>
		<div class="container">
			<?php if(isset($_SESSION['username']) && $_SESSION['username']!=''): ?>
				<a href="logout.php" class="link-logout"><img src="images/logout.png" style="height:24px;"> &nbsp; Log out</a>
			<?php else: ?>
				<a href="userregister.php" class="link-logout"><img src="images/newuser.png" style="height:24px;"> &nbsp; New user</a>
			<?php endif; ?>
			
			<?php if(isset($_SESSION['usertype']) && (int)$_SESSION['usertype']==1): ?>
				<h2 id="header-heading"><a href="admin.php" title="home">Room Booking</a></h2>
			<?php else: ?>
				<h2 id="header-heading"><a href="user.php" title="home">Room Booking</a></h2>
			<?php endif; ?>
		</div>
		<div class="container">
			<div id="nav-main">
				<?php if(isset($_SESSION['usertype']) && (int)$_SESSION['usertype']==1): ?>
				<div class="dropdown">
					<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
						View as <?php echo (strpos($_SERVER['PHP_SELF'],'admin')>-1?'Admin':'User'); ?>
						<span class="caret"></span>
					</button>
					<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
						<li><a href="admin.php">Admin</a></li>
						<li><a href="user.php">User</a></li>
					</ul>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</header>
	<!-- end of header -->
	
	<!-- content -->	
	<section id="content">
		<div class="container">