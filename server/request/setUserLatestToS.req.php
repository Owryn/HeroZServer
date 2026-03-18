<?php
namespace Request;
use Srv\Req;

class setUserLatestToS{

    public function __request($player){

        $settings = json_decode($player->user->settings, true) ?: [];
        $settings['tos_sep2015'] = true;
        $player->user->settings = json_encode($settings);

        Req::addData('user', $player->user);
    }

}
