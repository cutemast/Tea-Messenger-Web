<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-12-13 17:53:00
 *  Namespace App
 */

namespace App;

use PDO;
use IceTea\Database\DB;
use IceTea\Hub\Singleton;
use IceTea\Support\Model;

class Login extends Model
{

    use Singleton;

    private $session_id;

    private $session_key;

    private $user_id;

    private $isLoggedIn;

    public static function getSessionId()
    {
        self::isLoggedIn();
        $ins = self::getInstance();
        return $ins->user_id;
    }

    public static function getSessionKey()
    {
        self::isLoggedIn();
        $ins = self::getInstance();
        return $ins->session_key;
    }

    public static function getUserId()
    {
        self::isLoggedIn();
        $ins = self::getInstance();
        return $ins->user_id;
    }

    public static function logout()
    {
        self::isLoggedIn();
        $ins = self::getInstance();
        $st = DB::prepare("DELETE FROM `sessions` WHERE `user_id`=:user_id AND `session_id`=:session_id LIMIT 1;");
        pc($st->execute(
            [
                ":user_id"      => $ins->user_id,
                ":session_id"   => $ins->session_id
            ]
        ), $st);
        return true;
    }

    public static function isLoggedIn()
    {
        $ins = self::getInstance();
        if ($ins->isLoggedIn === null) {
            if (isset($_COOKIE['session_id'], $_COOKIE['session_key'], $_COOKIE['user_id'])) {
                $ins->session_key = ice_decrypt($_COOKIE['session_key'], "tea_messenger123");
                $ins->session_id  = ice_decrypt($_COOKIE['session_id'], $ins->session_key);
                $ins->user_id     = ice_decrypt($_COOKIE['user_id'], $ins->session_key);
                $st = DB::prepare("SELECT `expired_at` FROM `sessions` WHERE `session_id`=:session_id AND `user_id`=:user_id LIMIT 1;");
                pc($st->execute(
                    [
                        ":session_id" => $ins->session_id,
                        ":user_id"    => $ins->user_id
                    ]
                ), $st);
                if ($st = $st->fetch(PDO::FETCH_NUM)) {
                    $ins->isLoggedIn = strtotime($st[0]) > time();
                    return $ins->isLoggedIn;
                }
            }
            return false;
        }
        return $ins->isLoggedIn;
    }

    public static function setSessionWithValidationCredentials($identity, $password)
    {
        if ($st = self::getBcryptHash($identity)) {
            if (password_verify($password, $st[0])) {
                $dt = [
                    "user_id"    => $st[1],
                    "session_id" => rstr(121),
                    "key"        => rstr(64),
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

    public static function getBcryptHash($identity)
    {
        $field = filter_var($identity, FILTER_VALIDATE_EMAIL) ? "email" : (is_numeric($identity) ? "user_id" : "username");
        $st = DB::prepare("SELECT `password`,`user_id` FROM `users` WHERE `{$field}`=:bind LIMIT 1;");
        pc($st->execute([":bind" => $identity]), $st);
        return $st->fetch(PDO::FETCH_NUM);
    }
}
