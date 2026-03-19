<?php
namespace Request;

use Srv\Core;
use Schema\Messages;
use Schema\MessageCharacter;

class getMessageList{
    
    public function __request($player){
    	$load_received = getField('load_received', FIELD_BOOL)=='true';
    	$load_sent = getField('load_sent', FIELD_BOOL)=='true';
    	if($load_received)
        	$messages = Messages::findAll(function($q) use($player){ $q->where('character_to_ids','LIKE',"%;{$player->character->id};%"); });
        else if($load_sent)
        	$messages = Messages::findAll(function($q) use($player){ $q->where('character_from_id',$player->character->id)->where('flag',''); });
        
        $charinfo = MessageCharacter::getFromList($messages);
        
        $readed = [];
		foreach($messages as &$msg){
			if($msg->readed)
				$readed[] = $msg->id;
			unset($msg->message);
		}
		
		Core::req()->data = array(
			"character" => $player->character,
			"messages" => $messages,
			"messages_character_info" => $charinfo,
			"messages_ignored_character_info" => $this->getIgnoredCharacters($player),
			"messages_read" => $readed
		);
		if($load_received){
			Core::req()->data['new_messages'] = (count($messages) - count($readed));
			Core::req()->data['messages_received_count'] = count($messages);
		}
		if($load_sent)
			Core::req()->data['messages_sent_count'] = count($messages);
    }

    private function getIgnoredCharacters($player){
        $ignored = \Srv\DB::sql("SELECT mic.ignored_character_id, mic.ts_creation, c.name, c.gender FROM message_ignored_characters mic JOIN `character` c ON c.id = mic.ignored_character_id WHERE mic.character_id = {$player->character->id}")->fetchAll();
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