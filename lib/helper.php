<?php

global $db;

function connect()
{
	new MysqliDb ('localhost', DB_USER, DB_PASS, DB_NAME);
}

function checkOTP(array $data): bool
{
	$db = MysqliDb::getInstance();
	$user = $db->where('phone', $data['phone'])
		->getOne('passwords');

	return !empty($user);
}

function verifyOTP(array $data): bool
{
	$db = MysqliDb::getInstance();
	$user = $db->where('phone', $data['phone'])
		->where('password', $data['password'])
		->getOne('passwords');

	return !empty($user);
}

function checkEmail(array $data): bool
{
	$db = MysqliDb::getInstance();
	$users = $db->where('email', $data['email'])
		->orWhere('phone', $data['phone'])
		->get('users');

	$user = $db->where('email', $data['email'])
		->where('phone', $data['phone'])
		->getOne('users');

	if(count($users) == 0 || !empty($user)) {
		return true;
	}
	else {
		return false;
	}
}

function sendVerification(string $phone): string
{
	$db = MysqliDb::getInstance();
	$user = $db->where('phone', $phone)
		->getOne('passwords');

	if(empty($user)) {
		$password = generatePassword();
		$db->insert('passwords', [
			'phone'	=> $phone,
			'password' => $password,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}
	else {
		$password = $user['password'];
	}
	return $password;
}

function checkAttempt(array $data): bool
{
	$db = MysqliDb::getInstance();
	$pass = $db->where('phone', $data['phone'])
		->getOne('passwords');

	if(!empty($pass)) {
		logAttempt($data);
		$lastAttemptTimestamp = strtotime($pass['last_attemp_at']);
		// Check cooldown period
		if($lastAttemptTimestamp && $lastAttemptTimestamp + COOLDOWN_TIME_SECS <= time()) {
			// Reset attempts
			$db->where('id', $pass['id'])->update('passwords', ['attempts' => 0]);
			return true;
		}
		if($pass['attempts'] < MAX_ATTEMPTS) {
			return true;
		}
		else {
			return false;
		}
	}
	return true;
}

function newVerification(string $phone)
{
	$db = MysqliDb::getInstance();
	$pass = getPassword([
		'phone' => $phone
	]);

	if($pass) {
		$password = generatePassword();
		$db->where('id', $pass['id'])
			->update('passwords', [
				'password' => $password,
				'updated_at' => date('Y-m-d H:i:s')
			]);
	}

	return $password ?? '';
}

function saveUser(array $data)
{
	$db = MysqliDb::getInstance();
	$user = getUser($data);
	$pass = getPassword($data);

	if(!empty($user)) {
		$db->where('id', $user['id'])
			->update('users', [
				'updated_at' => date('Y-m-d H:i:s')
			]);
	}
	else {
		$db->insert('users', [
			'email' => $data['email'],
			'phone' => $data['phone'],
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s'),
		]);
	}

	// Reset attempts after login
	if(!empty($pass)) {
		$db->where('id', $pass['id'])->update('passwords', ['attempts' => 0]);
	}
}

function checkUser(array $data): bool
{
	$db = MysqliDb::getInstance();
	$users = $db->rawQuery('select id from users where email = ? or phone = ?', [
		$data['email'], $data['phone']
	]);
	return count($users) == 0;
}

function logAttempt(array $data)
{
	$db = MysqliDb::getInstance();
	$db->insert('attempts', [
		'phone' => $data['phone'],
		'password' => $data['password'] ?? null,
		'created_at' => date('Y-m-d H:i:s'),
		'updated_at' => date('Y-m-d H:i:s'),
	]);
	$password = getPassword($data);
	if(!empty($password)) {
		$saveData['last_attemp_at'] = date('Y-m-d H:i:s');
 		$saveData['updated_at'] = date('Y-m-d H:i:s');
		$saveData['attempts'] = $password['attempts'] + 1;
		$db->where('id', $password['id'])->update('passwords', $saveData);
	}
}

function getPassword(array $data): array
{
	$db = MysqliDb::getInstance();
	$pass = $db->where('phone', $data['phone'])
		->getOne('passwords');
	return $pass ?? [];
}

function getUser(array $data): array
{
	$db = MysqliDb::getInstance();
	$user = $db->where('phone', $data['phone'])->getOne('users');
	return $user ?? [];
}

function generatePassword()
{
	return rand(1000, 9999);
}

function formatPhone(string $phone): string
{
	$phone = trim($phone);
	$replacedPhone = preg_replace('/[^\dxX]/', '', $phone);
	$replacedPhone = strpos($replacedPhone, '0') === 0 ? substr($replacedPhone, 1) : $replacedPhone;
	return strpos($replacedPhone, COUNTRY_CODE) === 0 ? $replacedPhone : COUNTRY_CODE.$replacedPhone;
}

function validateEmail(string $email): bool
{
	return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone(string $phone): bool
{
	$phone = formatPhone($phone);
	$length = strlen(COUNTRY_CODE) + 9;
	return ($length == strlen($phone)) ? true : false;
}

function validateForm(array $data): bool
{
	return (validateEmail($data['email']) && validatePhone($data['phone']) && !empty($data['password']));
}

function redirect(string $url) {
	@header("HTTP/1.1 301 Moved Permanently");
	header('Location: '.$url);
}