# Quiz View File Guide

This document provides a mapping of the various quiz-related view files in the Bishwo Calculator project, their functionality, and the browser links required to access them.

## Quiz View File Mapping & Links

| View File Path | Functionality & Use Case | Browser URL (Full Link) |
| :--- | :--- | :--- |
| **Analysis** | | |
| `analysis/report.php` | Detailed performance report shown after completing a quiz attempt. | [http://localhost/Bishwo_Calculator/quiz/result/{id}](http://localhost/Bishwo_Calculator/quiz/result/{id}) |
| **Arena** | | |
| `arena/room.php` | The main interface where users take a live quiz and answer questions. | [http://localhost/Bishwo_Calculator/quiz/room/{id}](http://localhost/Bishwo_Calculator/quiz/room/{id}) |
| **Firms (Guilds)** | | |
| `firms/dashboard.php` | Dashboard for a specific engineering firm/guild members. | [http://localhost/Bishwo_Calculator/quiz/firms/dashboard](http://localhost/Bishwo_Calculator/quiz/firms/dashboard) |
| `firms/index.php` | The main page to list, search, and join existing engineering firms. | [http://localhost/Bishwo_Calculator/quiz/firms](http://localhost/Bishwo_Calculator/quiz/firms) |
| **Games** | | |
| `games/blueprint_arena.php` | The gameplay interface for the Terminology "Blueprint" game. | [http://localhost/Bishwo_Calculator/blueprint/arena/{id}](http://localhost/Bishwo_Calculator/blueprint/arena/{id}) |
| `games/blueprint_list.php` | List of available terminology blueprint challenges. | [http://localhost/Bishwo_Calculator/blueprint](http://localhost/Bishwo_Calculator/blueprint) |
| `games/contest_result.php` | Results page for a specific competitive student contest. | [http://localhost/Bishwo_Calculator/quiz/contests/result/{id}](http://localhost/Bishwo_Calculator/quiz/contests/result/{id}) |
| `games/contest_room.php` | Interface for participating in a live competitive contest. | [http://localhost/Bishwo_Calculator/quiz/contests/room/{id}](http://localhost/Bishwo_Calculator/quiz/contests/room/{id}) |
| `games/contests_list.php" | Directory of all active and upcoming quiz contests. | [http://localhost/Bishwo_Calculator/quiz/contests](http://localhost/Bishwo_Calculator/quiz/contests) |
| **Gamification** | | |
| `gamification/battle_pass.php` | Battle Pass UI showing progression tiers and claimed rewards. | [http://localhost/Bishwo_Calculator/quiz/battle-pass](http://localhost/Bishwo_Calculator/quiz/battle-pass) |
| `gamification/city.php` | The "Civil City" metagame hub for building construction/planning. | [http://localhost/Bishwo_Calculator/quiz/city](http://localhost/Bishwo_Calculator/quiz/city) |
| `gamification/sawmill.php` | Resource gathering and crafting center for the city gamification. | [http://localhost/Bishwo_Calculator/quiz/sawmill](http://localhost/Bishwo_Calculator/quiz/sawmill) |
| `gamification/shop.php` | Marketplace to buy lifelines and construction resources. | [http://localhost/Bishwo_Calculator/quiz/shop](http://localhost/Bishwo_Calculator/quiz/shop) |
| **Leaderboard** | | |
| `leaderboard/index.php` | Global rankings showing top players by rank and score. | [http://localhost/Bishwo_Calculator/leaderboard](http://localhost/Bishwo_Calculator/leaderboard) |
| **Multiplayer** | | |
| `multiplayer/lobby.php` | Waiting room for multiplayer battles (Ghost mode). | [http://localhost/Bishwo_Calculator/quiz/lobby/{code}](http://localhost/Bishwo_Calculator/quiz/lobby/{code}) |
| `multiplayer/menu.php` | Main multiplayer entry menu to choose battle type. | [http://localhost/Bishwo_Calculator/quiz/multiplayer](http://localhost/Bishwo_Calculator/quiz/multiplayer) |
| **Portal** | | |
| `portal/index.php` | The main Quiz Portal dashboard (Home for all quizzes). | [http://localhost/Bishwo_Calculator/quiz](http://localhost/Bishwo_Calculator/quiz) |
| `portal/overview.php` | The introduction and instruction page before starting an exam. | [http://localhost/Bishwo_Calculator/quiz/overview/{slug}](http://localhost/Bishwo_Calculator/quiz/overview/{slug}) |

---

### Important Notes

1. **URLs with placeholders**: For links like `http://localhost/Bishwo_Calculator/quiz/result/{id}`, you will need a valid `ID` from your database (e.g., `http://localhost/Bishwo_Calculator/quiz/result/1`).
2. **Slug**: For the portal overview, use the exam slug (e.g., `http://localhost/Bishwo_Calculator/quiz/overview/civil-engineering-basics`).
3. **Authentication**: Most of these pages require you to be logged in with a valid user session.
