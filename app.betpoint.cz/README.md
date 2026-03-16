# Documentation App.betpoint.cz

- Created by: 24U s.r.o.

## User technologies

- MySQL  
- PHP 8.4 (Yii 2 Advanced template)  
- React 18  
- Node 20  
- Webpack 5  
- Cron  

---

## Install guide

Follow these steps to install the project.

In the root folder, run these commands:

1. `composer install`  
2. Set up your cookie validation key for **api-sports.io** at `environments/prod/backend/config/main-local.php`.  
3. `php init` - select production (1).  
4. `mkdir -p console/runtime/logs/cron/` - create this folder for cron logs that are required for reviews.  
5. Copy the local Webpack config `webpack.config.local.default.js` into `webpack.config.local.js` and edit the corresponding values in your local copy.  
6. `npm install`  
7. `npm run icon-font`

---

### Initial startup

Before the first launch of the website, run these commands:

1. `./yii migrate`  
2. `./yii migrate --migrationPath=@yii/rbac/migrations`  
3. `./yii rbac/init`  
4. `./yii rbac/create-user user@example.com 'X123456' 'John' 'Doe'` - creates a default user; you can use your own data (`./yii rbac/create-user #email '#password' '#firstname' '#surname'`).  
5. `./yii rbac/add-role-to-user 'role_admin' 'user@example.com'` - assigns the admin role to the user created in step 4 (the email must match).  
6. `./yii migrate --migrationPath=@app/migrations/core-data`  
7. `./yii api-sync/sync-categories`  
8. `./yii category/enable-default-categories`  
9. `./yii api-sync/sync-odd-bet-type`  
10. `./yii odd-bet-type/enable-and-configure-default-odd-bet-types`  
11. `./yii api-sync/sync-matches`  
12. `./yii api-sync/sync-odds`  
13. Start the crons from `app.betpoint.cz/cron`.

---

### Restarting the server if crons failed or stopped running

Run the Yii commands in the same order as in the main cron sync script:

1. `./yii clear-data/database-data`  
2. `./yii api-sync/sync-categories`  
3. `./yii category/enable-default-categories`  
4. `./yii api-sync/sync-odd-bet-type`  
5. `./yii odd-bet-type/enable-and-configure-default-odd-bet-types`  
6. `./yii api-sync/sync-matches`  
7. `./yii api-sync/sync-odds`  
8. `./yii api-sync/sync-latest-matches-from-today-to-history`  
9. `./yii bets/evaluate`  
10. Start the crons from `app.betpoint.cz/cron`.

---

### Starting cron

```bash
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin

# Clearing the logs folder
2 1 4 * * cd /path_to_project/cron && /usr/bin/php clearLogs.php
# The rest is handled by dispatcher every minute
* * * * * cd /path_to_project/cron && /usr/bin/php cronDispatcher.php
---

## Developer mode

For development, Docker is used. You will need to install Docker to proceed.

---

## Docker Setup

1. Navigate to the `betpoint-dev-proxy` folder.  
2. Follow the steps in the README file located at `betpoint-dev-proxy/README.md`.

---

### Docker image setup

1. Set up the `.env` file.  
2. Configure your credentials in `environments/dev/common/config/main-local.php`.  
3. Set your cookie validation key for **api-sports.io** in `environments/dev/backend/config/main-local.php`.  
4. Run `docker compose up -d` — starts the Docker container.  
   - `docker exec -it betpoint bash` — to enter the container.  
   - `php init` — select development (0).  
   - Continue with the same steps as in the production installation, but execute them inside the Docker container. Skip `npm install` and `composer install`.  
5. URLs for the web app and database are defined in `.env`:  
   - **Page URL:** `http://#Domain`  
   - **Database URL:** `http://pma.#Domain`  

---

### Scripts explanation

**This method downloads categories and seasons for all sports defined in the database via the API.**
``` 
./yii api-sync/sync-categories
``` 
Execute script below when category selections have changed or during the setup of a new project. 
This script will set-up categories as enabled based pre-defined set in 'params.php' configuration file.

```
./yii category/enable-default-categories
```

**This method downloads odd bet type for all sports defined in the database via the API.**
```
./yii api-sync/sync-odd-bet-type
``` 

**This configures and enables the default bet types.**
Run the script below if the selection of odd bet types has changed or when setting up a new project.
The script enables odd bet types based on the predefined set specified in the params.php configuration file.
If you’ve enabled any new odd bet types, don’t forget to define their evaluation conditions in @console/controllers/BetsController.php.

```
./yii odd-bet-type/enable-and-configure-default-odd-bet-types
```

**This method downloads matches for all sports with enabled category/league deined in the database via the API.**


```
./yii api-sync/sync-matches
``` 

**This method updates key match information, such as live status and progress, and retrieves results for completed matches.**
It should be executed every minute in the production environment.
```
./yii api-sync/sync-latest-matches-from-today-to-history
```

**This method retrieves odds via the API for all matches in the database with enabled categories/leagues and active seasons.**
```
./yii api-sync/sync-odds
```

**Once match results are downloaded and a user has placed a bet, the script below evaluates that bet and create transactions.**
```
./yii bets/evaluate
```

**Run when action/description functions are updated to adjust existing Transaction labels:**  
```
./yii transaction/update-labels
```
  
**The script below deletes odds, sport_matches, and sport_match_results for matches that have already finished and have no associated bets.**

```
./yii clear-data/database-data
```

**The script below deletes outdated cron logs.**

```
./yii clear-data/clear-logs
```

#### API definition

Complete API definition is dumped as Postman collection JSON export at `resources/api.postman_collection.json`. Only `frontend` app utilizes API.

#### External API services

Application exposes following public API endpoints:

##### Leaderboard
GET: ```<frontend_app>/api/leaderboard```

Returns a JSON-formatted leaderboard. Data is cached for a specified duration, so no database queries are made if the leaderboard is requested within the cache period. The number of records returned is defined by the application param configuration:

```
'leaderboard.maxAge' => 3600, // seconds
'leaderboard.maxUsers' => 100, // maximum users to return in the leaderboard
```

