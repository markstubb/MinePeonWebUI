<?php


require('ssl.inc.php');
require('timezone.inc.php');
require('miner.inc.php');
require('settings.inc.php');

// read the miner config file
$minerConf = file_get_contents("/opt/minepeon/etc/miner.conf", true);

// decode the json
$data = json_decode($minerConf, true);


if (!empty($_POST['URL0']) and !empty($_POST['USER0']) and !empty($_POST['PASS0'])) {

	// unset the pools
	unset($data['pools']);
	
	// Construct pool 0
	$pool = array(
		"url" => $_POST['URL0'],
		"user" => $_POST['USER0'],
		"pass" => $_POST['PASS0'],
	);
	
	// Set pool 0
	$data['pools'][0] = $pool;
	
	// echo $_POST['URL0'] . $_POST['USER0'] . $_POST['PASS0'] ;

	if (!empty($_POST['URL1']) and !empty($_POST['USER1']) and !empty($_POST['PASS1'])) {

		// Construct pool 1
		$pool = array(
			"url" => $_POST['URL1'],
			"user" => $_POST['USER1'],
			"pass" => $_POST['PASS1'],
		);
	
		// Set pool 1
		$data['pools'][1] = $pool;
	
		// echo $_POST['URL1'] . $_POST['USER1'] . $_POST['PASS1'] ;
		
		if (!empty($_POST['URL2']) and !empty($_POST['USER2']) and !empty($_POST['PASS2'])) {
		
			// Construct pool 2
			$pool = array(
				"url" => $_POST['URL2'],
				"user" => $_POST['USER2'],
				"pass" => $_POST['PASS2'],
			);
	
			// Set pool 2
			$data['pools'][2] = $pool;
		
			// echo $_POST['URL2'] . $_POST['USER2'] . $_POST['PASS2'] ;
		
		}
	}
	
	// Recode into JSON and save
	file_put_contents("/opt/minepeon/etc/miner.conf", json_encode($data));
	cgminer("restart");
	sleep(10);
}

include('head.php');
include('menu.php');

?>
	<div>
		<?php if($donation == 0) { ?><embed height="0" width="0" src="inc/kitten.mp3"><?php } ?>
	</div>
    <div class="container">

      <h1>Mining Pools</h1>
	  <form name="input" action="/pools.php" method="post">
	  URL: <input type="text" value="<?php echo $data['pools'][0]['url']; ?>" name="URL0">
	  Username: <input type="text" value="<?php echo $data['pools'][0]['user']; ?>" name="USER0">
	  Password: <input type="text" value="<?php echo $data['pools'][0]['pass']; ?>" name="PASS0"><br>
	  URL: <input type="text" value="<?php echo $data['pools'][1]['url']; ?>" name="URL1">
	  Username: <input type="text" value="<?php echo $data['pools'][1]['user']; ?>" name="USER1">
	  Password: <input type="text" value="<?php echo $data['pools'][1]['pass']; ?>" name="PASS1"><br>
	  URL: <input type="text" value="<?php echo $data['pools'][2]['url']; ?>" name="URL2">
	  Username: <input type="text" value="<?php echo $data['pools'][2]['user']; ?>" name="USER2">
	  Password: <input type="text" value="<?php echo $data['pools'][2]['pass']; ?>" name="PASS2"><br>
	  <input type="submit" value="Submit">
	  </form>
      <p>Use this form to change to your own mining accounts!  Pressing submit will take 10 seconds as the miner restarts with the new configuration.</p> 
	  <p><b>WARNING:</b> There is very little validation on these settings at the moment so make sure your settings are correct!</p>
	  <p>While I don't mind if you leave my details you will be mining 
	  using the MinePeon test account and any bitcoins generated will be concidered
	  a dontation to the MinePeon project.</p>

    </div><!-- /container -->
		<?php if($donation == 0) { echo $plea; } ?>
<?php

include('foot.php');