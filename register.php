<?php

require_once('config.php');
require_once('lib/helper.php');
require "vendor/autoload.php";

use eftec\bladeone\BladeOne;

connect();

if(isset($_POST['register'])) {
	$phone = formatPhone($_POST['phone']);
	$email = trim($_POST['email']);
	$savedPassword = $_POST['password'];
	$formData = [
		'phone' => $phone,
		'email' => $email,
		'password' => $savedPassword,
	];
	if(validateForm($formData)) {
		// check password
		if(checkOTP($formData)) {
			// check cooldown
			if(checkAttempt($formData)) {
				if(checkEmail($formData)) {
					if(verifyOTP($formData)) {
						saveUser($formData);
						die('Welcome to SMSBump!');
					}
					else {
						$message = 'Incorrect OTP password.';
					}
				}
				else {
					$message = 'Email / phone doesnt match.';
				}
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
		$message = 'Please fill in all fields.';
	}
}

$blade = new BladeOne();

echo $blade->run("registration", [
	'email' => $email ?? null,
	'phone' => $phone ?? null,
	'password' => $savedPassword ?? null,
	'message' => $message ?? null,
]);