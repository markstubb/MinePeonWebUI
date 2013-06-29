<?php

$uptime = explode(' ', exec("cat /proc/uptime"));

$version = file_get_contents("/opt/minepeon/etc/version");

$donation = file_get_contents("/opt/minepeon/etc/donation");

$plea = '
		<div class="container">
			<fieldset>
				<legend>Plea</legend>
				<p>Please reconsider your decision to give back absolutely nothing to the project that is currently running your miners. A lot of time and effort has gone into making MinePeon what it is today and a small token of 15 minutes of your hash power would be greatly appreciated and will continue to fund the ongoing development and support of MinePeon.  </p>
				<p>It is such a small amount and well below the normal variance in bitcoin mining you will not even notice the difference. If you work it out for every 1 GH/s you have it is 0.00027 bitcoin a day, ask yourself, is that really too much to support MinePeon?</p>
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
				<p>Most of those new features cost money to setup and run, I would prefer not to have to make features avalible as "paied for" addons but it all depends on you.</p>
				<p>Neil Fincham</p>
				<p>The MineForeman</p>
				<p>P.S. Every time you set donations to zero a kitten dies. <marquee direction="left" scrollamount="3" behavior="scroll" style="width: 60px; height: 15px; color: #ff0000; font-size: 11px; text-decoration: blink;">Kitten Killer!</marquee></p>
			</fieldset>
		</div>';