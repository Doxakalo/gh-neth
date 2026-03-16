<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use Exception;

class Football extends BaseSport
{
    const SPORT_ALIAS = "football";

    const API_ENDPOINT_CATEGORIES = "leagues";
    const API_ENDPOINT_GAMES = "fixtures";
    const API_ENDPOINT_ODDS = "odds";
    const API_ENDPOINT_ODD_BET_TYPE = "odds/bets";

    const IN_PROGRESS_STATUSES = [
        '1H',   // First Half, Kick Off
        'HT',   // Halftime
        '2H',   // Second Half, 2nd Half Started
        'ET',   // Extra Time
        'BT',   // Break Time
        'P',    // Penalty In Progress
        'LIVE', // In Progress (rare case)
        'INT',  // Match Interrupted
        'SUSP', // Match Suspended
    ];

    const FINISH_STATUSES = [
        'FT',   // Match Finished in regular time
        'AET',  // Match Finished after extra time
        'PEN',  // Match Finished after penalty shootout

        // ----- NOT PLAYED STATUSES - BUT FINISHED -----
        'AWD',  // Technical Loss / Awarded Defeat
        'AW',  // Awarded
        'WO',   // WalkOver
    ];

    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Maps the raw data from the API to a structured format for insert to the database.
     *
     * @param string $type The type of data being mapped (e.g., categories, fixtures, odd bet types).
     * @param array $record The raw record from the API.
     * @return array The structured data.
     */
    public static function dataMapper($type, $record)
    {
        if ($type === self::API_ENDPOINT_CATEGORIES) {
            $data = [
                "name" => $record["league"]["name"] ?? null,
                "id_vendor" => $record["league"]["id"],
                "country_name" => $record["country"]["name"] ?? null,
                "logo_url" =>  $record["league"]["logo"] ?? null,
                "seasons" => []
            ];

            if (isset($record["seasons"]) && !empty($record["seasons"])) {
                foreach ($record["seasons"] as $season) {
                    $data["seasons"][] = [
                        "year" => $season["year"],
                        "current" => intval($season["current"] ?? 0),
                        "odds" => intval($season["coverage"]["odds"] ?? 0)
                    ];
                }
            }
        }
        if ($type === self::API_ENDPOINT_GAMES) {
            $data = [
                "id_vendor" => $record["fixture"]["id"],
                "name" => ($record["teams"]["home"]["name"] ?? "N/A") . " - " . ($record["teams"]["away"]["name"] ?? "N/A"),
                "match_start" => $record["fixture"]["timestamp"],
                "extra" => intval($record["fixture"]["status"]["extra"] ?? 0),
                "status" => $record["fixture"]["status"]["short"] ?? null,
                "status_name" => $record["fixture"]["status"]["long"] ?? null,
                "home" => $record["teams"]["home"]["name"] ?? null,
                "away" => $record["teams"]["away"]["name"] ?? null,
                "detail" => $record,
                "season_year" => $record["league"]["season"] ?? null
            ];
        }

        if ($type === self::API_ENDPOINT_ODDS) {
            if (isset($record["bookmakers"]) && !empty($record["bookmakers"])) {
                foreach ($record["bookmakers"][0]["bets"] as $bet) {
                    foreach ($bet["values"] as $value) {
                        $data[] = [
                            "match_vendor_id" => $record["fixture"]["id"],
                            "name" => $value["value"],
                            "odd" => $value["odd"]
                        ];
                    }
                }
            }
        }


        if ($type === self::API_ENDPOINT_ODD_BET_TYPE) {
            $data = [
                "id_vendor" => $record["id"],
                "name" => $record["name"]
            ];
        }

        if ($type === self::SPORT_TOTAL_RESULTS) {
            $data = [
                static::TEAM_HOME_ALIAS => $record["goals"]["home"] ?? null,
                static::TEAM_AWAY_ALIAS => $record["goals"]["away"] ?? null,
            ];
        }

        if ($type === self::SPORT_COMPLETE_RESULTS) {
            $data = [
                "goals" => $record["goals"] ?? null,
                "score" => $record["score"] ?? null,
            ];
        }

            return $data;
    }
}