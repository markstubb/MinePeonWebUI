<?php

require_once('Mail.php');

/**********************************

Generic functions theat may be called from many differant places

*/

function sendEmail($settings, $subject, $body) {

		$mailSettings = array(
				'host' => $settings['smtp']
			
				/* ,
				'auth' => true,
				'username' => $username,
				'password' => $password,
				'port' => '25'
				*/
			);
	
	
		$mail = Mail::factory("smtp", $mailSettings );

		$headers = array("From"=>$settings['email'], "Subject"=>$subject);
		$mail->send($settings['email'], $headers, $body);		

}