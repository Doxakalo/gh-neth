<?php

namespace common\services;

use Yii;
use common\services\ApiSports;
use DateTime;
use Exception;
use common\models\Season;

class BaseSport
{
    const IN_PROGRESS_STATUSES = [];
    const FINISH_STATUSES = [];

    const CANCELLED_STATUSES = [
        'CANC', // Match Cancelled
    ];
    const ABANDONED_STATUSES = [
        'ABD',  // Match Abandoned
    ];
    const POSTPONED_STATUSES = [
        'PST',  // Match Postponed
        'POST', // Postponed alternative code
    ];

    const API_ENDPOINT_CATEGORIES = "";
    const API_ENDPOINT_GAMES = "";
    const API_ENDPOINT_ODDS = "";
    const API_ENDPOINT_ODD_BET_TYPE = "";

    const SPORT_ALIAS = "";
    const SPORT_TOTAL_RESULTS = "TOTAL_RESULTS";
    const SPORT_COMPLETE_RESULTS = "COMPLETE_RESULTS";
    const TEAM_HOME_ALIAS = "home";
    const TEAM_AWAY_ALIAS = "away";


    private $api;
    private $config;
    private $season;

    public function __construct($config = null)
    {
        $this->api = new ApiSports(static::SPORT_ALIAS, Yii::$app->params['apiSportsKey']);
        $this->config = Yii::$app->params['sports'][static::SPORT_ALIAS] ?? null;
    }

    /**
     * Returns the categories of the sport.
     *
     * This method retrieves the categories (leagues) for the specified sport from the API.
     * It maps the raw data to a structured format and returns an associative array of categories.
     *
     * @return array An associative array of categories with vendor IDs as keys.
     */
    public function getCategories()
    {
        $catagoriesRaw = $this->api->createRequest(static::API_ENDPOINT_CATEGORIES);

        $categories = [];

        foreach ($catagoriesRaw["response"] as $category) {
            $data = static::dataMapper(static::API_ENDPOINT_CATEGORIES, $category);

            if (!empty($data)) {
                $categories[$data["id_vendor"]] = $data;
            }
        }

        return $categories;
    }

    /*
    * Returns an array of sport odd bet types.
    *
    * This method retrieves the available odd bet types for the specified sport from the API.
    * It maps the raw data to a structured format and returns an associative array of bet types.
    *
    * @return array An associative array of sport odd bet types with vendor IDs as keys.
    */
    public function getSportOddBetsTypes()
    {
        $sportOddBetTypeRaw = $this->api->createRequest(static::API_ENDPOINT_ODD_BET_TYPE);

        $sportOddBetTypes = [];

        foreach ($sportOddBetTypeRaw["response"] as $sportOddBetType) {
            $data = static::dataMapper(static::API_ENDPOINT_ODD_BET_TYPE, $sportOddBetType);

            if (!empty($data)) {
                $sportOddBetTypes[$data["id_vendor"]] = $data;
            }
        }

        return $sportOddBetTypes;
    }


    /*     
     * Returns an array of upcoming matches for active seasons by category ID.
     *
     * This method retrieves upcoming matches for the specified category and active seasons.
     * It can optionally filter matches based on a range of dates.
     *
     * @param array $category The category data containing the vendor ID.
     * @param bool|array $rangeOfDates Optional. If false, no date filtering is applied. If an array, it should contain 'from' and 'to' dates.
     * @return array An associative array of upcoming matches with vendor IDs as keys.
     */
    public function getUpcomingMatchesForActiveSeasonsByCategoryId($category, $rangeOfDates = false)
    {
        $params = [
            "league" => $category["id_vendor"],
        ];


        $seasonType = Season::getOneSeasonsByCategoryId($category['id']);
        
        $seasonTypePass = $seasonType['year'];

        $seasonsParams = self::getActiveSportSeasonsProp(1 ,$seasonTypePass);
        //var_dump($seasonsParams);
        

        $games = [];
        foreach ($seasonsParams as $seasonParam) {

            if ($rangeOfDates === false) {
                unset($seasonParam["to"]);
                unset($seasonParam["from"]);
            }
            //var_dump($seasonsParams);
            $searchParams = array_merge($params, $seasonParam);
            if ($rangeOfDates === true) {
    echo "<pre>search_params => " . json_encode($searchParams, JSON_PRETTY_PRINT) . "</pre>";
    // NE exit, aby foreach pokračoval
}
            
            $gamesRaw = $this->api->createRequest(static::API_ENDPOINT_GAMES, $searchParams);
            
            foreach ($gamesRaw["response"] as $game) {
                $data = static::dataMapper(static::API_ENDPOINT_GAMES, $game);

                if (!empty($data)) {
                    $games[$data["id_vendor"]] = $data;
                }
            }
        }

        return $games;
    }

    /*
     * Returns an array of today's sport matches.
     *
     * This method retrieves matches scheduled for today from the API.
     * It maps the raw data to a structured format and returns an associative array of matches.
     *
     * @return array An associative array of today's sport matches with vendor IDs as keys.
     */
    public function getMatchesForDaysFromTodayToHistory($loadUntilLastDays = 0)
    {
        $games = [];

        for($i = 0; $i <= $loadUntilLastDays; $i++) {
            $currentDateTime = new DateTime();
            $currentDateTime->modify("-" . $i . " days");
            $date = $currentDateTime->format("Y-m-d");

            $params = [
                "date" => $date,
            ];
            $gamesRaw = $this->api->createRequest(static::API_ENDPOINT_GAMES, $params);

            foreach ($gamesRaw["response"] as $game) {
                $data = static::dataMapper(static::API_ENDPOINT_GAMES, $game);

                if (!empty($data) && !isset($games[$data["id_vendor"]])) {
                    $games[$data["id_vendor"]] = $data;
                }
            }
        }

        return $games;
    }


    /*     
     * Returns an array of odds for a specific category, season year, and odd bet type.
     *
     * This method retrieves odds from the API based on the provided parameters.
     * It maps the raw data to a structured format and returns an array of odds.
     *
     * @param int $categoryVendorId The vendor ID of the category (league).
     * @param int $seasonYear The year of the season.
     * @param int $oddBetTypeVendorId The vendor ID of the odd bet type.
     * @return array An array of odds with mapped data.
     */
    public function getOdds($searchQueryParams = [])
    {

        $initPage = 1;
        $odds = [];

        $result = $this->getOddsForSpecificPage($searchQueryParams, $initPage);
        $odds = array_merge($odds, $result["odds"]);

        $maximumPages = $result["paging"]["total"] ?? 1;

        if ($initPage <= $maximumPages) {
            $initPage += 1;
            for ($i = $initPage; $i <= $maximumPages; $i++) {
                $result = $this->getOddsForSpecificPage($searchQueryParams, $i);

                $odds = array_merge($odds, $result["odds"]);
            }
        }

        return $odds;
    }

    private function getOddsForSpecificPage($searchQueryParams, $page)
    {
        if ($page > 1) {
            $searchQueryParams['page'] = $page;
        }
        
        $oddsRaw = $this->api->createRequest(static::API_ENDPOINT_ODDS, $searchQueryParams);

        $result = [
            "paging" => $oddsRaw["paging"] ?? [],
            "odds" => []
        ];
       
        foreach ($oddsRaw["response"] as $odd) {
            $data = static::dataMapper(static::API_ENDPOINT_ODDS, $odd);

            if (!empty($data)) {
                $result["odds"][] = $data;
            }
        }

        return $result;
    }

    /*	 
	 * Returns an array of sport seasons with start and end dates based on the current date.
	 *
	 * This method is used to prepare season parameters for the Sport API request. It generates
	 * one or more seasons starting from the current date until the end of the current year,
	 * and full years for subsequent seasons.
	 * 
	 * @param int $rangeOfSeasons The number of seasons to include, starting from the current year.
	 * @return array An array of associative arrays, each containing 'season', 'from', and 'to' keys.
	 */
  public static function getActiveSportSeasonsProp(int $rangeOfSeasons = 1, ?string $seasonYear = null)
    {
        $seasons = [];
        $today = date('Y-m-d'); // dnes

        if ($seasonYear !== null) {
            // dvouletá sezóna, např. "2025-2026"
            if (preg_match('/^(\d{4})-(\d{4})$/', $seasonYear)) {
                $season = [
                    'season' => $seasonYear,
                    'date'   => $today, // necháváme beze změny
                ];
            } else {
                // jednoroční sezóna, vezmeme minulý a aktuální rok
                $year = (int)$seasonYear;
                $season = [
                    'season' => $seasonYear,
                    'from'   => ($year - 1) . '-01-01',
                    'to'     => ($year + 1) . '-12-31',
                ];
            }

            $seasons[] = $season;
            return $seasons;
        }

        // fallback: generujeme podle aktuálního roku
        $currentYear = (int)date('Y');
        for ($yearOffset = 0; $yearOffset <= $rangeOfSeasons; $yearOffset++) {
            $year = $currentYear - 1 + $yearOffset; // minulý rok + offset
            $seasonStart = new DateTime(($year - 1) . '-01-01'); // minulý rok začátek
            $seasonEnd   = new DateTime($year . '-12-31');       // aktuální rok konec

            $seasons[] = [
                'season' => (string)$year,
                'from'   => $seasonStart->format('Y-m-d'),
                'to'     => $seasonEnd->format('Y-m-d'),
            ];
        }

        return $seasons;
    }


    public static function dataMapper($type, $record)
    {
        // This method should be overridden in child classes to handle specific data mapping
        return [];
    }

    public static function getInProgressStatuses()
    {
        return static::IN_PROGRESS_STATUSES;
    }

    public static function getAbandonedStatuses()
    {
        return static::ABANDONED_STATUSES;
    }

    public static function getFinishStatuses()
    {
        return static::FINISH_STATUSES;
    }

    public static function getCancelledStatuses()
    {
        return static::CANCELLED_STATUSES;
    }

    public static function getPostponedStatuses()
    {
        return static::POSTPONED_STATUSES;
    }

    /**
     * 
     * Returns an array of inactive statuses for the sport. 
     * 'Inactive' means that the match is not upcoming or in-progress
     * 
     * * @return array An array of inactive statuses. 
     */
    public static function getInactiveStatuses()
    {
        return array_merge(
            static::getCancelledStatuses(),
            static::getAbandonedStatuses(),
            static::getPostponedStatuses(),
            static::getFinishStatuses()
        );
    }
    
    public function getConfig()
    {
        return $this->config;
    }

    /*
     * Returns the sport class based on the provided alias.
     *
     * This method constructs the fully qualified class name for the sport service
     * based on the provided alias and checks if the class exists.
     *
     * @param string $sportAlias The alias of the sport (e.g., 'football', 'basketball').
     * @return string The fully qualified class name of the sport service.
     * @throws Exception If the class does not exist.
     */
    public static function getSportClassByAlias($sportAlias)
    {
        $sportClass = "\\common\\services\\" . ucfirst($sportAlias);
        if (class_exists($sportClass)) {
            return $sportClass;
        }
        throw new Exception("Sport class for alias '$sportAlias' does not exist.");
    }

    public static function validateRestructuredTotalMatchResults(array $data){
        $result = true;
        if(isset($data[self::TEAM_HOME_ALIAS]) && $data[self::TEAM_HOME_ALIAS] === null || !isset($data[self::TEAM_HOME_ALIAS])){
            $result = false;
        }
        if(isset($data[self::TEAM_AWAY_ALIAS]) && $data[self::TEAM_AWAY_ALIAS] === null || !isset($data[self::TEAM_AWAY_ALIAS])){
            $result = false;
        }
        return $result;
    }
}
