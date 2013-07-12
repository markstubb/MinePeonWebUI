<?php


require('ssl.inc.php');
require('timezone.inc.php');
require('miner.inc.php');
require('settings.inc.php');

// set the number of extra empty rows for adding pools
$extraPools = 2;

// read the miner config file
$minerConf = file_get_contents("/opt/minepeon/etc/miner.conf", true);

// decode the json
$data = json_decode($minerConf, true);

// check whether there is POST data to handle
if (!empty($_POST)) {

	// unset the pools
	unset($data['pools']);

	// initialize the POST data counter
	$j = 0;
 
	//initialize a limit to the number of pools that are added to the miner config file. is there an official limit?
	$poolLimit = 20;

	// as long as the POST URL, USER and PASS data are present and the count is under the poolLimit, process the POST data
	while (!empty($_POST['URL'.$j.'']) and !empty($_POST['USER'.$j.'']) and !empty($_POST['PASS'.$j.'']) and $j<$poolLimit) {
     	
		// Construct pool at j
		$pool = array(
			"url" => $_POST['URL'.$j.''],
			"user" => $_POST['USER'.$j.''],
			"pass" => $_POST['PASS'.$j.''],
		);
	
		// Set pool at j
		$data['pools'][$j] = $pool;
	
		// debug output
		// echo $_POST['URL'.$j.''] . $_POST['USER'.$j.''] . $_POST['PASS'.$j.''];

		// increment count
		$j++;
	}

	// Recode into JSON and save
	if (!empty($data['pools'])) {
		file_put_contents("/opt/minepeon/etc/miner.conf", json_encode($data));
		cgminer("quit");
		sleep(10);
	}
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
		<?php
 
		// set the number of populated pools
		$countOfPools = count($data['pools']);
 
		for ($i = 0; $i < $countOfPools; $i++) {
			if (!empty($data['pools'][$i]['url']) and !empty($data['pools'][$i]['user']) and !empty($data['pools'][$i]['pass'])) { ?>
			  URL: <input type="text" value="<?php echo $data['pools'][$i]['url']; ?>" name="URL<?php echo $i; ?>">
			  Username: <input type="text" value="<?php echo $data['pools'][$i]['user']; ?>" name="USER<?php echo $i; ?>">
			  Password: <input type="text" value="<?php echo $data['pools'][$i]['pass']; ?>" name="PASS<?php echo $i; ?>"><br>
		  <?php }
               }
 
               //output extra empty rows to accomodate adding more pools
               for ($k = $countOfPools; $k < $countOfPools+$extraPools; $k++) {?>
                     URL: <input type="text" value="" name="URL<?php echo $k; ?>">
                     Username: <input type="text" value="" name="USER<?php echo $k; ?>">
                     Password: <input type="text" value="" name="PASS<?php echo $k; ?>"><br>
               <?php } ?>
	  <input type="submit" value="Submit">
	  </form>
      <p>Use this form to change to your own mining accounts!  Pressing submit will take 10 seconds as the miner restarts with the new configuration.</p> 
	  <p><b>WARNING:</b> There is very little validation on these settings at the moment so make sure your settings are correct!</p>
	  <p>While I don't mind if you leave my details you will be mining 
	  using the MinePeon test account and any bitcoins generated will be considered
	  a dontation to the MinePeon project.</p>

    </div><!-- /container -->
		<?php if($donation == 0) { echo $plea; } ?>
<?php include('foot.php'); ?>
