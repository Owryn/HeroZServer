<?php
namespace Request;

use Srv\Core;
use Srv\DB;
use Srv\Config;
use Srv\Mail;
use Srv\Socket;
use Schema\Messages;
use Schema\MessageCharacter;

class sendMessage{
    public function __request($player){
        $targetName = getField('to', FIELD_ALNUM);
        $subject = getField('subject');
        $message = getField('message');
        
        $subject = Core::validMSG($subject, "[***]");
        $message = Core::validMSG($message, "[***]");
        
        $subject = trim(preg_replace("/[^a-zA-Z0-9 ]+/", "", $subject));
        $message = trim($message);

        if(strlen($subject) == 0 || strlen($message) == 0)
			return Core::setError('');
		
		if(is_numeric($targetName))
			return Core::setError("errSendMessageInvalidRecipient");
		
		$target = MessageCharacter::find(function($q)use($targetName){ $q->where('name',$targetName); });
		if(!$target)
			return Core::setError("errSendMessageInvalidRecipient_{$targetName}");
		
		if($target->id == $player->character->id)
			return Core::setError("errCreatePersonalMessageSelfRecipient");

		$isIgnored = DB::sql("SELECT id FROM message_ignored_characters WHERE character_id = {$target->id} AND ignored_character_id = {$player->character->id}")->fetchAll();
		if(count($isIgnored) > 0)
			return Core::setError("errSendMessageInvalidRecipient");

		$msg = new Messages([
			'character_from_id'=>$player->character->id,
			'character_to_ids'=>";{$target->id};",
			'subject'=>$subject,
			'message'=>$message,
			'ts_creation'=>time()
		]);
		$msg->save();

		// Notify recipient in real-time
		$stmt = DB::$connection->prepare("SELECT u.id as user_id, u.email, u.email_notifications, u.locale FROM user u JOIN `character` c ON c.user_id = u.id WHERE c.id = ?");
		$stmt->execute([$target->id]);
		$rcpt = $stmt->fetch(\PDO::FETCH_ASSOC);
		if($rcpt) Socket::syncGame($rcpt['user_id']);

		// Email notification
		if(Config::get('email.notify_message')){
		    if($rcpt && $rcpt['email_notifications'])
		        Mail::queue(0, $rcpt['email'], 'New Message — HeroZero', 'new-message', [
		            'sender_name'=>$player->character->name,
		            'msg_subject'=>$subject,
		            'locale'=>$rcpt['locale']
		        ]);
		}

		Core::req()->data = array(
			"message" => $msg,
			"messages_character_info" => [$target->id => $target]
		);
    }
}