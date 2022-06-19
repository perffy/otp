<?php

require_once('config.php');
require_once('lib/helper.php');
require "vendor/autoload.php";

use eftec\bladeone\BladeOne;

connect();

if(isset($_POST['forgot'])) {
	$phone = formatPhone($_POST['phone']);
	$formData = [
		'phone' => $phone,
	];
	if(validatePhone($_POST['phone'])) {
		if(checkOTP($formData)) {
			if(checkAttempt($formData)) {
				$password = newVerification($phone);
				// I am showing here the password just to not look in the DB :)
				// redirect('register.php?password='.$password);
				redirect('register.php');
			}
			else {
				$message = 'You got 3 failures already. Please try again in '.COOLDOWN_TIME_SECS.' seconds.';
			}
		}
		else {
			$message = 'We cannot find such phone in our system.';
		}
	}
	else {
		$message = 'Incorrect phone length. ';
	}
}

$blade = new BladeOne();

echo $blade->run("password", [
	'phone' => $phone ?? null,
	'message' => $message ?? null,
]);