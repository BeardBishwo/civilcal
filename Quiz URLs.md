# Quiz URLs (Localhost)
Base: `http://localhost/Bishwo_Calculator`

## Public / Auth Paths
- Portal: [http://localhost/Bishwo_Calculator/quiz](http://localhost/Bishwo_Calculator/quiz)
- Overview: `http://localhost/Bishwo_Calculator/quiz/overview/{slug}`
- Start exam (auth): `http://localhost/Bishwo_Calculator/quiz/start/{slug}`
- Exam room (auth): `http://localhost/Bishwo_Calculator/quiz/room/{id}`
- Save answer (POST, auth, CSRF): `http://localhost/Bishwo_Calculator/quiz/save-answer`
- Submit exam (POST, auth, CSRF): `http://localhost/Bishwo_Calculator/quiz/submit`
- Result (auth): `http://localhost/Bishwo_Calculator/quiz/result/{id}`
- Leaderboard: [http://localhost/Bishwo_Calculator/quiz/leaderboard](http://localhost/Bishwo_Calculator/quiz/leaderboard)

## Multiplayer
- Lobby list: [http://localhost/Bishwo_Calculator/quiz/multiplayer](http://localhost/Bishwo_Calculator/quiz/multiplayer) (auth)
- Lobby create (POST, auth): `http://localhost/Bishwo_Calculator/quiz/lobby/create`
- Lobby join (POST, auth): `http://localhost/Bishwo_Calculator/quiz/lobby/join`
- Lobby view (auth): `http://localhost/Bishwo_Calculator/quiz/lobby/{code}`
- Lobby status (auth): `http://localhost/Bishwo_Calculator/api/lobby/{code}/status`
- Lobby wager (POST, auth): `http://localhost/Bishwo_Calculator/api/lobby/wager`

## Gamified City/Shop (Auth)
- City: [http://localhost/Bishwo_Calculator/quiz/city](http://localhost/Bishwo_Calculator/quiz/city)
- Shop: [http://localhost/Bishwo_Calculator/quiz/shop](http://localhost/Bishwo_Calculator/quiz/shop)
- Sawmill: [http://localhost/Bishwo_Calculator/quiz/sawmill](http://localhost/Bishwo_Calculator/quiz/sawmill)
- Battle pass: [http://localhost/Bishwo_Calculator/quiz/battle-pass](http://localhost/Bishwo_Calculator/quiz/battle-pass)
- Build (POST): `http://localhost/Bishwo_Calculator/api/city/build`
- Craft (POST): `http://localhost/Bishwo_Calculator/api/city/craft`
- Purchase lifeline (POST): `http://localhost/Bishwo_Calculator/api/shop/purchase`
- Purchase resource (POST): `http://localhost/Bishwo_Calculator/api/shop/purchase-resource`
- Sell resource (POST): `http://localhost/Bishwo_Calculator/api/shop/sell-resource`
- Purchase bundle (POST): `http://localhost/Bishwo_Calculator/api/shop/purchase-bundle`
- Use lifeline (POST): `http://localhost/Bishwo_Calculator/api/quiz/use-lifeline`
- Claim battle pass (POST): `http://localhost/Bishwo_Calculator/api/battle-pass/claim`

## In-Quiz Lifeline API
- Use lifeline (POST, auth): `http://localhost/Bishwo_Calculator/api/quiz/lifeline/use`

## Admin Quiz Module (Auth + Admin)
- Dashboard: [http://localhost/Bishwo_Calculator/admin/quiz](http://localhost/Bishwo_Calculator/admin/quiz)
- Syllabus: [http://localhost/Bishwo_Calculator/admin/quiz/syllabus](http://localhost/Bishwo_Calculator/admin/quiz/syllabus)
  - Categories store/update/delete (POST): `/admin/quiz/categories/store`, `/admin/quiz/categories/update/{id}`, `/admin/quiz/categories/delete/{id}`
  - Subjects store/update/delete (POST): `/admin/quiz/subjects/store`, `/admin/quiz/subjects/update/{id}`, `/admin/quiz/subjects/delete/{id}`
  - Topics store/update/delete (POST): `/admin/quiz/topics/store`, `/admin/quiz/topics/update/{id}`, `/admin/quiz/topics/delete/{id}`
  - AJAX: `/admin/quiz/get-subjects/{id}`, `/admin/quiz/get-topics/{id}`
- Question bank: [http://localhost/Bishwo_Calculator/admin/quiz/questions](http://localhost/Bishwo_Calculator/admin/quiz/questions)
  - Create: `/admin/quiz/questions/create`
  - Store (POST): `/admin/quiz/questions/store`
  - Edit: `/admin/quiz/questions/edit/{id}`
  - Update (POST): `/admin/quiz/questions/update/{id}`
  - Delete (POST): `/admin/quiz/questions/delete/{id}`
  - Search JSON: `/admin/quiz/questions/search`
  - Import page: `/admin/quiz/import`
  - Import upload (POST): `/admin/quiz/import/upload`
- Leaderboard: [http://localhost/Bishwo_Calculator/admin/quiz/leaderboard](http://localhost/Bishwo_Calculator/admin/quiz/leaderboard)
- Exams: [http://localhost/Bishwo_Calculator/admin/quiz/exams](http://localhost/Bishwo_Calculator/admin/quiz/exams)
  - Create: `/admin/quiz/exams/create`
  - Store (POST): `/admin/quiz/exams/store`
  - Edit: `/admin/quiz/exams/edit/{id}`
  - Update (POST): `/admin/quiz/exams/update/{id}`
  - Builder: `/admin/quiz/exams/builder/{id}`
  - Add question (POST): `/admin/quiz/exams/add-question`
  - Remove question (POST): `/admin/quiz/exams/remove-question`
  - Analytics: `/admin/quiz/analytics`

## Notes
- Auth pages require login; admin pages require admin role.
- POST endpoints need proper CSRF/auth headers per your middleware.
- Replace `{slug}`, `{id}`, `{code}` with actual values when testing.
