## NicholasLeaderboard

<center><img src='leaderboard.png' width=150 height=150</img></center>

A Simple NicholasLeaderboard Plugin

## Features

| NicholasLeaderboard | Description                                                  |
| ------------------- | ------------------------------------------------------------ |
| Friendly Command    | Easy to manage                                               |
| TopNPC              | Get top 1 player skin with different player every identifier |

## Command

| Command                | Description                    | Permissions                   | Aliases |
| ---------------------- | ------------------------------ | ----------------------------- | ------- |
| `/nicholasleaderboard` | `NicholasLeaderboard Commands` | `nicholasleaderboard.command` | `/nl`   |

- /nl create/spawn (identifier) (type) --> Example: /nl create kills text
- /nl delete/remove (identifier) (type) (id) --> Example: /nl delete kills text 1
- /nl entities/topnpc --> Show all TopNPC entity (custom_id, identifier, type)
- /nl help --> Show help page
- /nl list --> Show identifier list
- /nl top (identifier) --> Show top player according to identifier

## Identifier NicholasLeaderboard

- breaks --> Count player block break
- deaths --> Count player deaths
- jumps --> Count player jumps
- kills --> Count player kills
- places --> Count player block place
- xp --> Count player xp

## Type NicholasLeaderboard

- human --> Top Ranks Player ⚠️ Warn: Stil under development
- text --> Simple FloatingText

## Where i can get custom_id ?

- Case 1 Open plugin_data/NicholasLeaderboard/top_leaderboard_entity.json
- Case 2 Execute Command /nl entities

## TODO List

- [] Can Use Different Type
- [] Clean and tidy code
- [] Simple UI NicholasLeaderboard Manager

## Example Configuration

```yaml
# NICHOLASLEADERBOARD
# Tag
# {identifier} --> NicholasLeaderboard Type ["breaks", "deaths", "jumps", "kills", "xp"]
# {player} --> Player Name
# {rank} --> Top Ranks
# {value} --> Data

# NICHOLASLEADERBOARD FORMAT
top-message-format: "§f#§a{rank} §e{player} §f{identifier} {value}" # Top Leaderboard Format

# TITLE NICHOLASLEADERBOARD FORMAT
breaks: "§6[§e BREAKS NicholasLeaderboard §6]" # Title of breaks leaderboard
deaths: "§6[§e DEATHS NicholasLeaderboard §6]" # Title of deaths leaderboard
jumps: "§6[§e JUMPS NicholasLeaderboard §6]" # Title of jumps leaderboard
kills: "§6[§e KILLS NicholasLeaderboard §6]" # Title of kills leaderboard
places: "§6[§e PLACES NicholasLeaderboard §6]" # Title of places leaderboard
xp: "§6[§e XP NicholasLeaderboard §6]" # Title of xp leaderboard
```

## Example Data

```yaml
{
  "1":
    {
      "identifier": "xp",
      "type": "human",
      "world": "world",
      "position": { "x": 306.6509, "y": 73, "z": 274.0091 },
    },
}
```

## Credits

<a target="_blank" href="https://icons8.com/icon/X2Bsuwu66e8y/leaderboard">Leaderboard</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a>
