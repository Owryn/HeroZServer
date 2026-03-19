<div align="center">

![Hero Zero](https://i.imgur.com/heSuus8.png)


**Private server for Hero Zero - browser RPG**

The original Flash client runs through Ruffle.rs WebAssembly — no Flash Player needed.

![PHP](https://img.shields.io/badge/PHP-8.x-777BB4?logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.x-4479A1?logo=mysql&logoColor=white)
![Node.js](https://img.shields.io/badge/Node.js-18+-339933?logo=nodedotjs&logoColor=white)
![Ruffle](https://img.shields.io/badge/Flash-Ruffle.rs-FF6600?logo=adobe&logoColor=white)
![License](https://img.shields.io/badge/License-GPL_v3-blue)
![SWF Coverage](https://img.shields.io/badge/SWF_Coverage-87%25-brightgreen)
[![Ko-fi](https://img.shields.io/badge/Ko--fi-support-ff5e5b?logo=ko-fi&logoColor=white)](https://ko-fi.com/owryn)

[Features](#features) · [Screenshots](#screenshots) · [Installation](#installation) · [Socket Server](#socket-server) · [Admin Panel](#admin-panel) · [CLI Scripts](#cli-scripts)

</div>

---

## About

Built on top of [xReveres/HeroZServer](https://github.com/xReveres/HeroZServer) v0.2. The original base had the core game loop but a lot was either broken or missing on PHP 8.x. This project rewrites and extends the server to get it closer to how the real game works, including systems that were never implemented in the original code.

## Screenshots

https://imgur.com/a/XjBiT4S



## Features

### Core Infrastructure
| Feature | Description |
|---------|-------------|
| **Ruffle.rs** | Flash SWF client runs natively in Chrome, Firefox, Edge — no plugins |
| **WebSocket Server** | Real-time push notifications for messages, goals, guild events, world boss |
| **Email System** | PHPMailer + MySQL async queue, password reset flow with tokens |
| **Admin Panel** | Bootstrap 5 dark theme, 8 management controllers |
| **Multi-Language** | Polish, English, Portuguese (PT-BR) on all pages |
| **CDN Proxy** | Serves Akamai game assets locally, bypasses CORS |

### Game Systems
| System | Status | Details |
|--------|:------:|---------|
| Quests, Duels, Training, Work | ✅ | All core gameplay loops |
| Guild Battles | ✅ | Team fights with tactics and projectiles |
| Guild Leader Elections | ✅ | Vote-based leadership transfers with majority resolution |
| Guild Artifacts | ✅ | Release artifacts for premium currency with cooldown |
| Goals & Achievements | ✅ | 243 achievements, 9 reward types, real-time sync |
| Herobook Objectives | ✅ | 3 daily + 2 weekly rotating challenges (level 40+) |
| Tournaments | ✅ | Weekly events with XP/honor leaderboards and rewards |
| World Boss | ✅ | Server-wide boss fights with damage rankings |
| Sewing Machine | ✅ | Change item skins for gold or donuts |
| Costume Collections | ✅ | 33 themed sets with milestone rewards |
| Guild Dungeons | ✅ | Full attack/join flow with NPC team battles |
| Guild Battle History | ✅ | View past guild battles and dungeon fights |
| Surprise Box | ✅ | 1-3 random equipment pieces (rare/epic) |
| Daily Login Rewards | ✅ | Consecutive login bonuses |
| Slot Machine | ✅ | 7 reward types (coins, XP, stat points, energy, training, boosters, items), anti-exploit protection |
| Sidekicks | ✅ | Equip, merge, rename, release, reorder |
| Message Ignore | ✅ | Block/unblock characters from messaging you |
| Messages | ✅ | Send, delete, claim item attachments |
| Account Management | ✅ | Change password/email, delete account, stat redistribution |
| Voucher / Promo Codes | ✅ | Code validation, usage limits, level/locale restrictions, expiry |
| Leaderboards | ✅ | Character XP, honor, guild rankings |
| Friend System | ❌ | Not yet |
| Event Quests | ❌ | Not yet |

### Admin Panel

**URL:** `http://localhost/admin/` · **Login:** `admin` / `admin`

| Module | Features |
|--------|----------|
| **Dashboard** | Server stats, player count, recent activity |
| **Users** | Ban/unban, reset password, add currency, view details |
| **Characters** | Edit stats, appearance, level, quick actions, give items (bag/bank) |
| **Guilds** | Members, currency, settings |
| **Items** | Read-only catalog of 779 templates (search, filter by type/quality/pattern) |
| **Messages** | Compose, broadcast to all players |
| **Email** | Queue, log, broadcast, SMTP settings |
| **Config** | Server config viewer, cache clear |

### SWF Compatibility

About 87% of the game client's actions are implemented. The remaining ones are mostly the friend system, event quests, and some minor features.



### What's Left to Implement

| Feature | Handlers | Priority | Details |
|---------|:--------:|:--------:|---------|
| Friend System | 5 | Medium | Add/remove friends, friend bar, invitations |
| Event Quests | 4 | Medium | Timed objectives with 10 tracking types, 6 reward types |
| Resource Requests | 8 | Medium | Send energy/spins to friends (depends on Friend System) |

> **Not planned:** Payments, Advertising, SSO/Platform, Kongregate, Game Testing - these don't apply to a private server.

## Tech Stack

```
┌──────────────────────────────────────────────────┐
│                   Browser                        │
│  ┌──────────┐  ┌──────────┐  ┌────────────────┐  │
│  │ Ruffle.rs│  │ SWF Game │  │ Language Switch│  │
│  │ (WASM)   │  │ (Flash)  │  │ (PL/EN/BR)     │  │
│  └────┬─────┘  └────┬─────┘  └────────────────┘  │
└───────┼─────────────┼────────────────────────────┘
        │             │
   WebSocket      HTTP POST
   (port 9998)    (port 80)
        │             │
┌───────┼─────────────┼───────────────────────────┐
│       ▼             ▼           Server          │
│  ┌─────────┐  ┌────────────┐  ┌───────────────┐ │
│  │ Node.js │  │   Apache   │  │  Admin Panel  │ │
│  │ Socket  │  │  PHP 8.x   │  │ Bootstrap 5   │ │
│  │ :9999   │  │  request/  │  │  /admin/      │ │
│  └────┬────┘  └─────┬──────┘  └───────────────┘ │
│       │             │                           │
│       └──────┬──────┘                           │
│              ▼                                  │
│       ┌────────────┐  ┌─────────────────┐       │
│       │  MySQL 8.x │  │ CDN Proxy       │       │
│       │  (hz)      │  │ (Akamai assets) │       │
│       └────────────┘  └─────────────────┘       │
└─────────────────────────────────────────────────┘
```

## Installation

### Prerequisites
- **Windows:** [Laragon](https://laragon.org/) (recommended), XAMPP, WAMP, or manual Apache + PHP + MySQL
- **Linux:** LAMP (`apt install apache2 php php-mysql php-curl mysql-server`) or LEMP (Nginx)
- **Node.js** 18+ (for the real-time socket server)
- **Composer** (for PHP dependencies)
- PHP extensions: `curl`, `pdo_mysql`

### Quick Start

```bash
# 1. Clone the repository
git clone https://github.com/xReveres/HeroZServer.git
cd HeroZServer

# 2. Install PHP dependencies
composer install

# 3. Create database and import schema
mysql -u root -e "CREATE DATABASE hz;"
mysql -u root hz < hzpriv.sql

# 4. Install socket server
cd socket-server && npm install && cd ..
```

### Configuration

**MySQL strict mode** — Edit `my.ini` / `my.cnf`, add under `[mysqld]`:
```ini
sql_mode=NO_ENGINE_SUBSTITUTION
```
> Restart MySQL after this change. Without it, INSERTs on legacy tables fail silently.

**Server config** — Edit `server/config.php`:
```php
'public_url'   => 'http://localhost/',
'request_url'  => 'http://localhost/server/request.php',
'resource_cdn' => 'cdn/proxy.php?a=',        // proxies Akamai CDN
'socket_url'   => 'http://localhost:9999',
```

**Cache permissions** (Linux only):
```bash
chmod -R 777 server/cache/
```

### Starting the Server

```bash
# Start the socket server (required for real-time features)
node socket-server/server.js

# Or use the platform-specific scripts:
# Windows: double-click socket-server/start.bat
# Linux:   chmod +x socket-server/start.sh && ./socket-server/start.sh
```

Then open `http://localhost/` in your browser. Create an account, create a character, play.

### Email Setup (Optional)

For password reset and notifications, install [MailPit](https://github.com/axllent/mailpit) for local development:

```bash
# Start MailPit (SMTP on :1025, Web UI on :8025)
mailpit
```

Emails are sent automatically — when `Mail::queue()` is called, it inserts into the queue and sends immediately. If sending fails (SMTP down, network issue), the email stays as `pending` in the queue. To retry failed emails:

- **Admin panel**: Email → **Process Queue** button
- **CLI**: `php server/process-email-queue.php`
- **Scheduled** (optional): cron or Task Scheduler to auto-retry every 5 minutes

Email config is in `server/config.php` under the `email` block.

## Socket Server

Real-time push notifications to connected game clients.

### Architecture

```
SWF Client ──► Ruffle socketProxy ──► ws://localhost:9998 ──► TCP localhost:9999
                                          (proxy)              (Socket.IO EIO=2)
```

| Port | Protocol | Purpose |
|------|----------|---------|
| **9999** | Socket.IO (EIO=2) | Main server — HTTP polling handshake + WebSocket upgrade |
| **9998** | WebSocket | Ruffle proxy — tunnels raw TCP bytes from browser to port 9999 |

> **Why two ports?** Browsers can't make raw TCP connections. Ruffle's `socketProxy` tunnels SWF's `flash.net.Socket` TCP through a browser WebSocket.

### Supported Events

| Event | Description | Used by |
|-------|-------------|---------|
| `syncGameImmediate` | Refresh game state (messages, quests, goals) | `Socket::syncGame()`, `Socket::syncGameAll()` |
| `syncGameAndGuild` | Refresh game + guild data | `Socket::syncGameAndGuild()` |
| `syncGuildLog` | Real-time guild chat messages and log updates | `Socket::syncGuildLog()` |
| `syncWorldboss` | Real-time world boss HP updates | `Socket::syncWorldboss()` |
| `syncSlotmachineChat` | Broadcast slot machine wins to all players | `Socket::syncSlotmachineChat()` |

> The SWF client also supports `syncFriendBar` but it is not currently sent by the server.

### PHP Push API

```php
use Srv\Socket;

Socket::syncGame($userId);                      // notify specific user
Socket::syncGameAll();                           // broadcast to all
Socket::syncGameAndGuild($userId);               // sync game + guild
Socket::syncGuildLog($guildId, $excludeUserId);  // guild chat/log to members
Socket::syncWorldboss($eventId, $hpCurrent);     // world boss HP broadcast
Socket::syncSlotmachineChat($message);           // slot machine win broadcast
```

### Status Check

```bash
curl http://localhost:9999/status
# Returns connected clients and user map
```

## CLI Scripts

Schedule these with **cron** (Linux) or **Task Scheduler** (Windows) for automatic operation.

| Script | Command | Description |
|--------|---------|-------------|
| **World Boss** | `php server/process-worldboss.php spawn` | Spawn a new boss event (4h duration) |
| | `php server/process-worldboss.php process` | **Generate rewards** when boss dies or time expires — must run periodically |
| | `php server/process-worldboss.php check` | Check event status (HP, time remaining) |
| **Tournament** | `php server/process-tournament.php start` | Start weekly tournament |
| | `php server/process-tournament.php end` | End current tournament and generate rewards |
| | `php server/process-tournament.php status` | Check tournament status |
| **Email** | `php server/process-email-queue.php` | Process pending emails |

> **Important:** `process-worldboss.php process` must run regularly (every 5-10 min) via Task Scheduler or cron. Without it, rewards are never generated and players cannot claim them after the boss is defeated. The boss HP formula assumes at least 100 active players — for solo testing, reduce HP manually after spawning: `UPDATE worldboss_event SET npc_hitpoints_total = 5000, npc_hitpoints_current = 5000 WHERE status = 1;`

## Cache Management

| Scenario | What to clear |
|----------|---------------|
| Config changes | `server/cache/data/*.tmp` |
| GameSettings changes | `server/cache/cache.json` + `server/cache/data/*.tmp` |
| initEnvironment changes | `server/cache/data/*.tmp` |

The admin panel has a **Clear Cache** button under **Config**.

## Debugging

| Check | Command |
|-------|---------|
| Test API | `curl -X POST http://localhost/server/request.php -d "action=initGame&client_version=flash_123"` |
| CDN proxy | `curl http://localhost/cdn/proxy.php?a=assets/sounds/ui_dialog_open.mp3` |
| Socket status | `curl http://localhost:9999/status` |
| MySQL mode | `SELECT @@sql_mode;` — must NOT contain `STRICT_TRANS_TABLES` |
| PHP errors | Comment out `error_reporting(0)` in `server/request.php` (breaks JSON) |

## Version History

See [CHANGELOG.md](CHANGELOG.md) for the full changelog.

| Version | Date | Highlights |
|---------|------|------------|
| **1.1.0** | 2026-03-19 | Message ignore system (ignore/unignore, server-side blocking) |
| **1.0.6** | 2026-03-18 | Quick win handlers (locale, ToS, quest refresh, logout, error logging), league fight fix |
| **1.0.5** | 2026-03-15 | Guild battle history, voucher/promo codes with admin panel, item validation |
| **1.0.4** | 2026-03-15 | 10 new handlers (guild elections, artifact release, message items, account management), Config::get() fix |
| **1.0.3** | 2026-03-14 | Slot machine (7 reward types, anti-exploit), world boss attack flow, tournament fixes |
| **1.0.2** | 2026-03-13 | World boss attack flow fixes, admin items/characters improvements |
| **1.0.1** | 2026-03-13 | Message system bug fixes |
| **1.0.0** | 2026-03-10 | Major rewrite — Ruffle.rs, email, admin, socket server, goals, herobook, tournaments, world boss, sewing, costumes, guild dungeons, surprise box |
| **0.2** | 2018 | Guild battles, energy limits, training, daily rewards, slot machine |
| **0.1** | 2018 | Original engine rewrite by xReveres |

## Contributing

This is a hobby project but contributions are welcome. Feel free to open issues or submit pull requests.

If you'd like to support the project, you can [support me on Ko-fi](https://ko-fi.com/owryn).

## License

This project is licensed under the [GNU General Public License v3.0](LICENSE).

---

<div align="center">

Developed by [Owryn](https://github.com/Owryn) · Built on [xReveres/HeroZServer](https://github.com/xReveres/HeroZServer) · Flash emulation by [Ruffle.rs](https://ruffle.rs/)

[![Ko-fi](https://img.shields.io/badge/Ko--fi-support-ff5e5b?logo=ko-fi&logoColor=white)](https://ko-fi.com/owryn)

</div>
