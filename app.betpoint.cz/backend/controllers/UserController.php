<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use common\models\Rbac;
use common\models\User;
use common\models\UserSearch;
use common\utils\formatters\SbcFormatter;
use backend\models\CreateUserForm;
use backend\models\UserFundsForm;
use backend\models\ChangePasswordForm;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
				'access' => [
					'class' => \yii\filters\AccessControl::class,
					'rules' => [
						[
							'allow' => true,
							'roles' => [Rbac::PERM_USER_VIEW],
							'actions' => ['index', 'view'],
						],							
						[
							'allow' => true,
							'roles' => [Rbac::PERM_USER_CREATE],
							'actions' => ['create'],
						],					
						[
							'allow' => true,
							'roles' => [Rbac::PERM_USER_UPDATE],
							'actions' => ['update', 'update-funds', 'change-password'],
						],					
						[
							'allow' => true,
							'roles' => [Rbac::PERM_USER_DELETE],
							'actions' => ['delete'],
						],					
					],
				],	
                'verbs' => [
                    'class' => VerbFilter::class,
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }


    /**
     * @inheritDoc
     */
	public function beforeAction($action)
	{
        SbcFormatter::setDefaultUserLocale();

		if (!parent::beforeAction($action)) {
			return false;
		}
		
		return true;
	}


    /**
     * Lists all User models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = ['created_at' => SORT_DESC];

        // only show public users, not admins
        $dataProvider->query->andWhere(['user.type' => User::USER_TYPE_PUBLIC]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $user = $this->findModel($id);

        // user bets
        $betsQuery = $user->getUserBetsWithDetail()->asArray();
        $betsDataProvider = new ActiveDataProvider([
            'query' => $betsQuery,
            'sort' => [
                'defaultOrder' => [
                    'created_at' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['bets.pageSize'],
            ],
        ]);

        // user transactions
        $transactionsQuery = $user->getUserTransactionsWithDetail()->asArray();
        $transactionsDataProvider = new ActiveDataProvider([
            'query' => $transactionsQuery,
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC,
                ],
            ],
            'pagination' => [
                'pageSize' => Yii::$app->params['transactions.pageSize'],
            ],
        ]);

        return $this->render('view', [
            'model' => $user,
            'betsDataProvider' => $betsDataProvider,
            'transactionsDataProvider' => $transactionsDataProvider,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new CreateUserForm();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->signup()) {
                \Yii::$app->session->setFlash(
                    'success',
                    sprintf('User "%s" has been created successfully.', $model->user->getFriendlyName())
                );
                return $this->redirect(['index']);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $userModel = $this->findModel($id);
        $userFundsModel = new UserFundsForm();
        $changePasswordModel = new ChangePasswordForm();

        if (\Yii::$app->request->isAjax && $userModel->load($this->request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!empty($_POST['ajax'])) {
                // validation
                return \yii\widgets\ActiveForm::validate($userModel);
            }
            if ($userModel->validate() && $userModel->save()) {
                return ['success' => true, 'message' => Yii::t('app', 'user_update_account_success')];
            } else {
                return ['success' => false, 'errors' => $userModel->getErrors()];
            }
        }

        return $this->render('update', [
            'userModel' => $userModel,
            'userFundsModel' => $userFundsModel,
            'changePasswordModel' => $changePasswordModel,
        ]);
    }


    /**
     * Updates the UserFunds model for a user via AJAX.
     * @param int $id ID
     * @return array
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdateFunds()
    {
        $userFundsModel = new UserFundsForm();

        if (\Yii::$app->request->isAjax && $userFundsModel->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!empty($_POST['ajax'])) {
                // validation
                $user = $userFundsModel->getUser();
                $currentBalance = $user ? $user->getFundBalance() : 0;
                $previewBalance = $currentBalance + (float)$userFundsModel->funds_amount;

                $validation = \yii\widgets\ActiveForm::validate($userFundsModel);

                // return calculated values for preview
                $validation['preview'] = [
                    'currentBalance' => $currentBalance,
                    'currentBalanceFormatted' => Yii::$app->formatter->asCurrencyValue($currentBalance),
                    'newBalance' => $previewBalance,
                    'newBalanceFormatted' => Yii::$app->formatter->asCurrencyValue($previewBalance),
                ];

                return $validation;
            }
            if ($userFundsModel->submit()) {
                return [
                    'success' => true, 
                    'preview' => [
                        'currentBalance' => $userFundsModel->getUser()->getFundBalance(),
                        'currentBalanceFormatted' => Yii::$app->formatter->asCurrencyValue($userFundsModel->getUser()->getFundBalance()),
                    ],
                    'message' => Yii::t('app', 'user_update_balance_success'),
                ];
            } else {
                return ['success' => false, 'errors' => $userFundsModel->getErrors()];
            }
        }

        // Not an AJAX request or not a POST
        throw new \yii\web\BadRequestHttpException('Invalid request.');
    }


    /**
     * Changes the password for a user via AJAX.
     * @return array
     * @throws \yii\web\BadRequestHttpException if the request is not valid
     */
    public function actionChangePassword()
    {
        $changePasswordModel = new ChangePasswordForm();

        if (\Yii::$app->request->isAjax && $changePasswordModel->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!empty($_POST['ajax'])) {
                // validation
                return \yii\widgets\ActiveForm::validate($changePasswordModel);
            }
            if ($changePasswordModel->submit()) {
                return ['success' => true, 'message' => Yii::t('app', 'user_change_password_success')];
            } else {
                return ['success' => false, 'errors' => $changePasswordModel->getErrors()];
            }
        }

        // Not an AJAX request or not a POST
        throw new \yii\web\BadRequestHttpException('Invalid request.');
    }


    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $user = $this->findModel($id);
        if($user) {
            $user->status = User::STATUS_DELETED;  
            $user->save();
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne([
            'id' => $id,
            'type' => User::USER_TYPE_PUBLIC, // only public users
        ])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
