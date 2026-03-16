<?php

namespace frontend\controllers;

use common\models\AppMonitor;
use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\User;
use common\models\Sport;
use common\models\SportMatch;
use common\models\UserBet;
use common\services\Leaderboard;
use frontend\models\BetForm;
use frontend\models\LoginForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use frontend\models\ComplaintForm;
use yii\data\ActiveDataProvider;

/**
 * API controller
 */
class ApiController extends Controller
{

	private $now;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
			
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => [
							'app-status',
							'signup',
							'login', 
							'logout', 
							'account', 
							'error',
							'leaderboard',
						],
                        'allow' => true,
                    ],
					[
						'actions' => [
							'sports',
							'sport-matches',
							'contact-form',
							'complaint-form',
							'create-bet',
							'bets',
							'transactions',
							'totalbet',
						],
						'roles' => ['@'],
						'allow' => true,
					],							
                ],
				'denyCallback' => function ($rule, $action) {
					// disable redirect to login page, use error response
					\Yii::$app->response->redirect(['api/error']);
				},
            ],	
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'signup' => ['post'],
                    'login' => ['post'],
                    'logout' => ['post'],
                    'account' => ['get'],
                    'sports' => ['get'],
                    'sport-matches' => ['get'],
                    'contact-form' => ['post'],
                    'complaint-form' => ['post'],
                    'bet' => ['post'],
                ],
            ],
        ];
    }


	public function beforeAction($action)
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;	
		$this->layout = null;
		$this->enableCsrfValidation = false;
		$this->now = date("Y-m-d H:i:s");

		if (!parent::beforeAction($action)) {
			return false;
		}
		
		return true;
	}


	/**
     * Generic error response
     *
     * @return mixed
     */
	public function actionError()
	{
		Yii::$app->response->statusCode = 400;

		$response = [
			'success' => FALSE,
			'message' => 'Error',
		];

		return $response;
	}


    /**
     * Signup user
     *
     * @return mixed
     */	
    public function actionSignup()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
		];

		if(!Yii::$app->user->isGuest) {
			$response['message'] = 'Already logged in';
			Yii::$app->response->statusCode = 400;
			return $response;
		}

		$model = new SignupForm();
		$model->load(['SignupForm' => Yii::$app->request->post()]);
		if ($model->validate() && $model->signup()) {
			$response['success'] = TRUE;
		} else {
			$response['message'] = 'Signup error';
			$response['errors'] = $model->errors;
			Yii::$app->response->statusCode = 400;
		}
		
		return $response;
    }


    /**
     * Login user
     *
     * @return mixed
     */	
    public function actionLogin()
	{
		$response = [
			'success' => false,
			'message' => null,
			'loginStatus' => false,
			'appStatus' => true,
		];

		if(!Yii::$app->user->isGuest) {
			$response['message'] = 'Already logged in';
			$response['loginStatus'] = true;
			Yii::$app->response->statusCode = 400;
			return $response;
		}

		$model = new LoginForm();
		$model->load(['LoginForm' => Yii::$app->request->post()]);
		if ($model->validate() && $model->login()) {
			$response['success'] = true;
			$response['loginStatus'] = true;

			// Zjisti stav aplikace, ale neblokuj login
			if(!AppMonitor::getAppStatusUserLevel()){
				$response['message'] = Yii::t('app', 'login_form_app_status_error');
				$response['appStatus'] = false;
			}
		} else {
			$response['message'] = 'Incorrect login';
			$response['errors'] = $model->errors;
			Yii::$app->response->statusCode = 401;
		}

		return $response;
	}

	
	
    /**
     * Logout user
     *
     * @return mixed
     */
    public function actionLogout()
    {
		$response = [
			'success' => false,
			'message' => null,
			'loginStatus' => false,
		];

		if(Yii::$app->user->isGuest) {
			$response['message'] = 'Not logged in';
			Yii::$app->response->statusCode = 400;
			return $response;
		}

		Yii::$app->user->logout();
		$response['success'] = true;
		
		return $response;
    }


    /**
     * Get user account
     *
     * @return mixed
     */
	public function actionAccount()
	{
		$response = [
			'success' => false,
			'message' => null,
			'loginStatus' => false,
			'profile' => null,
			'wallet' => null,
		];

		/** @var User|null $user */
		$user = !Yii::$app->user->isGuest ? User::getCurrent() : null;

		if ($user && $user->isPublicUser()) {
			$response['success'] = true;
			$response['loginStatus'] = true;
			$response['profile'] = $user->getProfile();
			$response['wallet'] = $user->getWallet();

			// update last active tim
			$user->updateLastActive();
		}

		return $response;
	}


    /**
     * Get app status
     *
     * @return mixed
     */
	public function actionAppStatus()
	{
		$appStatus = AppMonitor::getAppStatusUserLevel();
		$response = [
			'status' => $appStatus,
			'message' => !$appStatus ? Yii::t('app', 'app_status_error_generic') : '',
		];
		return $response;
	}


    /**
     * Get sports and categories
     *
     * @return mixed
     */
	public function actionSports()
	{
		$response = [
			'success' => false,
			'message' => null,
			'sports' => [],
		];

		$sports = Sport::getAllSportsWithCategories();
		
		if (count($sports) > 0) {
			$response['success'] = true;
			$response['sports'] = $sports;
		} else {
			$response['message'] = 'No sports found';
		}

		return $response;
	}


    /**
     * Get sport matches
     *
	 * @param int $categoryId
     * @return mixed
     */
	public function actionSportMatches(int $categoryId)
	{
		$response = [
			'success' => false,
			'message' => null,
			'matches' => [],
		];
	 
		$matchesArr = SportMatch::getFutureMatchesWithOdds($categoryId);

		if (count($matchesArr) > 0) {
			$response['success'] = true;
			$response['matches'] = $matchesArr;
		} else {
			$response['message'] = 'No matches found';
		}

		return $response;
	}


    /**
     * Place a bet
     *
     * @return mixed
     */	
    public function actionCreateBet()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
		];

		$model = new BetForm();
		$model->load(['BetForm' => Yii::$app->request->post()]);
		if ($model->validate() && $model->submit()) {
			$response['success'] = TRUE;
		} else {
			$response['message'] = 'Bet form error';
			$response['errors'] = $model->errors;
			Yii::$app->response->statusCode = 400;
		}
		
		return $response;
    }


    /**
     * List user Transactions
     *
     * @return mixed
     */	
    public function actionTransactions()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
			'transactions' => [],
		];

		$user = User::getCurrent();
		if ($user) {
			// pagination params
			$page = max(0, (intval(Yii::$app->request->get('page', 1))) - 1); // zero-based index
			$limit = intval(Yii::$app->request->get('limit'));
		 	$pageSize = $limit > 0 ? min($limit, Yii::$app->params['transactions.pageSize']) : Yii::$app->params['transactions.pageSize'];

			$query = $user->getUserTransactionsWithDetail()->asArray();
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => $page,
				],
				'sort' => [
					'defaultOrder' => [
						'id' => SORT_DESC,
					],
				],
			]);

			// output processed data
			$response['transactions'] = $dataProvider->getModels();

			// pagination data
			$pagination = $dataProvider->getPagination();
			$response['pagination'] = [
				'total_count' => $dataProvider->getTotalCount(),
				'page_count' => $pagination->getPageCount(),
				'current_page' => $pagination->getPage() + 1, // One-based index for current page
				'page_size' => $pagination->getPageSize(),
			];

			$response['success'] = true;
		}
		
		return $response;
    }


    /**
     * List user Bets
     *
     * @return mixed
     */	
    public function actionBets()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
			'bets' => [],
		];

		$user = User::getCurrent();
		if ($user) {
			// pagination params
			$page = max(0, (intval(Yii::$app->request->get('page', 1))) - 1); // zero-based index
			$limit = intval(Yii::$app->request->get('limit'));
		 	$pageSize = $limit > 0 ? min($limit, Yii::$app->params['bets.pageSize']) : Yii::$app->params['bets.pageSize'];

			$query = $user->getUserBetsWithDetail()->asArray();
			$dataProvider = new ActiveDataProvider([
				'query' => $query,
				'pagination' => [
					'pageSize' => $pageSize,
					'page' => $page,
				],
				'sort' => [
					'defaultOrder' => [
						'created_at' => SORT_DESC,
					],
				],
			]);

			// output processed data
			$response['bets'] = $dataProvider->getModels();

			// pagination data
			$pagination = $dataProvider->getPagination();
			$response['pagination'] = [
				'total_count' => $dataProvider->getTotalCount(),
				'page_count' => $pagination->getPageCount(),
				'current_page' => $pagination->getPage() + 1, // One-based index for current page
				'page_size' => $pagination->getPageSize(),
			];

			$response['success'] = true;
		}
		
		return $response;
    }

	public function actionTotalbet()
	{
		$user = User::getCurrent();
		$response = [
			'success' => false,
			'message' => null,
			'total_amount' => 0,
		];

		if ($user) {
			$total = UserBet::find()
				->where(['user_id' => $user->id]) 
				->andWhere(['status' => 0])
				->sum('amount'); 

			$response['total_amount'] = $total ?: 0;
			$response['success'] = true;
		}

		return $response;
	}
    /**
     * Submit contact form
     *
     * @return mixed
     */	
    public function actionContactForm()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
		];

		$model = new ContactForm();
		$model->load(['ContactForm' => Yii::$app->request->post()]);
		if ($model->validate() && $model->submit()) {
			$response['success'] = TRUE;
		} else {
			$response['message'] = 'Contact form error';
			$response['errors'] = $model->errors;
			Yii::$app->response->statusCode = 400;
		}
		
		return $response;
    }


    /**
     * Submit complaint form
     *
     * @return mixed
     */	
    public function actionComplaintForm()
    {
		$response = [
			'success' => FALSE,
			'message' => NULL,
		];

		$model = new ComplaintForm();
		$model->load(['ComplaintForm' => Yii::$app->request->post()]);
		if ($model->validate() && $model->submit()) {
			$response['success'] = TRUE;
		} else {
			$response['message'] = 'Complaint form error';
			$response['errors'] = $model->errors;
			Yii::$app->response->statusCode = 400;
		}
		
		return $response;
    }


	/**
	 * Get leaderboard, cached
	 * 
     * @return mixed
	 */
	public function actionLeaderboard() {
		$leaderboard = Leaderboard::getLeaderboard();
		return $leaderboard;
	}

	
}
