<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use Exception;

class Rugby extends BaseSport
{
    const SPORT_ALIAS = "rugby";

    const API_ENDPOINT_CATEGORIES = "leagues";
    const API_ENDPOINT_GAMES = "games";
    const API_ENDPOINT_ODDS = "odds";
    const API_ENDPOINT_ODD_BET_TYPE = "odds/bets";

    const IN_PROGRESS_STATUSES = [
        '1H', // First Half (In Play)
        '2H', // Second Half (In Play)
        'HT', // Half Time (In Play)
        'ET', // Extra Time (In Play)
        'BT', // Break Time (In Play)
        'PT', // Penalties Time (In Play)
        'INTR', // Interrupted
    ];

    const FINISH_STATUSES = [
        'FT',   // Finished (Game Finished)
        'AET',  // After Extra Time (Game Finished)

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
                static::TEAM_HOME_ALIAS => $record["scores"]["home"] ?? null,
                static::TEAM_AWAY_ALIAS => $record["scores"]["away"] ?? null,
            ];
        }

        if ($type === self::SPORT_COMPLETE_RESULTS) {
            $data = [
                "scores" => $record["scores"] ?? null,
                "periods" => $record["periods"] ?? null,
            ];
        }

        return $data;
    }
}
