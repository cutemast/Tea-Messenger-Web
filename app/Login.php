<?php

/**
 *	Generated by IceTea Framework 0.0.1
 *	Created at 2017-12-13 17:53:00
 *	Namespace App
 */

namespace App;

use PDO;
use IceTea\Database\DB;
use IceTea\Support\Model;

class Login extends Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function isLoggedIn()
	{

	}

	public function validateCredentials($identity, $password)
	{
		$field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? "email" : (is_numeric($identity) ? "user_id" : "username");
		$st = DB::prepare("SELECT `password`,`user_id` FROM `users` WHERE `{$field}`=:bind LIMIT 1;");
		pc($st->execute([":bind" => $identity]), $st);
		if ($st = $st->fetch(PDO::FETCH_NUM)) {
			if (password_verify($password, $st[0])) {
				$dt = [
					"user_id"	 => $st[1],
					"session_id" => rstr(121),
					"key"		 => rstr(64),
					"user_agent" => (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : ""),
					"remote_addr"=> (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : ""),
					"expired_at" => date("Y-m-d H:i:s", time() + (3600 * 24 * 14)),
					"created_at" => date("Y-m-d H:i:s")
				];
				$st = DB::prepare("INSERT INTO `sessions` (`user_id`, `session_id`, `key`, `user_agent`, `remote_addr`, `expired_at`, `created_at`) VALUES (:user_id, :session_id, :key, :user_agent, :remote_addr, :expired_at, :created_at);");
				pc($st->execute($dt), $st);
				return $dt;
			}
		}
		return false;
	}
}
