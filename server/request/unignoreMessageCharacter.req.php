<?php
namespace Request;

use Srv\Core;
use Srv\DB;

class unignoreMessageCharacter{
    public function __request($player){
        $characterUnignoreId = intval(getField('character_unignore_id', FIELD_NUM));

        if($characterUnignoreId <= 0){
            return;
        }

        DB::sql("DELETE FROM message_ignored_characters WHERE character_id = {$player->character->id} AND ignored_character_id = {$characterUnignoreId}");

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

        Core::req()->data = [
            'messages_ignored_character_info' => empty($info) ? (object)[] : (object)$info
        ];
    }
}
