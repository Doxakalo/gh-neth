<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use Exception;

class Baseball extends BaseSport
{
    const SPORT_ALIAS = "baseball";

    const API_ENDPOINT_CATEGORIES = "leagues";
    const API_ENDPOINT_GAMES = "games";
    const API_ENDPOINT_ODDS = "odds";
    const API_ENDPOINT_ODD_BET_TYPE = "odds/bets";

    const IN_PROGRESS_STATUSES = [
        'IN1',  // Inning 1 (In Play)
        'IN2',  // Inning 2 (In Play)
        'IN3',  // Inning 3 (In Play)
        'IN4',  // Inning 4 (In Play)
        'IN5',  // Inning 5 (In Play)
        'IN6',  // Inning 6 (In Play)
        'IN7',  // Inning 7 (In Play)
        'IN8',  // Inning 8 (In Play)
        'IN9',  // Inning 9 (In Play)
        'INTR', // Interrupted
    ];

    const FINISH_STATUSES = [
        'FT',   // Finished (Game Finished)
    ];


    public function __construct()
    {
        parent::__construct();
    }

    public static function dataMapper($type, $record)
    {
        $data = [];
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
                        "current" => intval($season["current"] ?? 0),
                        "odds" => null
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

        if ($type === self::API_ENDPOINT_ODD_BET_TYPE) {
            $data = [
                "id_vendor" => $record["id"],
                "name" => $record["name"]
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
