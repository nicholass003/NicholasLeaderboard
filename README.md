## NicholasLeaderboard

<center><img src='leaderboard.png' width=150 height=150</img></center>

A Simple NicholasLeaderboard Plugin

## Command

- /nl create/spawn <identifier> <type> --> Example: /nl create kills text
- /nl delete/remove <identifier> <type> <id> --> Example: /nl delete kills text 1
- /nl entities/topnpc --> Show all TopNPC entity (custom_id, identifier, type)
- /nl help --> Show help page
- /nl list --> Show identifier list
- /nl top <identifier> --> Show top player according to identifier

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

## Example Data

```yaml
{
  "1": #custom_id
    {
      "identifier": "xp",
      "type": "text",
      "world": "world",
      "position":
        { "x": 284.5861, "y": 68, "z": 260.7183, "yaw": 0, "pitch": 0 },
    },
  "2":
    {
      "identifier": "kills",
      "type": "text",
      "world": "world",
      "position":
        { "x": 284.9316, "y": 68, "z": 260.756, "yaw": 0, "pitch": 0 },
    },
}
```

## Credits

<a target="_blank" href="https://icons8.com/icon/X2Bsuwu66e8y/leaderboard">Leaderboard</a> icon by <a target="_blank" href="https://icons8.com">Icons8</a>
