<?php
namespace Request;

use Srv\Core;
use Srv\DB;

class getGuildBattleHistoryFight{
    public function __request($player){
        $type = intval(getField('type', FIELD_NUM));
        $id = intval(getField('id', FIELD_NUM));
        $guildId = $player->character->guild_id;

        if(!$guildId)
            return Core::setError('errNotInGuild');

        if($type == 3){
            $row = DB::sql("SELECT * FROM guild_dungeon_battle WHERE id = ? AND guild_id = ? AND status = 3", [$id, $guildId])->fetch(\PDO::FETCH_ASSOC);
            if(!$row)
                return Core::setError('');

            $rewards = json_decode($row['rewards'], true) ?: [];
            $won = !empty($rewards) && isset($rewards['game_currency']) && $rewards['game_currency'] > 0;

            $battle = [
                'id' => (int)$row['id'],
                'type' => 3,
                'enemy' => $row['npc_team_identifier'],
                'battle_timestamp' => (int)$row['ts_attack'],
                'won' => $won,
                'enemy_name' => '',
                'joined_character_ids' => $row['character_ids'],
                'rewards' => json_encode($rewards),
                'ebs' => 0, 'ebc' => 0, 'ebbc' => 0,
                'eis' => 0, 'eic' => 0, 'eiz' => 0,
            ];
        }else{
            $row = DB::sql("SELECT gb.*, g_att.name as attacker_name, g_att.emblem_background_shape as a_ebs, g_att.emblem_background_color as a_ebc, g_att.emblem_background_border_color as a_ebbc, g_att.emblem_icon_shape as a_eis, g_att.emblem_icon_color as a_eic, g_att.emblem_icon_size as a_eiz, g_def.name as defender_name, g_def.emblem_background_shape as d_ebs, g_def.emblem_background_color as d_ebc, g_def.emblem_background_border_color as d_ebbc, g_def.emblem_icon_shape as d_eis, g_def.emblem_icon_color as d_eic, g_def.emblem_icon_size as d_eiz FROM guild_battle gb LEFT JOIN guild g_att ON g_att.id = gb.guild_attacker_id LEFT JOIN guild g_def ON g_def.id = gb.guild_defender_id WHERE gb.id = ? AND gb.status = 3 AND (gb.guild_attacker_id = ? OR gb.guild_defender_id = ?)", [$id, $guildId, $guildId])->fetch(\PDO::FETCH_ASSOC);

            if(!$row)
                return Core::setError('');

            $isAttacker = ($row['guild_attacker_id'] == $guildId);
            $enemyGuildId = $isAttacker ? $row['guild_defender_id'] : $row['guild_attacker_id'];
            $enemyName = $isAttacker ? $row['defender_name'] : $row['attacker_name'];
            $charIds = $isAttacker ? $row['attacker_character_ids'] : $row['defender_character_ids'];
            $rewardsJson = $isAttacker ? $row['attacker_rewards'] : $row['defender_rewards'];

            $prefix = $isAttacker ? 'd_' : 'a_';
            $battle = [
                'id' => (int)$row['id'],
                'type' => $isAttacker ? 1 : 2,
                'enemy' => (int)$enemyGuildId,
                'battle_timestamp' => (int)$row['ts_attack'],
                'won' => ($row['guild_winner_id'] == $guildId),
                'enemy_name' => $enemyName ?: '',
                'joined_character_ids' => $charIds,
                'rewards' => $rewardsJson ?: '{}',
                'ebs' => (int)$row[$prefix.'ebs'],
                'ebc' => (int)$row[$prefix.'ebc'],
                'ebbc' => (int)$row[$prefix.'ebbc'],
                'eis' => (int)$row[$prefix.'eis'],
                'eic' => (int)$row[$prefix.'eic'],
                'eiz' => (int)$row[$prefix.'eiz'],
            ];
        }

        Core::req()->data = [
            'guild_history_battle' => $battle
        ];
    }
}
