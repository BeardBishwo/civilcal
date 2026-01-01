# Rank Assets Folder and Naming Convention

## Folder Placement
Use the theme-aware resources path so assets stay organized and swappable by theme.
```
themes/
└── default/
    └── assets/
        └── resources/
            ├── currency/
            ├── building/
            ├── material/
            └── ranks/      <-- add this
```

## File Names (sorted with leading zeros)
| Rank | File Name | Description |
| --- | --- | --- |
| 1. Intern | `rank_01_intern.png` | Bronze Helmet |
| 2. Surveyor | `rank_02_surveyor.png` | Silver Theodolite |
| 3. Supervisor | `rank_03_supervisor.png` | Gold Clipboard |
| 4. Assistant | `rank_04_assistant.png` | Platinum Crane |
| 5. Senior | `rank_05_senior.png` | Diamond Laptop |
| 6. Manager | `rank_06_manager.png` | Crown City |
| 7. Chief | `rank_07_chief.png` | Golden Conqueror Helmet |

## Theme-Compatible Usage
If your theme assets are publicly served under `themes/default/assets/`, reference:
```html
<img src="themes/default/assets/resources/ranks/rank_01_intern.png" alt="Intern Badge">
```
If you have a helper (e.g., `theme_url()`), prefer that for multi-theme support:
```php
$slug = strtolower($user['rank_slug']); // e.g., "supervisor"
$level = str_pad($user['rank_level'], 2, '0', STR_PAD_LEFT); // 1 -> 01
$icon = theme_url("assets/resources/ranks/rank_{$level}_{$slug}.png");
```

## Suggested PHP Snippet (auto-generate path)
```php
// $user['rank_level'] (1-7), $user['rank_slug'] (intern, surveyor, ...)
$rankLevel = str_pad((string)$user['rank_level'], 2, '0', STR_PAD_LEFT);
$rankSlug  = strtolower(preg_replace('/\s+/', '_', $user['rank_slug']));
$iconPath  = theme_url("assets/resources/ranks/rank_{$rankLevel}_{$rankSlug}.png");

echo "<img src='{$iconPath}' alt='Rank Badge' class='w-16 h-16'>";
```

## Notes
- Leading zeros keep files sorted in explorers and CDNs.
- Keep PNGs optimized (TinyPNG or ImageOptim) to reduce payload.
- If adding animations/variants, suffix with `_anim` or `_share` (e.g., `rank_03_supervisor_share.png`).
- Place dark/light variants in subfolders if needed: `ranks/dark/`, `ranks/light/`.

## Next Step (Optional)
Want a "Level Up" popup/animation snippet for promotions? I can supply CSS/HTML aligned to these filenames.
