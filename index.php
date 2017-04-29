<?php

include 'db.php';
require 'Slim/Slim.php';

define('USER_CREATED_SUCCESSFULLY', 0);
define('USER_CREATE_FAILED', 1);
define('USER_ALREADY_EXISTED', 2);
header('Content-Type: multipart/form-data');
\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim();

$app->post('/login', 'login');

function login() {
	$apps = \Slim\Slim::getInstance();
	$json = array();
	$request = $apps->request();
	$body = $request->post();
	$sql = "SELECT * from users WHERE username=:username";
	$db = getDB();
	$stmt = $db->prepare($sql);
	$stmt->bindParam("username", $body['username']);
	$stmt->execute();
	$existUser = $stmt->fetchAll(PDO::FETCH_OBJ);
	if(count($existUser)) {
		$sql = "SELECT * from users WHERE username=:username AND password=:password";
		$stmt = $db->prepare($sql);
		$passwordHash = md5($body['password']);
		$stmt->bindParam("username", $body['username']);
		$stmt->bindParam("password", $passwordHash);
		$stmt->execute();
		$validUser = $stmt->fetchAll(PDO::FETCH_OBJ);
		if(count($validUser)) {
			$sql = "UPDATE users SET device_id=:deviceId where id=:userId";
			$stmt = $db->prepare($sql);
			$stmt->bindParam("deviceId", $body['deviceId']);
			$stmt->bindParam("userId", $validUser[0]->id);
			$stmt->execute();
			$json["success"] = 1;
			$json["data"] = array("userId" => $validUser[0]->id);
			$json["msg"] = "User credentials are valid!";
		} else {
			$json["success"] = 0;
			$json["msg"] = "Invalid credentials!";
		}
	} else {
		//Insert user
		$sql = "INSERT INTO users (username, password, device_id, created_at) VALUES (:username, :password, :deviceId, :createdAt)";
		$stmt = $db->prepare($sql);
		$date = date("Y-m-d H:i:s");
		$passwordHash = md5($body['password']);
		$stmt->bindParam("username", $body['username']);
		$stmt->bindParam("password", $passwordHash);
		$stmt->bindParam("deviceId", $body['deviceId']);
		$stmt->bindParam("createdAt", $date);
		$stmt->execute();
		$userId = $db->lastInsertId();
		$json["success"] = 1;
		$json["data"] = array("userId" => $userId);
		$json["msg"] = "User created successfully!";
	}
	$db = null;
	echo json_encode($json);
}
$app->run();