<!DOCTYPE html>
<html lang="en" ng-app="Peon">
<head>
	<meta charset="utf-8">
	<title>MinePeon, from MineForeman</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="description" content="">
	<meta name="author" content="">

	<link href="css/bootstrap.min.css" rel="stylesheet">
	<link href="css/bootstrap-minepeon.css" rel="stylesheet">
</head>
<body>
	<div class="navbar">
		<div class="container">
			<a class="navbar-brand" href="http://mineforeman.com/minepeon/">MinePeon</a>
			<ul class="nav navbar-nav">
				<li><a href="/">Status</a></li>
				<li><a href="pools.php">Pools</a></li>
				<li><a href="settings.php">Settings</a></li>
				<li><a href="advanced.php">Advanced</a></li>
				<li><a href="miner.php">Miner</a></li>
				<li><a href="about.php">About</a></li>
				<li><a href="contact.php">Contact</a></li>
				<li><a href="license.php">License</a></li> 
			</ul>
		</div>
	</div>

  <div class="container">
    <div ng-view>ga
    </div>
  </div>

	<footer class="container">
		<hr />
		Server Time: ...
	</footer>

	<script src="js/ng/angular.min.js"></script> 
	<script src="js/ng/app.js"></script>
	<script src="js/ng/services.js"></script>
	<script src="js/ng/controllers.js"></script>
	<script src="js/ng/filters.js"></script>
	<script src="js/ng/directives.js"></script>
	<script src="js/ng/moment.min.js"></script>
</body>
</html>
