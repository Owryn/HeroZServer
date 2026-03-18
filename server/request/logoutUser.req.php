<?php
namespace Request;

class logoutUser{

    public function __request($player){

        $player->user->session_id = '';
        $player->user->session_id_cache1 = '';
        $player->user->session_id_cache2 = '';
        $player->user->session_id_cache3 = '';
        $player->user->session_id_cache4 = '';
        $player->user->session_id_cache5 = '';

        setcookie("ssid", "", time() - 3600, '/');
    }

}