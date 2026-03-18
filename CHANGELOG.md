# Changelog

All notable changes to this project will be documented in this file.

## [1.0.6] - 2026-03-18

### Added
- `setUserLocale` handler - save language preference from in-game locale change
- `setUserLatestToS` handler - accept Terms of Service dialog
- `refreshQuests` handler - refresh quest list when quest booster expires or leaving guild with active booster
- `logoutUser` handler - clears all session IDs and cookie on logout (was empty stub)
- `gameReportError` handler - logs SWF client errors to `server/cache/swf_errors.log` (was empty stub)

### Fixed
- League fight rewards crash - winner field read from wrong table (same as duel fix)
- League division boundary bug - demotion conditions used `<=` instead of `<`, causing promote+demote on same request at exact boundary value
- All new handlers now use `$player` parameter instead of `Core::$player` to match existing codebase pattern

## [1.0.5] - 2026-03-15

### Added
- Guild battle history (`getGuildBattleHistoryFights`, `getGuildBattleHistoryFight`) showing past guild battles, defenses and dungeon fights with enemy emblems
- Item validation in `Player::getItems()` and `Player::createItem()` to filter items with identifiers not in GameSettings templates, preventing SWF crashes
- Voucher / promo code system (`redeemVoucher`, `getUserVoucher`, `redeemUserVoucherLater`) with code validation, usage limits, level/locale restrictions, expiry dates
- Admin voucher management panel (create, view, toggle, delete, redemption log)
- New DB tables: `vouchers`, `voucher_redemptions`

## [1.0.4] - 2026-03-15

### Added
- Guild leader elections (`initGuildLeaderVote`, `voteForGuildLeader`) with majority-based resolution and new `guild_leader_votes` table
- Release guild artifacts for premium currency (`releaseArtifact`)
- Decline guild invitations with 72h expiry check
- Claim guild dungeon battle rewards (gold + optional item)
- Claim items attached to messages (`claimMessageItems`)
- Change account email with password verification
- Account deletion with full cascade cleanup
- Stat redistribution (`reskillCharacterStats`)
- `itemPatternSelected` stub (SWF notification, no action needed)

### Fixed
- `Config::get()` crashes in `releaseArtifact`, `voteForGuildLeader`, and `reskillCharacterStats` when config keys don't exist yet (replaced `?: default` with 2nd argument)

### Documentation
- Full SWF compatibility report (`SWF_COMPATIBILITY_REPORT.md`): 234 client actions cross-referenced against 171 server handlers, 86% coverage, prioritized list of 28 remaining actions
- README rewritten with feature tables, architecture diagram, install guide, and roadmap

## [1.0.3] - 2026-03-14

### Added
- Slot machine fully working (updated expired event dates to 2024-2030)
- Slot machine room handlers (`addUserToSlotmachineRoom`, `removeUserFromSlotmachineRoom`)
- All 7 reward types: coins, XP, stat points, quest energy, training, boosters, items. Sidekick symbol falls back to coins since the SWF dialog doesn't handle it
- Item rewards generate random equipment on symbol 2, quality scales with spin result
- Booster rewards on symbol 3 with 3 tiers, stacks with active boosters
- World boss reworked to two-step flow: `checkForWorldbossAttackComplete` runs the actual fight, `finishWorldbossAttack` just returns the result
- `abortWorldbossAttack` and `instantFinishWorldbossAttack` handlers
- Pending world boss attack now persists across page refresh (included in login response)
- Changed world boss identifier to `olympia_event_stage2` (the old one had no CDN translations)

### Fixed
- Slot machine always winning (leftover debug values instead of `mt_rand`)
- Slot quality wasn't tied to reel results. Now 3-match = quality 3, 2-match = quality 2, else quality 1
- Slot chat showing "NaN" (`json_decode` without `true` returns stdClass, `current()` on that gives false)
- Slot chat JSON parse error in SWF (`syncSlotmachineChat` was sending PHP object, not JSON string)
- Double-spin exploit: added `GET_LOCK` + `countCurrentSpins` check to cover the ~7-9s animation window where the SWF briefly re-enables the button
- World boss attack crash (`profileBHitPoints` on null): Battle records were missing profile stats fields
- Second world boss attack failing because the first attack's dialog never opened (blocked by the profileB crash)
- `finishWorldbossAttack` was running a second fight on completed attacks, doubling damage. Also: SWF sends `worldboss_event_id`, not `worldboss_attack_id`
- World boss attack stuck after changing language (login response was missing `worldboss_attack` key)
- World boss reward totals not updating between attacks (handlers now return full `worldboss_event_character_data`)
- `process-tournament.php end` crashing on `fetchAll()`: BIGINT UNSIGNED underflow when honor decreased during tournament. Fixed with `GREATEST(0, CAST(... AS SIGNED))`
- Tournament guild leaderboard expecting short keys (`r`, `n`, `v`, `ebs`, etc.) but getting full column names
- Same UNSIGNED underflow in character tournament rankings

## [1.0.2] - 2026-03-13

### Changed
- Admin Items page is now a read-only catalog of all 779 item templates from GameSettings (search, filter by type/quality/pattern, pagination)
- Admin Characters: items section categorized into Equipped, Bag, Shop, Bank
- "Give Item" replaces old "Create Item" on character edit (picks from template catalog, auto-generates stats, bag or bank destination)
- Removed `admin/views/items/create.php`

## [1.0.1] - 2026-03-13

### Fixed
- Messages not sending: `validMSG()` regex was missing the `/u` modifier for Unicode `\p{P}`, so `preg_replace` returned NULL on PHP 8.x
- `sendMessage` response: `messages_character_info` changed to dictionary keyed by character ID (was flat object, SWF expects dict)
- Admin message compose double-submit
- Duel/league stamina regeneration going over maximum

### Security
- Replaced `addslashes()` with prepared statements in all admin search queries
- Admin character edit: parameterized UPDATE instead of string building
- All destructive admin actions (ban, delete, etc.) now require POST + CSRF token instead of GET links

### Changed
- Removed dead Inventory and Battles entries from admin sidebar

## [1.0.0] - 2026-03-12

### Added
- Ruffle.rs Flash emulation so the SWF client runs in modern browsers
- CDN proxy (`cdn/proxy.php`) to serve Akamai assets locally, bypassing CORS
- Language switcher (PL / EN / BR) with cookie-based persistence on all pages
- Email system: PHPMailer + MySQL async queue + 10 templates + CLI processor
- Password reset flow (forgot password page, token validation, generated password email)
- `resetUserPassword` handler for the SWF's built-in "Reinicializar" button
- Admin panel at `/admin/` with Bootstrap 5 dark theme
- Admin management: users (ban/unban, currency), characters (stats, appearance, items), guilds, items, messages, email, config
- CSRF protection and rate-limited login for admin
- `pages/` directory for standalone pages
- WASM MIME type + GZIP in `.htaccess`
- Real-time socket server (Node.js + ws) with push notifications to SWF clients
- Ruffle socketProxy to tunnel `flash.net.Socket` TCP through browser WebSocket (9998→9999)
- `Socket` PHP helper class for pushing events from backend
- Admin message send triggers `syncGame` push to recipient
- 243 goals/achievements with milestone rewards, all 9 SWF reward types
- Goal stat tracking across all game mechanics (quests, duels, training, work, league, dungeons, guild battles, sidekicks, etc.)
- Real-time goal sync via socket push on `goalStatsChanged`
- Daily goal counters reset in `regenerateSometime()`
- Herobook system: 3 daily + 2 weekly rotating objectives, unlocks at level 40
- Herobook item drops (types 17/18/19) on reward claims
- Herobook progress tracked in 7 reward handlers
- Sewing machine: change item skins for gold/donuts
- Costume collections: 33 themed sets with milestone rewards
- Guild dungeon combat: attack and join battles with full fight resolution
- Surprise box: open for 1-3 random equipment pieces
- World boss: server-wide boss fights with HP tracking, damage ranking, rewards, CLI lifecycle
- Tournaments: weekly XP/honor/guild leaderboards with CLI lifecycle and reward distribution
- New DB tables: `email_queue`, `email_log`, `password_reset_tokens`, `collected_goals`, `herobook_objectives`, `goal_pending_items`, `pattern_items`, `worldboss_event`, `worldboss_attack`, `worldboss_reward`, `tournaments`, `tournament_snapshots`, `tournament_rewards`
- Goal tracking for 44 new goals across sewing, costumes, dungeons, surprise box, world boss, tournaments

### Changed
- All URLs point to `http://localhost/` for local dev
- `resource_cdn` uses local CDN proxy instead of direct Akamai
- `url_support` opens local forgot-password page
- Added `payment_price_overwrite` to extended config (required by SWF payment dialog)
- `game.php` loads Ruffle CDN, language switcher, socketProxy config
- `countdown.php` rewritten with multi-language support
- Replaced piggyback `new_messages` HTTP response with socket push
- Goal coin formula matches SWF: `coinsPerTime(level) * base * factor * time + pow(level, exp)`
- Quest energy from goals adds directly without capping
- Enabled `herobook_enabled` and `tournaments_enabled` in GameSettings
- `loginUser` and `createCharacter` return dynamic `tournament_end_timestamp`
- Re-enabled guild dungeon loading in `Guild.php`

### Fixed
- JSON responses corrupted by PHP 8.x deprecation warnings (Hydrahon dynamic properties)
- "Erro desconhecido" on password reset button (missing handler)
- Payment dialog crash (missing `payment_price_overwrite`)
- CORS blocking CDN assets on localhost
- Silent INSERT failures on MySQL 8.4 strict mode with legacy schemas
- Goal coin rewards off by orders of magnitude (formula mismatch)
- Quest energy from goals capped at 100
- `giveRewards()` not recognizing `stat_points` key (only handled camelCase)
- `herobook_objectives_renewed_today` never resetting
- Dungeon quest rewards not tracking herobook progress
- `$dungeon_status` undefined in `claimDungeonQuestRewards` (dungeons never regenerated)
- Missing `email_notifications` in User.php schema
- `GameSettings::getConstant()` crash on missing patterns
- League opponents boundary bug (`<=` should be `<`)
- `GuildDungeonBattle::checkFight()` incomplete
- `claimDuelRewards` reading `winner` from wrong record (Duel instead of Battle)
- Guild emblem crash when shape is 0 (asset arrays start at 1)

### Removed
- Direct Akamai CDN URL
- Root-level password reset pages (moved to `pages/`)
- Debug files

### Security
- Password reset: `random_bytes(32)` tokens, 1h TTL, single-use
- Anti-enumeration on reset (same response regardless of email existence)
- Rate limit: 20 resets per email per hour + 60s cooldown
- CSRF on all admin actions
- Prepared statements on all user input
- Admin login: 5 attempts per 15 min

---

## [0.2.0] - 2018-08-18

> Original release by [xReveres](https://github.com/xReveres/HeroZServer)

### Added
- Guild battles
- Energy renewal limit
- Training limit
- Energy and training points bank
- Sent messages view
- Guild boosters
- Daily login rewards
- Gamble City slot machine

### Fixed
- Fight reward calculation (was treating wins as losses)
- Quest loss rewards (reduced XP and coins)
- 5 tires now added when unlocking a new zone
- Quest XP generator formula

---

## [0.1a] - 2018-08-18

### Fixed
- EXP calculation
- Guild leave during attack/defense causing fight bugs
- Player and guild ranking display (max 50 limit)
- Ammo belt auto-refill when ammunition runs out
- Various server engine fixes
- Guild invite bug with 3-day expiry and re-invitations

---

## [0.1.0] - 2018-08-12

> Full engine rewrite by [xReveres](https://github.com/xReveres/HeroZServer)

### Added
- Guild battles (with tactics and projectiles, without doubles)
- Amnesia hammer
- Hero sense
- Guild development bonuses
- Multitasking

### Fixed
- Login error messages (wrong password now shows correct message instead of "account not found")
- Quest XP drops (closer to official server values)
- Coin drops
- Honor/coin drops from fights
- Shop item generation
- Mailbox auto-refresh (every 1 min)
- Guild auto-refresh (attack/members/chat, every ~20 sec)

---

[1.0.4]: https://github.com/xReveres/HeroZServer/compare/v1.0.3...v1.0.4
[1.0.3]: https://github.com/xReveres/HeroZServer/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/xReveres/HeroZServer/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/xReveres/HeroZServer/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/xReveres/HeroZServer/compare/v0.2.0...v1.0.0
[0.2.0]: https://github.com/xReveres/HeroZServer/compare/v0.1a...v0.2.0
[0.1a]: https://github.com/xReveres/HeroZServer/compare/v0.1.0...v0.1a
[0.1.0]: https://github.com/xReveres/HeroZServer/releases/tag/v0.1.0
