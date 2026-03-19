<?php
namespace Request;

use Srv\Core;
use Srv\DB;

class ignoreMessageCharacter{
    public function __request($player){
        $characterIgnoreId = intval(getField('character_ignore_id', FIELD_NUM));

        if($characterIgnoreId <= 0 || $characterIgnoreId == $player->character->id){
            return;
        }

        $existing = DB::sql("SELECT id FROM message_ignored_characters WHERE character_id = {$player->character->id} AND ignored_character_id = {$characterIgnoreId}")->fetchAll();
        if(count($existing) > 0){
            return;
        }

        $target = DB::sql("SELECT id, name, gender FROM `character` WHERE id = {$characterIgnoreId}")->fetchAll();
        if(count($target) == 0){
            return;
        }

        DB::sql("INSERT INTO message_ignored_characters (character_id, ignored_character_id, ts_creation) VALUES ({$player->character->id}, {$characterIgnoreId}, " . time() . ")");

        Core::req()->data = [
            'messages_ignored_character_info' => $this->getIgnoredList($player)
        ];
    }

    private function getIgnoredList($player){
        $ignored = DB::sql("SELECT mic.ignored_character_id, mic.ts_creation, c.name, c.gender FROM message_ignored_characters mic JOIN `character` c ON c.id = mic.ignored_character_id WHERE mic.character_id = {$player->character->id}")->fetchAll();
        $info = [];
        foreach($ignored as $row){
            $info[$row['ignored_character_id']] = [
                'id' => (int)$row['ignored_character_id'],
                'name' => $row['name'],
                'gender' => $row['gender'],
                'online_status' => 2,
                'ts_ignore_date' => (int)$row['ts_creation']
            ];
        }
        return empty($info) ? (object)[] : (object)$info;
    }
}
