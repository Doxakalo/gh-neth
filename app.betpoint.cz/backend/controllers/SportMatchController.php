<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use common\models\Rbac;
use common\models\SportMatch;
use common\models\SportMatchSearch;
use common\utils\formatters\SbcFormatter;
use backend\models\SportMatchResultForm;

/**
 * SportMatchController implements the CRUD actions for SportMatch model.
 */
class SportMatchController extends Controller
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
							'roles' => [Rbac::PERM_MATCH_VIEW],
							'actions' => ['index', 'view'],
						],							
						[
							'allow' => true,
							'roles' => [Rbac::PERM_MATCH_UPDATE],
							'actions' => ['update-result'],
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
     * Lists all SportMatch models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new SportMatchSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);
        $dataProvider->sort->defaultOrder = [
            'sportName' => SORT_ASC,
            'categoryName' => SORT_ASC,
        ];

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single SportMatch model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $sportMatch = $this->findModel($id);
        if(!$sportMatch) {
            throw new NotFoundHttpException('The requested sport match does not exist.');
        }

        $currentResult = $sportMatch->getCurrentSportMatchResult();
        $sportMatchResultForm = null;
        if($sportMatch->evaluated && $currentResult) {
            $sportMatchResultForm = new SportMatchResultForm();
            $sportMatchResultForm->sport_match_result_id = $currentResult->id;
            $sportMatchResultForm->loadInitialValues();
        }

        return $this->render('view', [
            'model' => $sportMatch,
            'currentResultValues' => $currentResult ? $currentResult->getResultValues() : null,
            'sportMatchResultForm' => $sportMatchResultForm,
        ]);
    }


    /**
     * Updates the result of a SportMatch
     * @return array
     * @throws \yii\web\BadRequestHttpException if the request is not valid
     */
    public function actionUpdateResult()
    {
        $sportMatchResultForm = new SportMatchResultForm();

        if (\Yii::$app->request->isAjax && $sportMatchResultForm->load(\Yii::$app->request->post())) {
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            if (!empty($_POST['ajax'])) {
                // validation
                return \yii\widgets\ActiveForm::validate($sportMatchResultForm);
            }
            $newResult = $sportMatchResultForm->submit();
            if ($newResult) {
                return [
                    'success' => true, 
                    'message' => Yii::t('app', 'sport_match_result_form_success'),
                    'preview' => [
                        'matchResult' => $newResult->getResultValues()
                    ],
                ];
            } else {
                return ['success' => false, 'errors' => $sportMatchResultForm->getErrors()];
            }
        }

        // Not an AJAX request or not a POST
        throw new \yii\web\BadRequestHttpException('Invalid request.');
    }


    /**
     * Creates a new SportMatch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new SportMatch();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing SportMatch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SportMatch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SportMatch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return SportMatch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SportMatch::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
