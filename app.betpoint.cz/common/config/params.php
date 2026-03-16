<?php
return [
    'adminEmail' => 'admin@betpoint.cz',
    'supportEmail' => 'support@betpoint.cz',
    'senderEmail' => 'noreply@betpoint.cz',
    'senderName' => 'BetPoint',

	// Password
    'user.passwordResetTokenExpire' => 3600,
    'user.passwordMinLength' => 8,
	'user.passwordStrengthMatch' => '/^.*(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*$/',	

    // Funds
    'user.initialFunds' => 10000, // Initial funds for new users
    'user.initialFundsMin' => 1,
    'user.initialFundsMax' => 1000000,

    // Betting
    'bet.minimumAmount' => 1, // Minimum bet amount

    // Currency label
    'currency.label' => 'Betcoin',
    'currency.labelPlural' => 'Betcoins',

    // OddBetTypes
    'enabledOddBetTypes' => [
        [
            'names' => ['Match Winner'],
            'alias' => 'match-winner',
            'rank' => 10,
        ],
        [
            'names' => ['Home/Away'],
            'alias' => 'home-away',
            'rank' => 20,
        ],
        [
            'names' => ['Result/Total Goals'],
            'alias' => 'result-total-goals',
            'rank' => 60,
        ],
        [
            'names' => ['Total Hits'],
            'alias' => 'total-hits',
            'rank' => 31,
        ],
        [
            'names' => ['Total - Home'],
            'alias' => 'total-home',
            'rank' => 40,
        ],
        [
            'names' => ['Total - Away'],
            'alias' => 'total-away',
            'rank' => 50,
        ],
        [
            'names' => ['Over/Under', 'Goals Over/Under'],
            'alias' => 'over-under',
            'rank' => 30,
        ]
    ],

    //for backup not needed anymore
  /*  "enabledCategories" => [
        ['vendor_id' => 4, 'sport_id' => 1], // Euro Championship
        ['vendor_id' => 21, 'sport_id' => 1], // Confederations Cup
        ['vendor_id' => 61, 'sport_id' => 1], // Ligue 1
        ['vendor_id' => 144, 'sport_id' => 1], // Jupiler Pro League
        ['vendor_id' => 71, 'sport_id' => 1], // Serie A
        ['vendor_id' => 39, 'sport_id' => 1], // Premier League
        ['vendor_id' => 78, 'sport_id' => 1], // Bundesliga
        ['vendor_id' => 135, 'sport_id' => 1], // Serie A
        ['vendor_id' => 88, 'sport_id' => 1], // Eredivisie
        ['vendor_id' => 94, 'sport_id' => 1], // Primeira Liga
        ['vendor_id' => 140, 'sport_id' => 1], // La Liga
        ['vendor_id' => 179, 'sport_id' => 1], // Premiership
        ['vendor_id' => 180, 'sport_id' => 1], // Championship
        ['vendor_id' => 1, 'sport_id' => 1], // World Cup
        ['vendor_id' => 803, 'sport_id' => 1], // Asian Games
        ['vendor_id' => 62, 'sport_id' => 1], // Ligue 2
        ['vendor_id' => 2, 'sport_id' => 1], // UEFA Champions League
        ['vendor_id' => 310, 'sport_id' => 1], // Superliga
        ['vendor_id' => 186, 'sport_id' => 1], // Ligue 1
        ['vendor_id' => 187, 'sport_id' => 1], // Ligue 2
        ['vendor_id' => 42, 'sport_id' => 1], // League Two
        ['vendor_id' => 568, 'sport_id' => 1], // Eerste Divisie
        ['vendor_id' => 571, 'sport_id' => 1], // Vysshaya Liga
        ['vendor_id' => 333, 'sport_id' => 1], // Premier League
        ['vendor_id' => 302, 'sport_id' => 1], // League Cup
        ['vendor_id' => 269, 'sport_id' => 1], // Segunda División
        ['vendor_id' => 202, 'sport_id' => 1], // Ligue 1
        ['vendor_id' => 203, 'sport_id' => 1], // Süper Lig
        ['vendor_id' => 2, 'sport_id' => 3], // NPB
        ['vendor_id' => 51, 'sport_id' => 3], // NPB Minor League
        ['vendor_id' => 21, 'sport_id' => 3], // LMB
        ['vendor_id' => 5, 'sport_id' => 3], // KBO
        ['vendor_id' => 55, 'sport_id' => 3], // KBO Futures League
        ['vendor_id' => 1, 'sport_id' => 3], // MLB
        ['vendor_id' => 71, 'sport_id' => 3], // MLB - Spring Training
        ['vendor_id' => 60, 'sport_id' => 3], // Triple-A East
        ['vendor_id' => 33, 'sport_id' => 3], // Triple-A National Championship
        ['vendor_id' => 61, 'sport_id' => 3], // Triple-A West
        ['vendor_id' => 47, 'sport_id' => 3], // Olympic Games
        ['vendor_id' => 41, 'sport_id' => 3], // Pan American Games
        ['vendor_id' => 43, 'sport_id' => 3], // WBSC Premier 12
        ['vendor_id' => 70, 'sport_id' => 3], // World Baseball Classic
        ['vendor_id' => 76, 'sport_id' => 3], // World Cup Women
        ['vendor_id' => 326, 'sport_id' => 4], // Africa Champions Cup
        ['vendor_id' => 320, 'sport_id' => 4], // African Championship
        ['vendor_id' => 316, 'sport_id' => 4], // Asia Champions Cup
        ['vendor_id' => 424, 'sport_id' => 4], // Asia Champions League
        ['vendor_id' => 301, 'sport_id' => 4], // Asia Cup
        ['vendor_id' => 1, 'sport_id' => 4], // NBL
        ['vendor_id' => 4, 'sport_id' => 4], // NBL 1
        ['vendor_id' => 3, 'sport_id' => 4], // NBL W
        ['vendor_id' => 217, 'sport_id' => 4], // Superliga
        ['vendor_id' => 218, 'sport_id' => 4], // Superliga Women
        ['vendor_id' => 23, 'sport_id' => 4], // Belgian Cup
        ['vendor_id' => 24, 'sport_id' => 4], // EuroMillions Basketball League
        ['vendor_id' => 374, 'sport_id' => 4], // Pro Basketball League
        ['vendor_id' => 222, 'sport_id' => 4], // CEBL
        ['vendor_id' => 31, 'sport_id' => 4], // CBA
        ['vendor_id' => 228, 'sport_id' => 4], // 1. Liga
        ['vendor_id' => 34, 'sport_id' => 4], // Canal Digital Ligaen
        ['vendor_id' => 198, 'sport_id' => 4], // NLB
        ['vendor_id' => 365, 'sport_id' => 4], // ABA Supercup
        ['vendor_id' => 196, 'sport_id' => 4], // Baltic League
        ['vendor_id' => 197, 'sport_id' => 4], // EuroBasket
        ['vendor_id' => 199, 'sport_id' => 4], // EuroChallenge
        ['vendor_id' => 194, 'sport_id' => 4], // Eurocup
        ['vendor_id' => 360, 'sport_id' => 4], // EuroCup Women
        ['vendor_id' => 120, 'sport_id' => 4], // Euroleague
        ['vendor_id' => 359, 'sport_id' => 4], // Euroleague Women
        ['vendor_id' => 201, 'sport_id' => 4], // FIBA Europe Cup
        ['vendor_id' => 9, 'sport_id' => 4], // French Cup
        ['vendor_id' => 2, 'sport_id' => 4], // LNB
        ['vendor_id' => 133, 'sport_id' => 4], // LNB Super Cup
        ['vendor_id' => 40, 'sport_id' => 4], // BBL
        ['vendor_id' => 41, 'sport_id' => 4], // DBBL Women
        ['vendor_id' => 134, 'sport_id' => 4], // German Cup
        ['vendor_id' => 52, 'sport_id' => 4], // Lega A
        ['vendor_id' => 142, 'sport_id' => 4], // Lega A - Super Cup
        ['vendor_id' => 53, 'sport_id' => 4], // Serie A1 W
        ['vendor_id' => 242, 'sport_id' => 4], // Serie A2
        ['vendor_id' => 56, 'sport_id' => 4], // B League
        ['vendor_id' => 73, 'sport_id' => 4], // Energa Basket Liga W
        ['vendor_id' => 72, 'sport_id' => 4], // Tauron Basket Liga
        ['vendor_id' => 154, 'sport_id' => 4], // Polish Cup
        ['vendor_id' => 91, 'sport_id' => 4], // KBL
        ['vendor_id' => 266, 'sport_id' => 4], // KBL Cup
        ['vendor_id' => 413, 'sport_id' => 4], // South American Championship
        ['vendor_id' => 117, 'sport_id' => 4], // ACB
        ['vendor_id' => 95, 'sport_id' => 4], // LEB - Oro
        ['vendor_id' => 94, 'sport_id' => 4], // Liga Femenina W
        ['vendor_id' => 402, 'sport_id' => 4], // T1 League
        ['vendor_id' => 269, 'sport_id' => 4], // TBL
        ['vendor_id' => 12, 'sport_id' => 4], // NBA
        ['vendor_id' => 20, 'sport_id' => 4], // NBA - G League
        ['vendor_id' => 17, 'sport_id' => 4], // NBA - Las Vegas Summer League
        ['vendor_id' => 21, 'sport_id' => 4], // NBA - Sacramento Summer League
        ['vendor_id' => 19, 'sport_id' => 4], // NBA - Utah Summer League
        ['vendor_id' => 422, 'sport_id' => 4], // NBA Cup
        ['vendor_id' => 404, 'sport_id' => 4], // NBA In-Season Tournament
        ['vendor_id' => 176, 'sport_id' => 4], // NBA Orlando Summer League
        ['vendor_id' => 274, 'sport_id' => 4], // NBA Salt Lake City Summer League
        ['vendor_id' => 13, 'sport_id' => 4], // NBA W
        ['vendor_id' => 116, 'sport_id' => 4], // NCAA
        ['vendor_id' => 423, 'sport_id' => 4], // NCAA Women
        ['vendor_id' => 205, 'sport_id' => 4], // Friendly International
        ['vendor_id' => 290, 'sport_id' => 4], // Friendly International Women
        ['vendor_id' => 280, 'sport_id' => 4], // Intercontinental Cup
        ['vendor_id' => 192, 'sport_id' => 4], // Olympic Games
        ['vendor_id' => 193, 'sport_id' => 4], // Olympic Games Women
        ['vendor_id' => 286, 'sport_id' => 4], // Pan American Games
        ['vendor_id' => 294, 'sport_id' => 4], // Pan American Games Women
        ['vendor_id' => 281, 'sport_id' => 4], // World Cup
        ['vendor_id' => 284, 'sport_id' => 4], // World Cup Women
        ['vendor_id' => 183, 'sport_id' => 8], // African Championship
        ['vendor_id' => 184, 'sport_id' => 8], // African Championship Women
        ['vendor_id' => 198, 'sport_id' => 8], // African Games
        ['vendor_id' => 199, 'sport_id' => 8], // African Games Women
        ['vendor_id' => 186, 'sport_id' => 8], // Asian Championship
        ['vendor_id' => 185, 'sport_id' => 8], // Asian Championship Women
        ['vendor_id' => 187, 'sport_id' => 8], // Asian Games
        ['vendor_id' => 188, 'sport_id' => 8], // Asian Games Women

    ],*/

    // Custom Log Configuration
    'cleanup' => [
        'cronLogs' => [
            "keepAlive" => 14, // Days to keep cron logs
        ],
        "unusedData" => [
            'deleteAfter' => 7, // Days to keep unused odds
        ]
    ],

    // Configuration of sports
    'sports' => [
        'football' => [
            'api' => [
                'baseUrl' => 'https://v3.football.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ],
            "odds" => [
                "pagination" => true, // Enable pagination for odds
            ]
        ],
        'hockey' => [
            'api' => [
                'baseUrl' => 'https://v1.hockey.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'baseball' => [
            'api' => [
                'baseUrl' => 'https://v1.baseball.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'basketball' => [
            'api' => [
                'baseUrl' => 'https://v1.basketball.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'rugby' => [
            'api' => [
                'baseUrl' => 'https://v1.rugby.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'nfl' => [
            'api' => [
                'baseUrl' => 'https://v1.american-football.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'handball' => [
            'api' => [
                'baseUrl' => 'https://v1.handball.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ],
        'volleyball' => [
            'api' => [
                'baseUrl' => 'https://v1.volleyball.api-sports.io',
            ],
            "categories" => [
                "seasons" => [
                    "active_max_year_offset" => 1,
                ]
            ]
        ]
    ],

    // App monitor validity
    "app_monitor" => [
        "services" => [
            "SYNC_CATEGORIES" => [
                "valid_for" => 5 * 60,
            ],
            "ENABLE_DEFAULT_CATEGORIES" => [
                "valid_for" => 5 * 60,
            ],
            "SYNC_ODD_BET_TYPE" => [
                "valid_for" => 5 * 60,
            ],
            "ENABLE_AND_CONFIGURE_ODD_BET_TYPES" => [
                "valid_for" => 5 * 60,
            ],
            "SYNC_MATCHES" => [
                "valid_for" => 5 * 60,
            ],
            "SYNC_LAST_MATCHES" => [
                "valid_for" => 70,
            ],
            "SYNC_ODDS" => [
                "valid_for" => 4 * 60,
            ],
            "BETS_EVALUATE" => [
                "valid_for" => 70,
            ],
            "DELETE_OLD_DATA" => [
                "valid_for" => 25 * 60,
            ],
            "DELETE_OLD_LOGS" => [
                "valid_for" => 25 * 60 * 60 * 60,
            ],
        ]
    ],

    // Leaderboard
    'leaderboard.maxAge' => 3600, // seconds
    'leaderboard.maxUsers' => 100, // maximum users to return in the leaderboard
];
