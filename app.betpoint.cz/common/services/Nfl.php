<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use Exception;

class Nfl extends BaseSport
{
    const SPORT_ALIAS = "nfl";

    const API_ENDPOINT_CATEGORIES = "leagues";
    const API_ENDPOINT_GAMES = "games";
    const API_ENDPOINT_ODDS = "odds";
    const API_ENDPOINT_ODD_BET_TYPE = "odds/bets";

    const IN_PROGRESS_STATUSES = [
        'Q1', // First Quarter (In Play)
        'Q2', // Second Quarter (In Play)
        'Q3', // Third Quarter (In Play)
        'Q4', // Fourth Quarter (In Play)
        'OT', // Overtime (In Play)
        'HT', // Halftime (In Play)
    ];

    const FINISH_STATUSES = [
        'FT',   // Finished (Game Finished)
        'AOT',  // After Over Time (Game Finished)
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
                        "odds" => null
                    ];
                }
            }
        }
        if ($type === self::API_ENDPOINT_GAMES) {
            $data = [
                "id_vendor" => $record["game"]["id"],
                "name" => ($record["teams"]["home"]["name"] ?? "N/A") . " - " . ($record["teams"]["away"]["name"] ?? "N/A"),
                "match_start" => $record["game"]["date"]["timestamp"],
                "extra" => 0,
                "status" => $record["game"]["status"]["short"] ?? null,
                "status_name" => $record["game"]["status"]["long"] ?? null,
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
                            "odd_bet_type_vendor_id" => $bet["id"],
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
