<?php
namespace Request;
use Srv\Req;

class refreshQuests{

    public function __request($player){

        Req::addData('character', $player->character);
        Req::addData('quests', $player->getQuests());
    }

}
