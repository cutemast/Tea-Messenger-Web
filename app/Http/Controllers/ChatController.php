<?php

/**
 *  Generated by IceTea Framework 0.0.1
 *  Created at 2017-12-13 23:14:40
 *  Namespace App\Http\Controllers
 */

namespace App\Http\Controllers;

use App\User;
use App\Chat;
use App\Login;
use IceTea\Http\Controller;
use App\Http\Controllers\Auth\Authenticated;

class ChatController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        return view('user/chat');
    }

    public function to($par)
    {
        Authenticated::login("", "/login?ref=unauthenticated_chat&w=".urlencode(rstr(64)));
        $info = Chat::getChatInfo($selfid = Login::getUserId(), $par['username']);
        $selfinfo = User::getInfo($selfid, "a.user_id");
        if ($info !== false) {
            return view('user/chat_end', ["info" => $info, "boundary" => json_encode(
                    [
                        $info['user_id'] => [
                            "status" => "party",
                            "name" => htmlspecialchars($info['first_name'].(empty($info['last_name'])?"":" ".$info['last_name']), ENT_QUOTES, 'UTF-8'),
                            "photo" => ($info['photo'])
                        ],
                        $selfid     => [
                            "status" => "self",
                            "name" => htmlspecialchars($selfinfo['first_name'].(empty($selfinfo['last_name'])?"":" ".$selfinfo['last_name']), ENT_QUOTES, 'UTF-8'),
                            "photo" => ($selfinfo['photo'])
                        ]
                    ]
                ),
                "selfinfo" => $selfinfo
            ]
        );
        }
        abort(404);
    }

    public function get($par)
    {
        Authenticated::login();
        header("Content-type:application/json");
        $receiverId = User::getUserId($par['username']);
        if ($receiverId !== false) {
            if (isset($_GET['realtime_update'])) {
                print json_encode(array_reverse(Chat::getPrivateConversationRealtimeUpdate(Login::getUserId(), $receiverId)));
            } else {
                print json_encode(array_reverse(Chat::getPrivateConversation(Login::getUserId(), $receiverId)));
            }
        }
    }

    public function post($par)
    {
        Authenticated::login();
        header("Content-type:application/json");
        $a = json_decode(file_get_contents("php://input"), true);
        $receiverId = User::getUserId($par['username']);
        if ($receiverId !== false) {
            print Chat::privatePost(Login::getUserId(), $receiverId, $a['text']);
        }
    }
}
