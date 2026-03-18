<?php
namespace Request;
use Srv\Req;

class setUserLocale{

    public function __request($player){
        $locale = $_POST['locale'] ?? '';

        $valid = ['en_GB','pl_PL','pt_BR','de_DE','fr_FR','es_ES','it_IT','tr_TR','nl_NL','sv_SE','ru_RU'];
        if(!in_array($locale, $valid))
            return Req::error('errInvalidLocale');

        $player->user->locale = $locale;

        Req::addData('user', $player->user);
    }

}
