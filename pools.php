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
	sleep(60);
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
      <p>Use this form to change to your own mining accounts!  Pressing submit will take 60 seconds as the miner restarts with the new configuration.</p> 
	  <p><b>WARNING:</b> There is very little validation on these settings at the moment so make sure your settings are correct!</p>
	  <p>While I don't mind if you leave my details you will be mining 
	  using the MinePeon test account and any bitcoins generated will be concidered
	  a dontation to the MinePeon project.</p>

    </div><!-- /container -->
		<?php if($donation == 0) { ?>
		<div class="container">
			<fieldset>
				<legend>Plea</legend>
				<p>Please reconsider your decision to give back absolutely nothing to the project that is currently running your miners. A lot of time and effort has gone into making MinePeon what it is today and a small token of 15 minutes of your hash power would be greatly appreciated and will continue to fund the ongoing development and support of MinePeon.  </p>
				<p>It is such a small amount and well below the normal variance in bitcoin mining you will not even notice the difference. If you work it out for every 1 GH/s you have it is 0.00027 bitcoin a day, ask yourself, is that really too much to support MinePeon?  Are you really that cheap?</p>
				<p>Some of the features that I would like to include are;-</p>
				<ul>
					<li>TFT Display</li>
					<li>LCD Display</li>
					<li>Android app</li> 
					<li>iOS app</li>
					<li>Live Update</li>
					<li>SMS/Email Alerts</li>
					<li>Backup/Restore</li>
					<li>Cloud Control</li>
					<li>VPN Tunneling (DDOS Protection & Anonymity)</li>
				</ul>
				<p>Most of those new features cost money to setup and run, I would prefer not to have to make features avalible as 'paied for' addons but it all depends on you.</p>
				<p>Neil Fincham</p>
				<p>The MineForeman</p>
				<p>P.S. Every time you set donations to zero a kitten dies. <marquee direction="left" scrollamount="3" behavior="scroll" style="width: 60px; height: 15px; color: #ff0000; font-size: 11px; text-decoration: blink;">Kitten Killer!</marquee></p>
			</fieldset>
		</div>
		<?php } ?>

<?php

include('foot.php');