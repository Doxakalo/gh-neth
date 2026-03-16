<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use Exception;

class Basketball extends BaseSport
{
    const SPORT_ALIAS = "basketball";

    // ENDPOINTS
    const API_ENDPOINT_CATEGORIES = "leagues";
    const API_ENDPOINT_GAMES = "games";
    const API_ENDPOINT_ODDS = "odds";
    const API_ENDPOINT_ODD_BET_TYPE = "bets";

    const IN_PROGRESS_STATUSES = [
        'Q1',   // Quarter 1 (In Play)
        'Q2',   // Quarter 2 (In Play)
        'Q3',   // Quarter 3 (In Play)
        'Q4',   // Quarter 4 (In Play)
        'OT',   // Over Time (In Play)
        'BT',   // Break Time (In Play)
        'HT',   // Halftime (In Play)
        'SUSP', // Game Suspended
    ];

    const FINISH_STATUSES = [
        'FT',   // Game Finished (Game Finished)
        'AOT',  // After Over Time (Game Finished)

        // ----- NOT PLAYED STATUSES - BUT FINISHED -----
        'AWD',  // Technical Loss / Awarded Defeat
        'AW',  // Awarded
        'WO',   // WalkOver
    ];

    public function __construct()
    {
        parent::__construct();
    }

    public static function dataMapper($type, $record)
    {
        if ($type === self::API_ENDPOINT_CATEGORIES) {
            $data = [
                "name" => $record["name"] ?? null,
                "id_vendor" => $record["id"],
                "country_name" => $record["country"]["name"] ?? null,
                "logo_url" =>  $record["logo"] ?? null,
                "seasons" => []
            ];

            if (isset($record["seasons"]) && !empty($record["seasons"])) {
                foreach ($record["seasons"] as $season) {
                    $data["seasons"][] = [
                        "year" => $season["season"],
                        "current" => null,
                        "odds" => $season["coverage"]["odds"]
                    ];
                }
            }
        }

        if ($type === self::API_ENDPOINT_GAMES) {
            $data = [
                "id_vendor" => $record["id"],
                "name" => ($record["teams"]["home"]["name"] ?? "N/A") . " - " . ($record["teams"]["away"]["name"] ?? "N/A"),
                "match_start" => $record["timestamp"],
                "extra" => 0,
                "status" => $record["status"]["short"] ?? null,
                "status_name" => $record["status"]["long"] ?? null,
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
                            "match_vendor_id" => $record["game"]["id"],
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
                static::TEAM_HOME_ALIAS => $record["scores"]["home"]["total"] ?? null,
                static::TEAM_AWAY_ALIAS => $record["scores"]["away"]["total"] ?? null,
            ];
        }

        if ($type === self::SPORT_COMPLETE_RESULTS) {
            $data = [
                "scores" => $record["scores"] ?? null,
            ];
        }

        return $data;
    }
}
